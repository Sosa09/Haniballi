<?php

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'patient_id',
        'user_id',
        'scheduled_at',
        'duration_minutes',
        'type',
        'status',
        'notes',
        'video_room_url',
        'video_room_token',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function videoSession(): HasOne
    {
        return $this->hasOne(VideoSession::class);
    }

    public function videoCallSignals(): HasMany
    {
        return $this->hasMany(VideoCallSignal::class);
    }
}
