<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\VideoCallSignal;
use App\Models\VideoSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebRtcSignalingController extends Controller
{
    /**
     * Post a WebRTC signaling packet (offer, answer, candidate, chat, status, leave).
     */
    public function postSignal(Request $request, int $appointmentId): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:offer,answer,candidate,chat,status,leave',
            'payload' => 'required',
            'client_id' => 'nullable|string',
        ]);

        $appointment = Appointment::findOrFail($appointmentId);
        $user = Auth::user();
        $senderRole = $user->role === 'doctor' ? 'doctor' : 'patient';

        $payload = is_string($validated['payload'])
            ? $validated['payload']
            : json_encode($validated['payload']);

        $signal = VideoCallSignal::create([
            'appointment_id' => $appointment->id,
            'sender_id' => $user->id,
            'client_id' => $validated['client_id'] ?? null,
            'sender_role' => $senderRole,
            'type' => $validated['type'],
            'payload' => $payload,
        ]);

        // If offer or answer is sent, activate session
        if (in_array($validated['type'], ['offer', 'answer'])) {
            VideoSession::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'room_name' => 'appointment_'.$appointment->id,
                    'room_url' => route('video.room', $appointment->id),
                    'provider' => 'daily',
                    'status' => 'active',
                    'started_at' => now(),
                ]
            );
        }

        if ($validated['type'] === 'leave') {
            $appointment->videoSession?->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'signal_id' => $signal->id,
        ]);
    }

    /**
     * Poll for incoming signals sent by the other peer.
     */
    public function getSignals(Request $request, int $appointmentId): JsonResponse
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $user = Auth::user();
        $lastId = (int) $request->query('last_id', 0);
        $clientId = $request->query('client_id');

        // Housekeeping: clean up signals older than 2 minutes
        if ($lastId === 0) {
            VideoCallSignal::where('appointment_id', $appointment->id)
                ->where('created_at', '<', now()->subMinutes(2))
                ->delete();
        }

        $query = VideoCallSignal::where('appointment_id', $appointment->id)
            ->where('id', '>', $lastId);

        if ($lastId === 0) {
            $query->where('created_at', '>=', now()->subSeconds(15));
        }

        if (! empty($clientId)) {
            $query->where(function ($q) use ($clientId, $user): void {
                $q->where('client_id', '!=', $clientId)
                    ->orWhere(function ($sub) use ($user): void {
                        $sub->whereNull('client_id')->where('sender_id', '!=', $user->id);
                    });
            });
        } else {
            $query->where('sender_id', '!=', $user->id);
        }

        $signals = $query->orderBy('id', 'asc')->get();

        $formattedSignals = $signals->map(function (VideoCallSignal $signal): array {
            $decoded = json_decode($signal->payload, true);

            return [
                'id' => $signal->id,
                'sender_id' => $signal->sender_id,
                'client_id' => $signal->client_id,
                'sender_role' => $signal->sender_role,
                'type' => $signal->type,
                'payload' => $decoded ?? $signal->payload,
                'created_at' => $signal->created_at->toISOString(),
            ];
        });

        $maxId = $signals->max('id') ?? $lastId;

        // Real-time peer presence via cache heartbeat
        $peerOnline = false;
        if (! empty($clientId)) {
            $cacheKey = "room_presence_{$appointment->id}";
            $presence = cache()->get($cacheKey, []);
            $presence[$clientId] = [
                'user_id' => $user->id,
                'role' => $user->role,
                'last_seen' => now()->timestamp,
            ];
            // Prune sessions not heard from in 6 seconds
            $presence = array_filter($presence, fn ($c) => (now()->timestamp - $c['last_seen']) <= 6);
            cache()->put($cacheKey, $presence, 60);

            foreach ($presence as $cid => $info) {
                if ($cid !== $clientId && $info['user_id'] !== $user->id) {
                    $peerOnline = true;
                    break;
                }
            }
        } else {
            $peerOnline = VideoCallSignal::where('appointment_id', $appointment->id)
                ->where('created_at', '>=', now()->subSeconds(15))
                ->where('sender_id', '!=', $user->id)
                ->exists();
        }

        return response()->json([
            'signals' => $formattedSignals,
            'last_id' => $maxId,
            'peer_online' => $peerOnline,
        ]);
    }

    /**
     * Save live clinical consultation notes.
     */
    public function saveNotes(Request $request, int $appointmentId): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update([
            'notes' => $validated['notes'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Clinical notes saved successfully',
        ]);
    }

    /**
     * End consultation session.
     */
    public function leaveRoom(Request $request, int $appointmentId): JsonResponse
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $user = Auth::user();
        $senderRole = $user->role === 'doctor' ? 'doctor' : 'patient';

        VideoCallSignal::create([
            'appointment_id' => $appointment->id,
            'sender_id' => $user->id,
            'sender_role' => $senderRole,
            'type' => 'leave',
            'payload' => json_encode(['reason' => 'User disconnected']),
        ]);

        $clientId = $request->input('client_id');
        if (! empty($clientId)) {
            $cacheKey = "room_presence_{$appointment->id}";
            $presence = cache()->get($cacheKey, []);
            unset($presence[$clientId]);
            cache()->put($cacheKey, $presence, 60);
        }

        $appointment->videoSession?->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
