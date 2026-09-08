<?php

use App\Models\Appointment;
use App\Models\ExerciseProgram;
use App\Models\NutritionPlan;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Patient Dashboard')] class extends Component
{
    public Collection $upcomingAppointments;

    public Collection $activePlans;

    public Collection $activeExercisePrograms;

    public function mount(): void
    {
        $patient = auth()->user()?->patient;

        if (! $patient) {
            $this->upcomingAppointments = new Collection;
            $this->activePlans = new Collection;
            $this->activeExercisePrograms = new Collection;

            return;
        }

        $this->upcomingAppointments = Appointment::where('patient_id', $patient->id)
            ->where('scheduled_at', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $this->activePlans = NutritionPlan::where('patient_id', $patient->id)
            ->where('status', 'active')
            ->orderByDesc('start_date')
            ->limit(3)
            ->get();

        $this->activeExercisePrograms = ExerciseProgram::where('patient_id', $patient->id)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
    }
};
?>

<div class="space-y-8 max-w-7xl mx-auto">
    {{-- Clinical Dossier Header --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03),0_10px_20px_-5px_rgba(0,0,0,0.02)] p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/70 uppercase tracking-wider">
                        Active Patient Dossier
                    </span>
                    <span class="text-xs text-slate-400">Dr. Mehdi Haniballi</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">
                    Welcome, {{ auth()->user()->name }}
                </h2>
                <p class="text-xs text-slate-500 max-w-xl font-normal">
                    Protocol Phase: Metabolic Restoration & Insulin Normalization · Next evaluation in 4 days.
                </p>
            </div>

            {{-- Quick Anthropometric Indicators --}}
            <div class="grid grid-cols-3 gap-3 bg-slate-50 border border-slate-200/70 p-3.5 rounded-xl text-center self-start md:self-auto">
                <div class="px-2">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Weight</span>
                    <span class="text-base font-bold text-slate-800">68.5 <span class="text-[10px] font-normal text-slate-500">kg</span></span>
                </div>
                <div class="px-2 border-x border-slate-200">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">BMI</span>
                    <span class="text-base font-bold text-emerald-700">22.4</span>
                </div>
                <div class="px-2">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Target</span>
                    <span class="text-base font-bold text-slate-800">1,750 <span class="text-[10px] font-normal text-slate-500">kcal</span></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Three Action Pillars --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Telehealth Consultations --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                        {{ $upcomingAppointments->count() }} Scheduled
                    </span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Telehealth Video Consultations</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Encrypted remote clinical sessions with Dr. Haniballi.</p>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('appointments.index') }}" class="text-xs font-bold text-slate-700 hover:text-emerald-700 transition">View Calendar</a>
                <a href="{{ route('video.room', ['appointmentId' => 1]) }}" class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition flex items-center gap-1">
                    <span>Enter Studio</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Clinical Nutrition Plan --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                        Active Protocol
                    </span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Nutritional Regimen</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Macro-calibrated meal timing and anti-inflammatory guidelines.</p>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800">1,750 kcal/day</span>
                <a href="{{ route('nutrition.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 transition">
                    <span>Inspect Meals</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Kinetic Stretching & Burn Studio --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-mono">
                        ~110 kcal / day
                    </span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Kinetic Stretching & Mobility</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Real anatomical animations with live MET energy burn tracking.</p>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500 font-medium">4 Active Modules</span>
                <a href="{{ route('training.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1">
                    <span>Launch Studio</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Upcoming Consultations & Clinical Protocol Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Appointments Schedule --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Upcoming Appointments</h3>
                <a href="{{ route('appointments.index') }}" class="text-xs font-bold text-emerald-700 hover:underline">Schedule New</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($upcomingAppointments as $appointment)
                    <div class="py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                                @if ($appointment->type === 'video')
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">
                                    {{ $appointment->type === 'video' ? 'Remote Video Consultation' : 'In-Clinic Assessment' }}
                                </p>
                                <p class="text-[11px] text-slate-500 font-mono">
                                    {{ $appointment->scheduled_at->format('l, M j · H:i') }} ({{ $appointment->duration_minutes }}m)
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $appointment->status === 'confirmed' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' : 'bg-amber-50 text-amber-800 border border-amber-200/60' }}">
                                {{ $appointment->status }}
                            </span>
                            @if ($appointment->type === 'video')
                                <a href="{{ route('video.room', ['appointmentId' => $appointment->id]) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 transition">
                                    Join
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-xs">No pending appointments on file.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Clinical Objectives & Directives --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Physiological Directives</h3>
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Dr. Haniballi</span>
            </div>
            <div class="space-y-3">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1">
                    <p class="text-xs font-bold text-slate-800">1. Postprandial Mobility Timing</p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Execute the 10-minute Post-Meal Glycemic protocol within 30 minutes following dinner to blunt the glucose excursion.
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1">
                    <p class="text-xs font-bold text-slate-800">2. Hydration Baseline</p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Minimum 2.5L filtered water daily with electrolyte pinch to maintain cellular hydration during caloric restriction.
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1">
                    <p class="text-xs font-bold text-slate-800">3. Thoracic Extension Holds</p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Incorporate the Cat-Cow decompression routine every morning to alleviate cervical-lumbar nerve compression.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>