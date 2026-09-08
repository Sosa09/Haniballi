<?php

use App\Models\ExerciseProgram;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kinetic Rehabilitation & Mobility - Dr. Mehdi Haniballi')] class extends Component
{
    public string $selectedCategory = 'all';
    public ?int $activeRoutineId = null;
    public int $currentExerciseIndex = 0;
    public float $patientWeight = 70.0; // kg

    public function selectCategory(string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function startRoutine(int $routineId): void
    {
        $this->activeRoutineId = $routineId;
        $this->currentExerciseIndex = 0;
    }

    public function closeRoutine(): void
    {
        $this->activeRoutineId = null;
    }
};
?>

<div class="space-y-8 max-w-7xl mx-auto" x-data="{
    activeTab: 'catalog', // 'catalog' | 'player' | 'calculator'
    activeExercise: 0,
    patientWeight: 70,
    timer: 45,
    initialTimer: 45,
    interval: null,
    running: false,
    elapsedTotalSeconds: 0,
    liveCaloriesBurned: 0.0,
    activeMet: 3.2,
    playbackSpeed: 1, // 0.75, 1, 1.25
    viewMode: 'realistic3d', // 'realistic3d' | 'biomechanical'

    exercises: [
        {
            id: 0,
            title: 'Cat-Cow Thoracic Mobilization',
            subtitle: 'Spinal Disc Rehydration & Segmental Articulation',
            category: 'mobility',
            met: 3.0,
            duration: 45,
            caloriesEstimated: 24,
            intensity: 'Light Clinical',
            musclesPrimary: 'Erector Spinae, Multifidus',
            musclesSecondary: 'Rectus Abdominis, Serratus Anterior',
            animationType: 'catcow',
            image: '/images/exercises/catcow.jpg',
            doctorNote: 'Initiate movement strictly from the pelvis before articulating through thoracic vertebrae. Avoid excessive cervical extension.',
            cues: [
                'Inhale: Drop belly gently, draw collarbones wide, tilt pelvis anteriorly (Cow).',
                'Exhale: Press floor away, round spine toward ceiling, tuck chin and tailbone (Cat).',
                'Maintain constant knee-hip 90° angle throughout the oscillation cycle.'
            ]
        },
        {
            id: 1,
            title: 'Primal Deep Squat & Hip Capsule Opener',
            subtitle: 'Ankle Dorsiflexion & Pelvic Floor Relaxation',
            category: 'stretching',
            met: 3.8,
            duration: 60,
            caloriesEstimated: 38,
            intensity: 'Moderate Mobility',
            musclesPrimary: 'Adductors, Gluteus Medius, Soleus',
            musclesSecondary: 'Thoracic Extensors, Tibialis Anterior',
            animationType: 'squathold',
            image: '/images/exercises/squat.jpg',
            doctorNote: 'Crucial for countering 8+ hours of sitting. Restores femoral head centralization within the acetabulum.',
            cues: [
                'Feet slightly wider than shoulder-width, toes turned outward 15-20°.',
                'Descend slowly into bottom position, keeping heels firmly rooted on the floor.',
                'Gently drive elbows against inner knees while elevating the sternum.'
            ]
        },
        {
            id: 2,
            title: 'Suboccipital & Upper Trapezius Release',
            subtitle: 'Cervicogenic Tension Relief & Desk Posture Alignment',
            category: 'posture',
            met: 2.5,
            duration: 40,
            caloriesEstimated: 16,
            intensity: 'Restorative',
            musclesPrimary: 'Upper Trapezius, Levator Scapulae',
            musclesSecondary: 'Sternocleidomastoid, Scalenes',
            animationType: 'necktilt',
            image: '/images/exercises/necktilt.jpg',
            doctorNote: 'Relieves chronic tension headaches caused by forward head posture and monitor gaze angle.',
            cues: [
                'Drop shoulders away from ears; depress the contralateral clavicle.',
                'Allow the ear to gravitate toward the shoulder without twisting the jaw.',
                'Apply feather-light overpressure using the fingertips; never pull or jerk.'
            ]
        },
        {
            id: 3,
            title: 'Supine Glute Bridge & Posterior Recoupling',
            subtitle: 'Reciprocal Inhibition of Tight Hip Flexors',
            category: 'activation',
            met: 3.5,
            duration: 50,
            caloriesEstimated: 32,
            intensity: 'Low Impact Strength',
            musclesPrimary: 'Gluteus Maximus, Biceps Femoris',
            musclesSecondary: 'Transverse Abdominis, Quadratus Lumborum',
            animationType: 'bridge',
            image: '/images/exercises/bridge.jpg',
            doctorNote: 'Re-activates inhibited gluteal motor units while promoting passive psoas lengthening at peak contraction.',
            cues: [
                'Lie flat, knees bent at 90°, feet hip-width apart and parallel.',
                'Posteriorly tilt pelvis to flatten lumbar curve before driving through heels.',
                'Hold at apex for 2 seconds with max glute engagement before controlled descent.'
            ]
        }
    ],

    get current() {
        return this.exercises[this.activeExercise];
    },

    startTimer() {
        if (this.running) return;
        this.running = true;
        this.interval = setInterval(() => {
            if (this.timer > 0) {
                this.timer--;
                this.elapsedTotalSeconds++;
                // Real MET Calorie formula: (MET * 3.5 * weightKg / 200) / 60 per second
                let kcalPerSec = (this.current.met * 3.5 * this.patientWeight) / (200 * 60);
                this.liveCaloriesBurned += kcalPerSec;
            } else {
                this.pauseTimer();
                this.nextExerciseAuto();
            }
        }, 1000);
    },

    pauseTimer() {
        this.running = false;
        clearInterval(this.interval);
    },

    resetTimer() {
        this.pauseTimer();
        this.timer = this.current.duration;
        this.initialTimer = this.current.duration;
    },

    setExercise(index) {
        this.activeExercise = index;
        this.resetTimer();
    },

    nextExerciseAuto() {
        if (this.activeExercise < this.exercises.length - 1) {
            this.setExercise(this.activeExercise + 1);
            this.startTimer();
        }
    },

    calculateBurn(met, minutes, weight) {
        return Math.round(((met * 3.5 * weight) / 200) * minutes);
    }
}">
    {{-- Inline CSS for Real Anatomical Vector Animations --}}
    <style>
        /* ==========================================================================
           REAL ANATOMICAL BIOMECHANICAL ANIMATIONS (Dr. Mehdi Haniballi Kinetic Lab)
           ========================================================================== */

        /* 1. Cat-Cow Anatomical Fluid Spine & Articulation */
        @keyframes catCowHeadAnatomy {
            0%, 100% { transform: translate(0px, -7px) rotate(-13deg); }
            50% { transform: translate(0px, 15px) rotate(20deg); }
        }
        @keyframes catCowPelvisAnatomy {
            0%, 100% { transform: rotate(-8deg); }
            50% { transform: rotate(10deg); }
        }
        @keyframes catCowSpineBackArc {
            0%, 100% { transform: translateY(12px) scaleY(0.78); }
            50% { transform: translateY(-16px) scaleY(1.22); }
        }
        @keyframes catCowBellySag {
            0%, 100% { transform: translateY(14px) scaleY(1.25); opacity: 0.95; }
            50% { transform: translateY(-14px) scaleY(0.75); opacity: 0.55; }
        }
        @keyframes catCowDiscPulse {
            0%, 100% { fill: #10b981; filter: drop-shadow(0 0 6px #10b981); }
            50% { fill: #38bdf8; filter: drop-shadow(0 0 6px #38bdf8); }
        }

        /* 2. Primal Deep Squat Full Body Sinking & Abduction */
        @keyframes squatFullBodySink {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(72px); }
        }
        @keyframes squatFemurLeftAnatomy {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(48deg); }
        }
        @keyframes squatFemurRightAnatomy {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(-48deg); }
        }
        @keyframes squatTibiaLeftAnatomy {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(-24deg); }
        }
        @keyframes squatTibiaRightAnatomy {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(24deg); }
        }
        @keyframes squatPelvicHalo {
            0%, 100% { opacity: 0.15; transform: scale(0.9); }
            50% { opacity: 0.9; transform: scale(1.18); }
        }

        /* 3. Cervical Spine & Upper Trapezius Release */
        @keyframes neckHeadTiltAnatomy {
            0%, 100% { transform: rotate(0deg); }
            22%, 46% { transform: rotate(-24deg); }
            50% { transform: rotate(0deg); }
            72%, 96% { transform: rotate(24deg); }
        }
        @keyframes neckGuidingArmAnatomy {
            0%, 100% { transform: rotate(0deg); }
            22%, 46% { transform: rotate(-20deg) translate(-2px, -3px); }
            50% { transform: rotate(0deg); }
            72%, 96% { transform: rotate(20deg) translate(2px, -3px); }
        }
        @keyframes myofascialWaveFlow {
            0% { stroke-dashoffset: 48; opacity: 0.3; }
            50% { stroke-dashoffset: 0; opacity: 1; }
            100% { stroke-dashoffset: -48; opacity: 0.3; }
        }

        /* 4. Supine Glute Bridge Concentric Apex Hold */
        @keyframes bridgeAnatomicalDrive {
            0%, 100% { transform: translateY(0px); }
            38%, 66% { transform: translateY(-58px); }
        }
        @keyframes bridgeGluteMaxApex {
            0%, 100% { opacity: 0.25; fill: #1e293b; stroke: #334155; }
            38%, 66% { opacity: 1; fill: #064e3b; stroke: #10b981; filter: drop-shadow(0 0 12px #10b981); }
        }
        @keyframes bridgeKineticAxisPulse {
            0%, 100% { opacity: 0.15; stroke-dashoffset: 20; }
            38%, 66% { opacity: 1; stroke-dashoffset: 0; }
        }

        /* Cadence Breathing Pulse */
        @keyframes breathCyclePulse {
            0%, 100% { transform: scale(1); opacity: 0.45; }
            50% { transform: scale(1.35); opacity: 0.95; }
        }

        .animate-catcow-head-real { animation: catCowHeadAnatomy 6s ease-in-out infinite; transform-origin: 105px 125px; will-change: transform; }
        .animate-catcow-pelvis-real { animation: catCowPelvisAnatomy 6s ease-in-out infinite; transform-origin: 245px 200px; will-change: transform; }
        .animate-catcow-spine-arc { animation: catCowSpineBackArc 6s ease-in-out infinite; transform-origin: 170px 140px; will-change: transform; }
        .animate-catcow-belly-sag { animation: catCowBellySag 6s ease-in-out infinite; transform-origin: 170px 165px; will-change: transform; }
        .animate-catcow-disc { animation: catCowDiscPulse 6s ease-in-out infinite; }

        .animate-squat-sink-real { animation: squatFullBodySink 5s cubic-bezier(0.45, 0, 0.55, 1) infinite; will-change: transform; }
        .animate-squat-femur-l-real { animation: squatFemurLeftAnatomy 5s cubic-bezier(0.45, 0, 0.55, 1) infinite; transform-origin: 130px 170px; will-change: transform; }
        .animate-squat-femur-r-real { animation: squatFemurRightAnatomy 5s cubic-bezier(0.45, 0, 0.55, 1) infinite; transform-origin: 250px 170px; will-change: transform; }
        .animate-squat-tibia-l-real { animation: squatTibiaLeftAnatomy 5s cubic-bezier(0.45, 0, 0.55, 1) infinite; transform-origin: 105px 248px; will-change: transform; }
        .animate-squat-tibia-r-real { animation: squatTibiaRightAnatomy 5s cubic-bezier(0.45, 0, 0.55, 1) infinite; transform-origin: 275px 248px; will-change: transform; }
        .animate-squat-capsule-halo { animation: squatPelvicHalo 5s cubic-bezier(0.45, 0, 0.55, 1) infinite; transform-origin: 190px 205px; }

        .animate-neck-head-real { animation: neckHeadTiltAnatomy 8s cubic-bezier(0.4, 0, 0.2, 1) infinite; transform-origin: 190px 165px; will-change: transform; }
        .animate-neck-guiding-arm { animation: neckGuidingArmAnatomy 8s cubic-bezier(0.4, 0, 0.2, 1) infinite; transform-origin: 190px 165px; will-change: transform; }
        .animate-myofascial-wave { animation: myofascialWaveFlow 2.5s linear infinite; }

        .animate-bridge-drive-real { animation: bridgeAnatomicalDrive 5s cubic-bezier(0.45, 0, 0.55, 1) infinite; will-change: transform; }
        .animate-bridge-glute-real { animation: bridgeGluteMaxApex 5s cubic-bezier(0.45, 0, 0.55, 1) infinite; will-change: opacity, fill; }
        .animate-bridge-axis-real { animation: bridgeKineticAxisPulse 5s cubic-bezier(0.45, 0, 0.55, 1) infinite; }
        .animate-breath-ring { animation: breathCyclePulse 6s ease-in-out infinite; }
    </style>

    {{-- Top Clinic Header (Refined, Editorial & Authoritative) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03),0_10px_20px_-5px_rgba(0,0,0,0.02)] p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70 tracking-wide uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Therapeutic Stretching & Kinesiology Protocol
                    </span>
                    <span class="text-xs text-slate-400 font-medium">Cabinet Dr. Mehdi Haniballi</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">
                    Therapeutic Stretching & Active Mobility
                </h1>
                <p class="text-sm text-slate-600 max-w-2xl font-normal leading-relaxed">
                    Prescribed therapeutic stretching regimens and kinetic drills designed to counter sedentary anterior compressive load, re-establish myofascial glide, and enhance insulin-mediated glucose disposal.
                </p>
            </div>

            {{-- Live Clinical Metrics Summary --}}
            <div class="flex items-center gap-3 self-start md:self-auto bg-slate-50 border border-slate-200/70 rounded-xl p-3.5">
                <div class="text-left px-2">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Patient Weight</span>
                    <div class="flex items-baseline gap-1 mt-0.5">
                        <span class="text-lg font-bold text-slate-800" x-text="patientWeight">70</span>
                        <span class="text-xs text-slate-500 font-medium">kg</span>
                    </div>
                </div>
                <div class="h-8 w-px bg-slate-200"></div>
                <div class="text-left px-2">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Estimated MET</span>
                    <div class="flex items-baseline gap-1 mt-0.5">
                        <span class="text-lg font-bold text-emerald-700" x-text="current.met.toFixed(1)">3.0</span>
                        <span class="text-xs text-slate-500 font-medium">Score</span>
                    </div>
                </div>
                <div class="h-8 w-px bg-slate-200"></div>
                <div class="text-left px-2">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Calorie Burn Rate</span>
                    <div class="flex items-baseline gap-1 mt-0.5">
                        <span class="text-lg font-bold text-slate-900" x-text="((current.met * 3.5 * patientWeight / 200)).toFixed(1)">3.7</span>
                        <span class="text-xs text-slate-500 font-medium">kcal/min</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sub-Navigation Tab Bar --}}
        <div class="flex items-center justify-between pt-4">
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/60">
                <button
                    @click="activeTab = 'catalog'"
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition"
                    :class="activeTab === 'catalog' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                    Protocol Catalog (4 Modules)
                </button>
                <button
                    @click="activeTab = 'player'"
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
                    :class="activeTab === 'player' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                    <span class="w-2 h-2 rounded-full bg-emerald-500" :class="running ? 'animate-ping' : ''"></span>
                    Guided Clinical Studio
                </button>
                <button
                    @click="activeTab = 'calculator'"
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition"
                    :class="activeTab === 'calculator' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                    Metabolic Burn Calculator
                </button>
            </div>

            <div class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-500">
                <span>Protocol ID: #KINE-2026-MED</span>
            </div>
        </div>
    </div>

    {{-- VIEW 1: GUIDED CLINICAL STUDIO (ANIMATED EXERCISE VIEWER) --}}
    <div x-show="activeTab === 'player'" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Main Interactive Movement Canvas (7 Cols) --}}
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
                <div>
                    {{-- Movement Header --}}
                    <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700" x-text="'Phase ' + (activeExercise + 1) + ' of ' + exercises.length"></span>
                                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/60" x-text="current.intensity"></span>
                            </div>
                            <h2 class="text-xl md:text-2xl font-bold text-slate-900 mt-1" x-text="current.title"></h2>
                            <p class="text-xs text-slate-500 font-normal mt-0.5" x-text="current.subtitle"></p>
                        </div>

                        {{-- Mode Selector & Real-time Live Calorie Accumulator Badge --}}
                        <div class="flex flex-wrap items-center gap-3 justify-end">
                            <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
                                <button
                                    @click="viewMode = 'realistic3d'"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                                    :class="viewMode === 'realistic3d' ? 'bg-white text-emerald-800 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="hidden sm:inline">Realistic 3D Athlete</span>
                                    <span class="sm:hidden">3D</span>
                                </button>
                                <button
                                    @click="viewMode = 'biomechanical'"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                                    :class="viewMode === 'biomechanical' ? 'bg-white text-emerald-800 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span class="hidden sm:inline">X-Ray Vector</span>
                                    <span class="sm:hidden">X-Ray</span>
                                </button>
                            </div>

                            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-3 text-right shadow-md">
                                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold block">Live Burn</span>
                                <div class="flex items-baseline justify-end gap-1 mt-0.5">
                                    <span class="text-2xl font-mono font-bold text-emerald-400" x-text="liveCaloriesBurned.toFixed(2)">0.00</span>
                                    <span class="text-xs text-slate-300 font-semibold">kcal</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ANIMATED CLINICAL STAGE --}}
                    <div class="relative bg-slate-950 rounded-xl overflow-hidden my-6 aspect-[16/10] flex items-center justify-center border border-slate-800 shadow-inner">
                        {{-- Background Grid & Calibrated Gridlines --}}
                        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:28px_28px] opacity-35 pointer-events-none z-10"></div>

                        {{-- STAGE 1: REALISTIC 3D HUMAN ATHLETE (Photo-Realistic 3D Character Studio) --}}
                        <div x-show="viewMode === 'realistic3d'" class="relative w-full h-full flex items-center justify-center overflow-hidden">
                            <img
                                :src="current.image"
                                :alt="current.title"
                                class="w-full h-full object-cover object-center select-none transition-transform duration-700 hover:scale-102"
                            />

                            {{-- Studio Lighting Depth Mask --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-transparent to-slate-950/40 pointer-events-none"></div>

                            {{-- Breathing & Respiratory Pacer Indicator (Top Center) --}}
                            <div class="absolute top-4 left-1/2 -translate-x-1/2 bg-slate-900/90 backdrop-blur-md px-4 py-1.5 rounded-full border border-emerald-500/40 text-xs font-semibold text-white shadow-xl flex items-center gap-2 z-20">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400" :class="running ? 'animate-ping' : ''"></span>
                                <span x-text="running ? (timer % 8 < 4 ? '🫁 Deep Inhale · Decompress Spine (4s)' : '💨 Controlled Exhale · Contract Core (4s)') : 'Ready · Press Start Movement'"></span>
                            </div>

                            {{-- Active Target Muscles Badge (Bottom Left) --}}
                            <div class="absolute bottom-4 left-4 flex items-center gap-2 bg-slate-900/90 backdrop-blur-md px-3.5 py-2 rounded-xl border border-slate-700/80 text-white text-xs shadow-xl z-20">
                                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block leading-none">Target Motor Group</span>
                                    <span class="font-bold text-emerald-300 text-xs mt-0.5 block" x-text="current.musclesPrimary"></span>
                                </div>
                            </div>

                            {{-- Real-time Kinetic Rate (Bottom Right) --}}
                            <div class="absolute bottom-4 right-4 flex items-center gap-2 bg-slate-900/90 backdrop-blur-md px-3.5 py-2 rounded-xl border border-slate-700/80 text-white text-xs shadow-xl z-20">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block leading-none">Kinetic Rate</span>
                                    <span class="font-mono font-bold text-amber-300 text-xs mt-0.5 block" x-text="((current.met * 3.5 * patientWeight) / (200 * 60)).toFixed(2) + ' kcal/s'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- STAGE 2: X-RAY BIOMECHANICAL VECTOR STAGE --}}
                        <div x-show="viewMode === 'biomechanical'" class="relative w-full h-full flex items-center justify-center">

                        {{-- ANIMATION 1: Cat-Cow Thoracic Spine (Realistic Anatomical Quadruped Human) --}}
                        <div x-show="current.animationType === 'catcow'" class="relative w-full h-full flex items-center justify-center">
                            <svg viewBox="0 0 380 260" class="w-full h-full max-w-[440px]" preserveAspectRatio="xMidYMid meet">
                                <defs>
                                    <linearGradient id="catcowMuscle" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#334155"/>
                                        <stop offset="60%" stop-color="#1e293b"/>
                                        <stop offset="100%" stop-color="#0f172a"/>
                                    </linearGradient>
                                    <linearGradient id="matGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#0f172a"/>
                                        <stop offset="50%" stop-color="#1e293b"/>
                                        <stop offset="100%" stop-color="#0f172a"/>
                                    </linearGradient>
                                    <filter id="glowG" x="-20%" y="-20%" width="140%" height="140%">
                                        <feGaussianBlur stdDeviation="3" result="blur"/>
                                        <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                                    </filter>
                                </defs>

                                {{-- Clinical Exercise Mat --}}
                                <rect x="40" y="210" width="300" height="10" rx="4" fill="url(#matGrad)" stroke="#334155" stroke-width="1.5"/>
                                <line x1="40" y1="210" x2="340" y2="210" stroke="#10b981" stroke-width="2"/>
                                <line x1="100" y1="210" x2="100" y2="220" stroke="#059669" stroke-width="1.5"/>
                                <line x1="245" y1="210" x2="245" y2="220" stroke="#059669" stroke-width="1.5"/>

                                {{-- STATIONARY SUPPORT ELEMENTS (Knees, Shins, Feet & Hands) --}}
                                {{-- Planted Foot & Ankle --}}
                                <path d="M 285 200 C 292 200 305 204 316 208 C 318 210 314 212 308 212 C 295 212 285 211 280 206 Z" fill="#475569" stroke="#64748b" stroke-width="1.5"/>
                                {{-- Lower Leg / Shin resting on mat --}}
                                <path d="M 240 208 C 240 200 248 194 265 194 C 275 194 282 196 288 200 C 285 208 270 210 240 210 Z" fill="url(#catcowMuscle)" stroke="#475569" stroke-width="1.5"/>

                                {{-- Support Arms & Hands flat on mat --}}
                                <path d="M 82 210 C 82 206 90 205 105 205 C 114 205 118 208 118 210 Z" fill="#64748b" stroke="#94a3b8" stroke-width="1.5"/>
                                {{-- Muscular Forearm (Radius / Ulna) --}}
                                <path d="M 94 206 C 92 185 91 168 93 152 C 98 150 108 150 110 152 C 109 168 107 185 104 206 Z" fill="url(#catcowMuscle)" stroke="#475569" stroke-width="1.5"/>
                                <circle cx="102" cy="150" r="3" fill="#10b981"/>
                                {{-- Muscular Upper Arm (Triceps / Deltoid base) --}}
                                <path d="M 93 152 C 91 140 92 130 96 122 C 102 118 112 118 114 124 C 113 134 111 142 110 152 Z" fill="url(#catcowMuscle)" stroke="#475569" stroke-width="1.5"/>
                                <circle cx="105" cy="120" r="4.5" fill="#10b981" filter="url(#glowG)"/>

                                {{-- DYNAMIC PELVIS & THIGH ASSEMBLY (Articulates at knee) --}}
                                <g class="animate-catcow-pelvis-real">
                                    {{-- Muscular Thigh connecting knee to hip --}}
                                    <path d="M 235 206 C 248 190 252 165 246 142 C 240 128 226 128 218 135 C 215 155 220 185 235 206 Z" fill="url(#catcowMuscle)" stroke="#475569" stroke-width="1.8"/>
                                    {{-- Athletic Gluteus Maximus & Sacral Contour --}}
                                    <path d="M 226 128 C 242 124 255 132 254 148 C 252 162 245 174 238 184 C 234 172 232 155 226 140 Z" fill="#1e293b" stroke="#059669" stroke-width="1.5"/>
                                    <circle cx="228" cy="138" r="4.5" fill="#10b981" filter="url(#glowG)"/>
                                    <line x1="230" y1="130" x2="255" y2="124" stroke="#34d399" stroke-width="2" stroke-dasharray="3 2"/>
                                </g>

                                {{-- DYNAMIC ARTICULATING TORSO & ABDOMINAL WALL --}}
                                <g class="animate-catcow-spine-arc">
                                    <path d="M 105 120 C 145 130 190 130 226 130 C 220 148 190 156 150 156 C 120 156 108 142 105 120 Z" fill="url(#catcowMuscle)" stroke="#334155" stroke-width="1.5"/>
                                    {{-- Sagittal Vertebral Kinetic Cord --}}
                                    <path d="M 105 122 Q 165 138 228 132" fill="none" stroke="#10b981" stroke-width="5" stroke-linecap="round" filter="url(#glowG)"/>
                                    <circle class="animate-catcow-disc" cx="120" cy="126" r="3.5" fill="#10b981"/>
                                    <circle class="animate-catcow-disc" cx="140" cy="130" r="3.5" fill="#ffffff"/>
                                    <circle class="animate-catcow-disc" cx="165" cy="133" r="4.5" fill="#34d399"/>
                                    <circle class="animate-catcow-disc" cx="190" cy="133" r="3.5" fill="#ffffff"/>
                                    <circle class="animate-catcow-disc" cx="212" cy="130" r="3.5" fill="#10b981"/>
                                </g>

                                {{-- DYNAMIC CORE DEFORMATION --}}
                                <g class="animate-catcow-belly-sag">
                                    <path d="M 112 142 C 145 160 185 160 218 145" fill="none" stroke="#059669" stroke-width="2.5" stroke-dasharray="5 3"/>
                                </g>

                                {{-- DYNAMIC ATHLETIC HEAD & CERVICAL COLUMN --}}
                                <g class="animate-catcow-head-real">
                                    <path d="M 106 122 C 98 112 92 104 88 94 C 95 90 102 96 108 108 Z" fill="url(#catcowMuscle)" stroke="#475569" stroke-width="1.5"/>
                                    {{-- Anatomical Cranium, Forehead, Nose Profile, Lips & Sculpted Jawline --}}
                                    <path d="M 88 94 C 84 94 76 90 73 84 C 70 76 72 65 80 58 C 90 50 104 52 110 62 C 114 68 114 78 109 86 C 104 92 98 94 88 94 Z" fill="#334155" stroke="#64748b" stroke-width="1.5"/>
                                    <path d="M 75 72 C 68 74 65 77 62 82 C 65 84 70 85 75 85 C 78 88 80 91 85 91" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/>
                                    <ellipse cx="94" cy="74" rx="3.5" ry="5.5" fill="#1e293b" stroke="#64748b" stroke-width="1"/>
                                    <path d="M 88 92 C 94 100 100 110 106 120" fill="none" stroke="#34d399" stroke-width="2" stroke-dasharray="2 2"/>
                                </g>

                                {{-- Biomechanical Overlay --}}
                                <text x="190" y="28" text-anchor="middle" fill="#34d399" font-size="11" font-weight="700" letter-spacing="1.2">
                                    SAGITTAL SPINAL ARTICULATION · CAT-COW (6s)
                                </text>
                                <rect x="50" y="40" width="105" height="20" rx="6" fill="#0f172a" fill-opacity="0.8" stroke="#334155" stroke-width="1"/>
                                <text x="102" y="54" text-anchor="middle" fill="#94a3b8" font-size="9" font-mono="true">C1-L5 SEGMENTAL</text>
                            </svg>
                        </div>

                        {{-- ANIMATION 2: Primal Squat & Pelvic Opener (Realistic Anatomical Squatting Human) --}}
                        <div x-show="current.animationType === 'squathold'" class="relative w-full h-full flex items-center justify-center">
                            <svg viewBox="0 0 380 280" class="w-full h-full max-w-[400px]" preserveAspectRatio="xMidYMid meet">
                                <defs>
                                    <linearGradient id="squatMuscle" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#334155"/>
                                        <stop offset="50%" stop-color="#1e293b"/>
                                        <stop offset="100%" stop-color="#0f172a"/>
                                    </linearGradient>
                                    <linearGradient id="squatTeal" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#10b981"/>
                                        <stop offset="100%" stop-color="#047857"/>
                                    </linearGradient>
                                </defs>

                                {{-- Calibrated Floor Baseline with Stance Width Grid --}}
                                <line x1="30" y1="250" x2="350" y2="250" stroke="#334155" stroke-width="2" stroke-dasharray="5 5"/>
                                <line x1="105" y1="248" x2="105" y2="256" stroke="#10b981" stroke-width="2"/>
                                <line x1="275" y1="248" x2="275" y2="256" stroke="#10b981" stroke-width="2"/>
                                <text x="190" y="268" text-anchor="middle" fill="#64748b" font-size="9" font-mono="true">ANTERIOR STANCE WIDTH · 1.2x BIACROMIAL</text>

                                {{-- FIXED GROUNDED FEET (Turned slightly outward ~18°) --}}
                                <path d="M 85 248 C 85 242 98 238 114 238 C 124 238 128 244 126 248 Z" fill="#475569" stroke="#64748b" stroke-width="1.8"/>
                                <path d="M 295 248 C 295 242 282 238 266 238 C 256 238 252 244 254 248 Z" fill="#475569" stroke="#64748b" stroke-width="1.8"/>

                                {{-- Ground Reaction Force Arrows under heels --}}
                                <path d="M 105 258 L 105 248 M 102 252 L 105 248 L 108 252" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                <path d="M 275 258 L 275 248 M 272 252 L 275 248 L 278 252" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>

                                {{-- SHINS / CALVES (Ankle Dorsiflexion Assembly) --}}
                                <g class="animate-squat-tibia-l-real">
                                    <path d="M 105 248 C 98 220 94 195 98 172 C 106 172 118 174 116 195 C 114 218 112 240 108 248 Z" fill="url(#squatMuscle)" stroke="#475569" stroke-width="1.8"/>
                                    <circle cx="106" cy="172" r="5" fill="#10b981"/>
                                </g>
                                <g class="animate-squat-tibia-r-real">
                                    <path d="M 275 248 C 282 220 286 195 282 172 C 274 172 262 174 264 195 C 266 218 268 240 272 248 Z" fill="url(#squatMuscle)" stroke="#475569" stroke-width="1.8"/>
                                    <circle cx="274" cy="172" r="5" fill="#10b981"/>
                                </g>

                                {{-- DESCENDING KINETIC ASSEMBLY (Femurs, Pelvis, Torso, Arms, Head) --}}
                                <g class="animate-squat-sink-real">
                                    {{-- Left Femur & Quads --}}
                                    <g class="animate-squat-femur-l-real">
                                        <path d="M 106 172 C 118 165 142 155 165 150 C 160 162 145 174 125 182 C 112 184 106 178 106 172 Z" fill="url(#squatMuscle)" stroke="#059669" stroke-width="1.8"/>
                                    </g>
                                    {{-- Right Femur & Quads --}}
                                    <g class="animate-squat-femur-r-real">
                                        <path d="M 274 172 C 262 165 238 155 215 150 C 220 162 235 174 255 182 C 268 184 274 178 274 172 Z" fill="url(#squatMuscle)" stroke="#059669" stroke-width="1.8"/>
                                    </g>

                                    {{-- Pelvic Floor Capsule Decompression Halo --}}
                                    <circle class="animate-squat-capsule-halo" cx="190" cy="155" r="28" fill="url(#squatTeal)" opacity="0.3"/>
                                    <ellipse cx="190" cy="155" rx="22" ry="14" fill="#047857" stroke="#10b981" stroke-width="2"/>

                                    {{-- Upright Athletic Torso --}}
                                    <path d="M 160 152 C 152 135 146 112 142 90 C 158 84 222 84 238 90 C 234 112 228 135 220 152 Z" fill="url(#squatMuscle)" stroke="#334155" stroke-width="2"/>
                                    <path d="M 152 92 C 165 106 182 108 190 108 C 198 108 215 106 228 92" fill="none" stroke="#475569" stroke-width="2"/>
                                    <line x1="190" y1="108" x2="190" y2="148" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="175" y1="124" x2="205" y2="124" stroke="#475569" stroke-width="1.5"/>
                                    <line x1="178" y1="138" x2="202" y2="138" stroke="#475569" stroke-width="1.5"/>

                                    {{-- Deltoids & Shoulders --}}
                                    <ellipse cx="144" cy="90" rx="9" ry="12" fill="#334155" stroke="#64748b" stroke-width="1.5"/>
                                    <ellipse cx="236" cy="90" rx="9" ry="12" fill="#334155" stroke="#64748b" stroke-width="1.5"/>

                                    {{-- Arms in Prayer Position (Elbows wedged against inner knees to open hips) --}}
                                    <path d="M 144 92 C 132 108 126 128 122 148 C 138 142 165 130 185 116 Z" fill="url(#squatMuscle)" stroke="#475569" stroke-width="1.5"/>
                                    <path d="M 236 92 C 248 108 254 128 258 148 C 242 142 215 130 195 116 Z" fill="url(#squatMuscle)" stroke="#475569" stroke-width="1.5"/>
                                    <ellipse cx="190" cy="114" rx="8" ry="12" fill="#e2e8f0" stroke="#94a3b8" stroke-width="1.5"/>
                                    <circle cx="124" cy="148" r="4.5" fill="#10b981"/>
                                    <circle cx="256" cy="148" r="4.5" fill="#10b981"/>
                                    <path d="M 124 148 L 110 148 M 115 144 L 110 148 L 115 152" stroke="#34d399" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M 256 148 L 270 148 M 265 144 L 270 148 L 265 152" stroke="#34d399" stroke-width="2" stroke-linecap="round"/>

                                    {{-- Athletic Head & Cervical Posture --}}
                                    <path d="M 182 86 C 182 78 184 68 184 60 C 187 60 193 60 196 60 C 196 68 198 78 198 86 Z" fill="url(#squatMuscle)" stroke="#475569" stroke-width="1.5"/>
                                    <ellipse cx="190" cy="46" rx="15" ry="19" fill="#334155" stroke="#64748b" stroke-width="1.8"/>
                                    <circle cx="185" cy="45" r="2.5" fill="#0f172a"/>
                                    <circle cx="195" cy="45" r="2.5" fill="#0f172a"/>
                                    <path d="M 186 54 Q 190 56 194 54" stroke="#64748b" stroke-width="1.5" fill="none"/>
                                </g>

                                {{-- Biomechanical Overlay --}}
                                <text x="190" y="24" text-anchor="middle" fill="#34d399" font-size="11" font-weight="700" letter-spacing="1.2">
                                    DEEP ACETABULAR & SACRAL DECOMPRESSION (5s)
                                </text>
                                <rect x="250" y="36" width="105" height="20" rx="6" fill="#0f172a" fill-opacity="0.8" stroke="#334155" stroke-width="1"/>
                                <text x="302" y="50" text-anchor="middle" fill="#10b981" font-size="9" font-mono="true">HIP FLEXION: 42°</text>
                            </svg>
                        </div>

                        {{-- ANIMATION 3: Cervical Spine & Scalene Tilt (Realistic Anatomical Upper Body Silhouette) --}}
                        <div x-show="current.animationType === 'necktilt'" class="relative w-full h-full flex items-center justify-center">
                            <svg viewBox="0 0 380 270" class="w-full h-full max-w-[400px]" preserveAspectRatio="xMidYMid meet">
                                <defs>
                                    <linearGradient id="bustMuscle" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#334155"/>
                                        <stop offset="60%" stop-color="#1e293b"/>
                                        <stop offset="100%" stop-color="#0f172a"/>
                                    </linearGradient>
                                    <linearGradient id="tensionStream" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#f59e0b"/>
                                        <stop offset="50%" stop-color="#10b981"/>
                                        <stop offset="100%" stop-color="#059669"/>
                                    </linearGradient>
                                </defs>

                                {{-- Posture Reference Gridlines --}}
                                <line x1="40" y1="175" x2="340" y2="175" stroke="#334155" stroke-width="1.5" stroke-dasharray="4 4"/>
                                <line x1="190" y1="30" x2="190" y2="250" stroke="#1e293b" stroke-width="1.5" stroke-dasharray="2 4"/>

                                {{-- FIXED BASE TORSO & DEPRESSED SHOULDER GIRDLE --}}
                                <path d="M 100 250 C 95 210 90 185 110 174 C 135 162 165 160 190 160 C 215 160 245 162 270 174 C 290 185 285 210 280 250 Z" fill="url(#bustMuscle)" stroke="#334155" stroke-width="2"/>

                                {{-- Anatomical Clavicle Bones with Suprasternal Notch --}}
                                <path d="M 118 174 C 140 172 170 176 186 182" fill="none" stroke="#64748b" stroke-width="3" stroke-linecap="round"/>
                                <path d="M 262 174 C 240 172 210 176 194 182" fill="none" stroke="#64748b" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="190" cy="182" r="3.5" fill="#94a3b8"/>

                                {{-- Deltoid Muscle Silhouettes --}}
                                <ellipse cx="105" cy="182" rx="16" ry="22" fill="#1e293b" stroke="#475569" stroke-width="1.5"/>
                                <ellipse cx="275" cy="182" rx="16" ry="22" fill="#1e293b" stroke="#475569" stroke-width="1.5"/>

                                {{-- Contralateral Left Shoulder Anchor (Actively Depressed Downward) --}}
                                <path d="M 105 212 L 105 228 M 101 224 L 105 228 L 109 224" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round"/>
                                <text x="105" y="244" text-anchor="middle" fill="#f59e0b" font-size="8.5" font-bold="true">DEPRESSED</text>

                                {{-- DYNAMIC ACTIVE MYOFASCIAL TENSION WAVE --}}
                                <path class="animate-myofascial-wave" d="M 240 174 Q 215 130 198 106" fill="none" stroke="url(#tensionStream)" stroke-width="4" stroke-linecap="round" stroke-dasharray="8 5"/>
                                <path class="animate-myofascial-wave" d="M 140 174 Q 165 130 182 106" fill="none" stroke="url(#tensionStream)" stroke-width="4" stroke-linecap="round" stroke-dasharray="8 5"/>

                                {{-- ARTICULATING CERVICAL COLUMN, HEAD & GUIDING ARM --}}
                                <g class="animate-neck-head-real">
                                    {{-- Muscular Neck Column --}}
                                    <path d="M 174 165 C 172 145 174 125 176 110 C 184 108 196 108 204 110 C 206 125 208 145 206 165 Z" fill="url(#bustMuscle)" stroke="#475569" stroke-width="1.8"/>
                                    <line x1="190" y1="165" x2="190" y2="112" stroke="#10b981" stroke-width="4" stroke-linecap="round"/>

                                    {{-- Anatomical Cranium, Jawline, Brow & Ear Contour --}}
                                    <path d="M 190 40 C 208 40 220 54 220 74 C 220 96 208 112 190 114 C 172 112 160 96 160 74 C 160 54 172 40 190 40 Z" fill="#334155" stroke="#64748b" stroke-width="2"/>
                                    <ellipse cx="163" cy="76" rx="4" ry="7" fill="#1e293b" stroke="#64748b" stroke-width="1"/>
                                    <ellipse cx="217" cy="76" rx="4" ry="7" fill="#1e293b" stroke="#64748b" stroke-width="1"/>
                                    <path d="M 178 72 Q 183 75 188 72" stroke="#94a3b8" stroke-width="1.5" fill="none"/>
                                    <path d="M 192 72 Q 197 75 202 72" stroke="#94a3b8" stroke-width="1.5" fill="none"/>
                                    <path d="M 186 92 Q 190 94 194 92" stroke="#64748b" stroke-width="1.5" fill="none"/>

                                    {{-- Suboccipital Release Focal Node --}}
                                    <circle cx="190" cy="106" r="4.5" fill="#f59e0b"/>
                                    <path d="M 175 42 A 30 30 0 0 1 205 42" fill="none" stroke="#38bdf8" stroke-width="1.5" stroke-dasharray="2 2"/>
                                </g>

                                {{-- TACTILE GUIDING ARM --}}
                                <g class="animate-neck-guiding-arm">
                                    <path d="M 270 174 C 285 130 270 70 240 45 C 220 28 190 26 172 38" fill="none" stroke="url(#bustMuscle)" stroke-width="16" stroke-linecap="round"/>
                                    <path d="M 270 174 C 285 130 270 70 240 45 C 220 28 190 26 172 38" fill="none" stroke="#64748b" stroke-width="1.5" stroke-linecap="round"/>
                                    <ellipse cx="168" cy="42" rx="7" ry="5" fill="#cbd5e1" stroke="#94a3b8" stroke-width="1"/>
                                    <circle cx="168" cy="42" r="2.5" fill="#10b981"/>
                                </g>

                                {{-- Biomechanical Overlay --}}
                                <text x="190" y="24" text-anchor="middle" fill="#f59e0b" font-size="11" font-weight="700" letter-spacing="1.2">
                                    CERVICOTHORACIC & LEVATOR MYOFASCIAL RELEASE (8s)
                                </text>
                                <rect x="250" y="36" width="105" height="20" rx="6" fill="#0f172a" fill-opacity="0.8" stroke="#334155" stroke-width="1"/>
                                <text x="302" y="50" text-anchor="middle" fill="#f59e0b" font-size="9" font-mono="true">LATERAL TILT: 22°</text>
                            </svg>
                        </div>

                        {{-- ANIMATION 4: Supine Glute Bridge (Realistic Anatomical Bridge Human) --}}
                        <div x-show="current.animationType === 'bridge'" class="relative w-full h-full flex items-center justify-center">
                            <svg viewBox="0 0 380 260" class="w-full h-full max-w-[440px]" preserveAspectRatio="xMidYMid meet">
                                <defs>
                                    <linearGradient id="bridgeMuscle" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#334155"/>
                                        <stop offset="60%" stop-color="#1e293b"/>
                                        <stop offset="100%" stop-color="#0f172a"/>
                                    </linearGradient>
                                    <filter id="glowBridge" x="-20%" y="-20%" width="140%" height="140%">
                                        <feGaussianBlur stdDeviation="3.5" result="blur"/>
                                        <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                                    </filter>
                                </defs>

                                {{-- Clinical Mat Baseline --}}
                                <rect x="40" y="210" width="300" height="10" rx="4" fill="#1e293b" stroke="#334155" stroke-width="1.5"/>
                                <line x1="40" y1="210" x2="340" y2="210" stroke="#10b981" stroke-width="2"/>
                                <line x1="95" y1="210" x2="95" y2="220" stroke="#059669" stroke-width="1.5"/>
                                <line x1="280" y1="210" x2="280" y2="220" stroke="#059669" stroke-width="1.5"/>

                                {{-- FIXED GROUNDED UPPER BODY --}}
                                <rect x="295" y="200" width="40" height="10" rx="3" fill="#0f172a" stroke="#334155" stroke-width="1.5"/>
                                <path d="M 300 200 C 298 185 308 172 322 172 C 335 172 342 185 340 200 Z" fill="#334155" stroke="#64748b" stroke-width="1.8"/>
                                <ellipse cx="320" cy="186" rx="3.5" ry="5" fill="#1e293b" stroke="#64748b" stroke-width="1"/>
                                <path d="M 260 208 C 260 196 270 194 298 196 C 298 206 285 208 260 208 Z" fill="url(#bridgeMuscle)" stroke="#475569" stroke-width="1.8"/>
                                <circle cx="270" cy="200" r="5" fill="#10b981" filter="url(#glowBridge)"/>

                                {{-- Grounded Arms Extended Along Mat --}}
                                <path d="M 210 208 C 210 204 235 202 265 202 C 265 208 245 210 210 210 Z" fill="#475569" stroke="#64748b" stroke-width="1.5"/>
                                <ellipse cx="205" cy="208" rx="7" ry="3" fill="#94a3b8"/>

                                {{-- FIXED GROUNDED FEET (90° knee bend) --}}
                                <path d="M 80 208 C 80 202 92 198 112 198 C 118 198 122 204 120 208 Z" fill="#475569" stroke="#64748b" stroke-width="1.8"/>
                                <path d="M 96 202 C 92 185 92 165 98 145 C 105 145 116 148 114 170 C 112 188 110 200 106 202 Z" fill="url(#bridgeMuscle)" stroke="#475569" stroke-width="1.8"/>
                                <circle cx="106" cy="145" r="5" fill="#10b981" filter="url(#glowBridge)"/>

                                {{-- ELEVATING PELVIS, THIGHS & KINETIC POSTERIOR CHAIN --}}
                                <g class="animate-bridge-drive-real">
                                    <path d="M 106 145 C 128 146 155 152 182 160 C 178 174 154 172 130 166 C 114 162 106 152 106 145 Z" fill="url(#bridgeMuscle)" stroke="#334155" stroke-width="1.8"/>
                                    <path d="M 182 160 C 205 174 235 188 262 198 C 255 204 225 192 196 178 C 185 172 182 166 182 160 Z" fill="url(#bridgeMuscle)" stroke="#334155" stroke-width="1.8"/>

                                    {{-- Gluteus Maximus Apex Contraction Halo --}}
                                    <ellipse class="animate-bridge-glute-real" cx="184" cy="166" rx="20" ry="16" stroke-width="2"/>
                                    <circle cx="184" cy="162" r="5" fill="#34d399" filter="url(#glowBridge)"/>

                                    {{-- Posterior Chain Torque Drive Vector Arrow --}}
                                    <path d="M 184 190 L 184 172 M 179 177 L 184 172 L 189 177" stroke="#34d399" stroke-width="2.5" stroke-linecap="round"/>
                                </g>

                                {{-- DYNAMIC KINETIC ALIGNMENT AXIS --}}
                                <line class="animate-bridge-axis-real" x1="270" y1="200" x2="106" y2="145" stroke="#10b981" stroke-width="2.5" stroke-dasharray="6 4" filter="url(#glowBridge)"/>

                                {{-- Biomechanical Overlay --}}
                                <text x="190" y="24" text-anchor="middle" fill="#34d399" font-size="11" font-weight="700" letter-spacing="1.2">
                                    POSTERIOR RECOUPLING · TERMINAL GLUTE EXTENSION (5s)
                                </text>
                                <rect x="235" y="36" width="125" height="20" rx="6" fill="#0f172a" fill-opacity="0.8" stroke="#334155" stroke-width="1"/>
                                <text x="297" y="50" text-anchor="middle" fill="#10b981" font-size="9" font-mono="true">180° HIP ALIGNMENT AXIS</text>
                            </svg>
                        </div>
                    </div>

                    {{-- Floating Cadence Pacer Overlay --}}
                    <div class="absolute bottom-3 left-3 bg-slate-900/90 backdrop-blur-md px-3 py-1.5 rounded-lg border border-slate-700/60 flex items-center gap-2 z-20">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-breath-ring"></span>
                        <span class="text-[11px] font-mono text-slate-300">Paced: 4s Inhale · 4s Exhale</span>
                    </div>
                </div>

                    {{-- Clinical Coaching Cues --}}
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/80">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 uppercase tracking-wide mb-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Biomechanical Directives
                        </div>
                        <ul class="text-xs text-slate-600 space-y-1.5 list-none">
                            <template x-for="(cue, idx) in current.cues" :key="idx">
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-600 font-bold">•</span>
                                    <span x-text="cue"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                {{-- Player Controls Bar --}}
                <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        {{-- Prev Exercise --}}
                        <button
                            @click="if(activeExercise > 0) setExercise(activeExercise - 1)"
                            :disabled="activeExercise === 0"
                            class="p-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        {{-- Play / Pause Primary Button --}}
                        <button
                            x-show="!running"
                            @click="startTimer()"
                            class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl font-bold text-sm shadow-md shadow-emerald-700/20 transition flex items-center gap-2">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <span>Start Movement</span>
                        </button>
                        <button
                            x-show="running"
                            @click="pauseTimer()"
                            class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-sm shadow-md shadow-amber-600/20 transition flex items-center gap-2">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                            <span>Pause Sequence</span>
                        </button>

                        {{-- Reset --}}
                        <button
                            @click="resetTimer()"
                            class="p-2.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition"
                            title="Reset Timer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>

                    {{-- Time Remaining Ring / Digit --}}
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Hold Duration</span>
                            <div class="text-2xl font-black font-mono text-slate-900 leading-none mt-0.5" x-text="timer + 's'">45s</div>
                        </div>
                        <button
                            @click="if(activeExercise < exercises.length - 1) setExercise(activeExercise + 1)"
                            :disabled="activeExercise === exercises.length - 1"
                            class="p-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Column: Physiological Impact & Sequence List (5 Cols) --}}
            <div class="lg:col-span-5 space-y-6">
                {{-- Dr. Haniballi's Clinical Rationale --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center font-bold text-emerald-800 text-sm">
                            MH
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Dr. Mehdi Haniballi</h3>
                            <p class="text-xs text-slate-500">Clinical Protocol Rationale</p>
                        </div>
                    </div>
                    <blockquote class="text-xs text-slate-600 leading-relaxed italic border-l-2 border-emerald-600 pl-3 py-1 bg-emerald-50/40 rounded-r-lg" x-text="'“' + current.doctorNote + '”'">
                    </blockquote>

                    {{-- Targeted Muscle Activation Matrix --}}
                    <div class="pt-3 border-t border-slate-100 space-y-2.5">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Target Myofascial Groups</span>
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-700">Primary:</span>
                                <span class="font-semibold text-emerald-800" x-text="current.musclesPrimary"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-700">Secondary:</span>
                                <span class="font-medium text-slate-500" x-text="current.musclesSecondary"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Protocol Queue Selector --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Prescribed Movement Order</h3>
                    <div class="space-y-2">
                        <template x-for="(ex, index) in exercises" :key="ex.id">
                            <div
                                @click="setExercise(index)"
                                class="p-3 rounded-xl border transition cursor-pointer flex items-center justify-between"
                                :class="activeExercise === index ? 'bg-emerald-50/80 border-emerald-300 ring-1 ring-emerald-300' : 'bg-slate-50/70 border-slate-200/80 hover:bg-slate-100/70'">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-slate-200 bg-slate-900">
                                        <img :src="ex.image" :alt="ex.title" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900" x-text="ex.title"></p>
                                        <p class="text-[10px] text-slate-500" x-text="ex.duration + 's · MET ' + ex.met"></p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-slate-700" x-text="'~' + calculateBurn(ex.met, ex.duration/60, patientWeight) + ' kcal'"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- VIEW 2: PROTOCOL CATALOG (ALL EXERCISES) --}}
    <div x-show="activeTab === 'catalog'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <template x-for="(ex, index) in exercises" :key="ex.id">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition p-6 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="relative aspect-[16/9] rounded-xl overflow-hidden bg-slate-900 border border-slate-100 shadow-inner group">
                            <img :src="ex.image" :alt="ex.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                            <div class="absolute bottom-2.5 left-3 flex items-center gap-1.5 text-[11px] text-white font-medium">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span x-text="ex.intensity"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700" x-text="ex.category"></span>
                            <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-200/80" x-text="'MET ' + ex.met"></span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900" x-text="ex.title"></h3>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="ex.subtitle"></p>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed" x-text="ex.doctorNote"></p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400">Energy Expenditure</span>
                            <p class="text-sm font-black text-slate-900" x-text="'~' + calculateBurn(ex.met, ex.duration/60, patientWeight) + ' kcal per set'"></p>
                        </div>
                        <button
                            @click="activeExercise = index; activeTab = 'player'; resetTimer(); startTimer();"
                            class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <span>Open in Studio</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- VIEW 3: CLINICAL METABOLIC BURN CALCULATOR --}}
    <div x-show="activeTab === 'calculator'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 max-w-3xl mx-auto space-y-8">
        <div class="space-y-1">
            <span class="text-[11px] uppercase font-bold text-emerald-700 tracking-wider">AHA & ACSM Standardized Formula</span>
            <h2 class="text-2xl font-bold text-slate-900">Personalized Movement Energy Expenditure</h2>
            <p class="text-xs text-slate-500">
                Caloric burn is calculated dynamically using the clinical Metabolic Equivalent of Task formula:
                <code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-800 font-mono text-[11px]">(MET × 3.5 × Weight [kg] / 200) × Time [min]</code>
            </p>
        </div>

        <div class="space-y-6 bg-slate-50 p-6 rounded-xl border border-slate-200/80">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Adjust Patient Body Mass</label>
                    <span class="text-base font-black text-slate-900 font-mono" x-text="patientWeight + ' kg'">70 kg</span>
                </div>
                <input
                    type="range"
                    min="45"
                    max="140"
                    step="1"
                    x-model="patientWeight"
                    class="w-full accent-emerald-600 h-2 bg-slate-200 rounded-lg cursor-pointer">
                <div class="flex justify-between text-[10px] text-slate-400 font-medium mt-1">
                    <span>45 kg</span>
                    <span>70 kg (Reference)</span>
                    <span>140 kg</span>
                </div>
            </div>

            {{-- Dynamic Calculation Output Table --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-200">
                <div class="bg-white p-4 rounded-xl border border-slate-200 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Full 20-Min Routine</span>
                    <p class="text-2xl font-black text-emerald-700 font-mono mt-1" x-text="calculateBurn(3.2, 20, patientWeight) + ' kcal'"></p>
                    <span class="text-[10px] text-slate-500">Daily baseline protocol</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Weekly Total (4x)</span>
                    <p class="text-2xl font-black text-slate-900 font-mono mt-1" x-text="(calculateBurn(3.2, 20, patientWeight) * 4) + ' kcal'"></p>
                    <span class="text-[10px] text-slate-500">80 active minutes</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Post-Meal EPOC Boost</span>
                    <p class="text-2xl font-black text-amber-600 font-mono mt-1" x-text="'+' + Math.round(calculateBurn(3.2, 20, patientWeight) * 0.15) + ' kcal'"></p>
                    <span class="text-[10px] text-slate-500">Excess post-exercise oxygen</span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button
                @click="activeTab = 'player'; setExercise(0); startTimer();"
                class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                Apply Weight & Launch Studio →
            </button>
        </div>
    </div>
</div>