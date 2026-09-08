@extends('layouts.app')

@section('title', 'Nutritional Protocol')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    {{-- Clinical Nutrition Header Banner --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03),0_10px_20px_-5px_rgba(0,0,0,0.02)] p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70 uppercase tracking-wide">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Active Clinical Prescription
                    </span>
                    <span class="text-xs text-slate-400">Dr. Mehdi Haniballi</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">
                    Metabolic Health & Lean Mass Preservation
                </h1>
                <p class="text-sm text-slate-600 max-w-2xl font-normal leading-relaxed">
                    Personalized isocaloric protocol calibrated to optimize insulin sensitivity, stabilize postprandial glucose variability, and support lean tissue retention.
                </p>
            </div>

            <div class="bg-slate-900 text-white rounded-xl p-4 border border-slate-800 text-center self-start md:self-auto shadow-md">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Daily Caloric Target</span>
                <div class="flex items-baseline justify-center gap-1 mt-0.5">
                    <span class="text-3xl font-mono font-black text-emerald-400">1,750</span>
                    <span class="text-xs text-slate-400 font-semibold">kcal/day</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Macro Distribution Cards (SVG Icons, Clean Proportions) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        {{-- Protein --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-teal-800 bg-teal-50 px-2.5 py-0.5 rounded border border-teal-200/80 uppercase tracking-wider">
                    Protein · 30%
                </span>
                <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 font-mono">130g</p>
                <p class="text-xs text-slate-500 mt-0.5">520 kcal · Lean poultry, wild fish, whey isolate, eggs</p>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-teal-600 h-2 rounded-full" style="width: 75%"></div>
            </div>
        </div>

        {{-- Complex Carbohydrates --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded border border-amber-200/80 uppercase tracking-wider">
                    Low-GI Carbs · 40%
                </span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 font-mono">175g</p>
                <p class="text-xs text-slate-500 mt-0.5">700 kcal · Rolled oats, tri-color quinoa, sweet potato</p>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-amber-500 h-2 rounded-full" style="width: 60%"></div>
            </div>
        </div>

        {{-- Essential Lipids --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-200/80 uppercase tracking-wider">
                    Essential Lipids · 30%
                </span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 font-mono">58g</p>
                <p class="text-xs text-slate-500 mt-0.5">522 kcal · Cold-pressed EVOO, walnuts, avocado</p>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-emerald-600 h-2 rounded-full" style="width: 85%"></div>
            </div>
        </div>
    </div>

    {{-- Meal Schedule Breakdown --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daily Nutrient Chronobiology Protocol</h3>
                <p class="text-xs text-slate-500 mt-0.5">Nutrient timing synchronized with endogenous insulin peak</p>
            </div>
            <span class="text-xs bg-slate-100 text-slate-700 font-semibold px-3 py-1 rounded-full border border-slate-200">
                Phase 1 Active
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Breakfast --}}
            <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50 hover:border-emerald-300 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-teal-800 uppercase tracking-wide">08:00 · Morning Meal</span>
                    <span class="text-xs font-bold text-slate-700 bg-white px-2.5 py-0.5 rounded border border-slate-200 font-mono">420 kcal</span>
                </div>
                <h4 class="text-sm font-bold text-slate-900">Cinnamon Rolled Oats with Whey Isolate</h4>
                <ul class="text-xs text-slate-600 space-y-1.5 list-none">
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>50g Rolled oats soaked in unsweetened almond milk</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>30g Whey protein isolate (cold micro-filtered)</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>40g Fresh blueberries (polyphenols) + 10g crushed walnuts</li>
                </ul>
            </div>

            {{-- Lunch --}}
            <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50 hover:border-emerald-300 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wide">12:30 · Midday Meal</span>
                    <span class="text-xs font-bold text-slate-700 bg-white px-2.5 py-0.5 rounded border border-slate-200 font-mono">560 kcal</span>
                </div>
                <h4 class="text-sm font-bold text-slate-900">Mediterranean Lemon Herb Chicken & Quinoa</h4>
                <ul class="text-xs text-slate-600 space-y-1.5 list-none">
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>180g Free-range chicken breast grilled with rosemary</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>120g Steamed tri-color quinoa (complex carbohydrates)</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Large arugula, cucumber & tomato salad + 1 tbsp EVOO</li>
                </ul>
            </div>

            {{-- Snack --}}
            <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50 hover:border-emerald-300 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wide">16:30 · Pre-Mobility Snack</span>
                    <span class="text-xs font-bold text-slate-700 bg-white px-2.5 py-0.5 rounded border border-slate-200 font-mono">210 kcal</span>
                </div>
                <h4 class="text-sm font-bold text-slate-900">Greek Yogurt with Chia Seeds & Crisp Apple</h4>
                <ul class="text-xs text-slate-600 space-y-1.5 list-none">
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>150g Organic 0% Greek yogurt (15g casein & whey)</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>1 tbsp Hydrated chia seeds (soluble fiber & ALA)</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>1 Small sliced green apple with Ceylon cinnamon</li>
                </ul>
            </div>

            {{-- Dinner --}}
            <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50 hover:border-emerald-300 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-cyan-800 uppercase tracking-wide">19:30 · Evening Meal</span>
                    <span class="text-xs font-bold text-slate-700 bg-white px-2.5 py-0.5 rounded border border-slate-200 font-mono">560 kcal</span>
                </div>
                <h4 class="text-sm font-bold text-slate-900">Wild Atlantic Salmon with Asparagus & Sweet Potato</h4>
                <ul class="text-xs text-slate-600 space-y-1.5 list-none">
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-cyan-600"></span>160g Pan-seared wild salmon fillet (Omega-3 EPA/DHA)</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-cyan-600"></span>150g Roasted sweet potato wedges</li>
                    <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-cyan-600"></span>Steamed green asparagus spears drizzled with balsamic vinegar</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
