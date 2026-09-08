<!DOCTYPE html>
<html lang="en" class="scroll-smooth bg-white antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cabinet Médical & Nutritionnel Dr. Mehdi Haniballi - Clinical nutrition, metabolic optimization, and functional movement kinesiology.">
    <title>Dr. Mehdi Haniballi - Clinical Nutrition & Functional Health</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .appear { opacity: 0; transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1); transform: translateY(24px); }
        .appear.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans selection:bg-emerald-100 selection:text-emerald-900">

    {{-- Top Utility Bar --}}
    <div class="bg-slate-950 text-slate-400 text-xs py-2 border-b border-slate-800 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1 text-emerald-400 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Telehealth Consultations Open
                </span>
                <span class="hidden sm:inline text-slate-600">|</span>
                <span class="hidden sm:inline text-slate-400">Cabinet Médical · Casablanca & Consultation à Distance</span>
            </div>
            <div class="flex items-center gap-4 font-mono text-[11px]">
                <a href="tel:+212522000000" class="hover:text-slate-200 transition">+212 (0) 522 00 00 00</a>
                <span class="text-slate-700">·</span>
                <a href="{{ route('login') }}" class="hover:text-emerald-400 text-slate-300 font-semibold transition">Patient Portal →</a>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-700 to-teal-900 flex items-center justify-center text-white shadow-md shadow-emerald-900/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block">Cabinet Médical</span>
                        <span class="text-base font-bold text-slate-900 tracking-tight">Dr. Mehdi Haniballi</span>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-8 text-xs font-semibold text-slate-600">
                    <a href="#clinical-scope" class="hover:text-emerald-700 transition">Clinical Scope</a>
                    <a href="#specialties" class="hover:text-emerald-700 transition">Medical Protocols</a>
                    <a href="#pathway" class="hover:text-emerald-700 transition">Patient Pathway</a>
                    <a href="#credentials" class="hover:text-emerald-700 transition">Credentials</a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-700 hover:text-emerald-700 px-3.5 py-2 rounded-xl transition hover:bg-slate-50">
                        Sign In
                    </a>
                    <a href="{{ route('appointments.index') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition shadow-sm shadow-emerald-700/20">
                        Book Consultation
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section (Subtle, Authoritative & Clinical) --}}
    <section class="relative bg-gradient-to-b from-slate-50 via-white to-white py-20 lg:py-28 overflow-hidden border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                {{-- Left: Copy --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Evidence-Based Clinical Nutrition & Functional Medicine
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-slate-900 leading-[1.1]">
                        Precision Metabolic Care & Kinetic Health
                    </h1>
                    <p class="text-base sm:text-lg text-slate-600 font-normal leading-relaxed max-w-2xl">
                        Doctor-led dietary therapeutics, remote WebRTC clinical consultations, and kinetic posture rehabilitation designed to reverse metabolic syndrome, optimize body composition, and sustain athletic vitality.
                    </p>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                        <a href="{{ route('appointments.index') }}"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white px-8 py-3.5 rounded-xl font-bold text-sm shadow-md shadow-emerald-700/20 text-center transition">
                            Schedule Initial Assessment
                        </a>
                        <a href="{{ route('login') }}"
                            class="border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 px-6 py-3.5 rounded-xl font-semibold text-sm text-center transition">
                            Access Patient Dossier →
                        </a>
                    </div>

                    {{-- Clinical Checkpoints --}}
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-slate-200/80">
                        <div>
                            <p class="text-2xl font-black text-slate-900 font-mono">10+ <span class="text-xs font-bold text-slate-400">YRS</span></p>
                            <p class="text-xs text-slate-500 mt-0.5">Clinical Practice</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-emerald-700 font-mono">500+ <span class="text-xs font-bold text-emerald-500">CASES</span></p>
                            <p class="text-xs text-slate-500 mt-0.5">Patients Managed</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900 font-mono">98%</p>
                            <p class="text-xs text-slate-500 mt-0.5">Adherence Rate</p>
                        </div>
                    </div>
                </div>

                {{-- Right: Medical Artifact Card --}}
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl p-6 md:p-8 space-y-6 relative overflow-hidden">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center font-bold text-emerald-800 text-sm">
                                    MH
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Dr. Mehdi Haniballi</h3>
                                    <p class="text-[11px] text-slate-400">Clinical Dietitian & Medical Coach</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                                Verified M.D.
                            </span>
                        </div>

                        {{-- Metric snapshot preview --}}
                        <div class="space-y-3 bg-slate-50 rounded-2xl p-4 border border-slate-200/70">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-medium text-slate-500">Therapeutic Focus:</span>
                                <span class="font-bold text-slate-800">Insulin Sensitivity & Lipidology</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-medium text-slate-500">Consultation Channel:</span>
                                <span class="font-bold text-emerald-700">Encrypted HD WebRTC</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-medium text-slate-500">Kinetic Studio:</span>
                                <span class="font-bold text-slate-800">Active SVG Biomechanical Cues</span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Recent Clinical Protocol</span>
                            <div class="p-3 bg-slate-100/80 rounded-xl border border-slate-200/80 text-xs text-slate-700 space-y-1">
                                <p class="font-bold text-slate-900">14-Day Postprandial Glycemic Attenuation</p>
                                <p class="text-[11px] text-slate-500">Calibrated macronutrient sequence with soleus activation walks.</p>
                            </div>
                        </div>

                        <a href="{{ route('appointments.index') }}" class="w-full bg-slate-900 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-xs transition text-center block shadow-sm">
                            Initiate Telehealth Session
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section: Clinical Scope --}}
    <section id="clinical-scope" class="py-20 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 appear">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Core Disciplines</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-1">Four Pillars of Clinical Care</h2>
                <p class="text-sm text-slate-600 mt-2">Comprehensive protocols designed to target root causes of physiological dysfunction.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Card 1 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4 hover:border-emerald-300 transition appear">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Telehealth Video Consultations</h3>
                        <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                            Private, peer-to-peer clinical consultations with live medical notes, patient telemetry, and in-session dietary reviews.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 block pt-2 border-t border-slate-100">30–45 min appointments</span>
                </div>

                {{-- Card 2 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4 hover:border-emerald-300 transition appear">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Clinical Nutrition Prescriptions</h3>
                        <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                            Gram-accurate macronutrient distribution, low glycemic impact meal plans, and chronobiological nutrient timing.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 block pt-2 border-t border-slate-100">Updated weekly</span>
                </div>

                {{-- Card 3 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4 hover:border-emerald-300 transition appear">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Kinetic Stretching & Mobility</h3>
                        <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                            Guided animated vector movement routines with real-time MET calorie burn calculations and spinal decompression cues.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 block pt-2 border-t border-slate-100">Interactive player</span>
                </div>

                {{-- Card 4 --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4 hover:border-emerald-300 transition appear">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Continuous Metabolic Monitoring</h3>
                        <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                            Patient portal dashboard tracking body mass progression, dietary compliance, and follow-up clinical adjustments.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 block pt-2 border-t border-slate-100">Portal available 24/7</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Section: Patient Journey / How It Works --}}
    <section id="pathway" class="py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 appear">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Clinical Workflow</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-1">Your Diagnostic & Treatment Pathway</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50 space-y-3 appear">
                    <span class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-bold text-xs">1</span>
                    <h3 class="font-bold text-slate-900 text-base">Schedule Online</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Select a weekday consultation slot on our live booking calendar with instant confirmation.
                    </p>
                </div>
                <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50 space-y-3 appear">
                    <span class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-bold text-xs">2</span>
                    <h3 class="font-bold text-slate-900 text-base">Clinical Video Session</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Meet Dr. Haniballi in the encrypted telehealth room for in-depth metabolic assessment.
                    </p>
                </div>
                <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50 space-y-3 appear">
                    <span class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-bold text-xs">3</span>
                    <h3 class="font-bold text-slate-900 text-base">Active Prescription</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Access your nutrition schedule, stretching routines, and follow-up metrics on your portal.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Section: Verified Case Observations (Testimonials) --}}
    <section id="credentials" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 appear">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Patient Outcomes</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-1">Documented Clinical Transformations</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 shadow-sm appear">
                    <div class="flex items-center text-amber-400 gap-1 text-xs">★★★★★</div>
                    <p class="text-xs text-slate-600 leading-relaxed italic">
                        "Dr. Haniballi restructured my meal sequencing and metabolic timing. I shed 12kg over 3 months without fatigue. The telehealth studio made consultations completely effortless."
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">SM</div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Sarah Martinez</p>
                            <p class="text-[10px] text-slate-400">Weight Management Protocol</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 shadow-sm appear">
                    <div class="flex items-center text-amber-400 gap-1 text-xs">★★★★★</div>
                    <p class="text-xs text-slate-600 leading-relaxed italic">
                        "As a competitive footballer, nutrient timing is everything. Dr. Haniballi's glycogen repletion protocols and kinetic mobility drills significantly reduced my recovery intervals."
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">KB</div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Karim Benali</p>
                            <p class="text-[10px] text-slate-400">Sports Nutrition & Performance</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 shadow-sm appear">
                    <div class="flex items-center text-amber-400 gap-1 text-xs">★★★★★</div>
                    <p class="text-xs text-slate-600 leading-relaxed italic">
                        "Managing Type 2 diabetes was causing constant anxiety. Dr. Haniballi's low-glycemic dietary prescription lowered my HbA1c significantly. Exceptional medical rigor."
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">LT</div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Leila Tazi</p>
                            <p class="text-[10px] text-slate-400">Metabolic Endocrinology Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-950 text-slate-400 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="space-y-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-300 block">Cabinet Médical</span>
                    <p class="text-sm font-bold text-white">Dr. Mehdi Haniballi</p>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Nutrition Clinique · Diététique Médicale · Kinésiologie Fonctionnelle.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">Clinical Care</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('appointments.index') }}" class="hover:text-white transition">Schedule Consultation</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Patient Telehealth Portal</a></li>
                        <li><a href="#clinical-scope" class="hover:text-white transition">Macronutrient Therapeutics</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">Cabinet Info</h4>
                    <ul class="space-y-2 text-xs">
                        <li>Casablanca, Morocco</li>
                        <li>Consultations: Lun - Ven (09:00 - 18:00)</li>
                        <li>Direct: contact@haniballi.com</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">Doctor Access</h4>
                    <a href="/admin" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-emerald-400 hover:text-emerald-300 font-semibold transition">
                        <span>Filament Medical Console</span>
                        <span>↗</span>
                    </a>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500">
                <p>&copy; {{ date('Y') }} Dr. Mehdi Haniballi. Tous droits réservés.</p>
                <p>Designed with clinical precision & Tailwind CSS.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.appear').forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>
