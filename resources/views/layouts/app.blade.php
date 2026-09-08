<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Clinical Portal') - Dr. Mehdi Haniballi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full antialiased font-sans text-slate-800" x-data="{ mobileMenuOpen: false }">
    <div class="flex h-screen overflow-hidden bg-slate-100/60">

        {{-- Sidebar (Desktop) --}}
        <aside class="w-64 bg-slate-950 text-slate-300 flex flex-col flex-shrink-0 border-r border-slate-800/80 hidden md:flex">
            {{-- Brand Mark --}}
            <div class="px-6 py-6 border-b border-slate-800/80 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-800 flex items-center justify-center text-white shadow-md shadow-emerald-950">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold tracking-widest text-slate-400 uppercase block">Clinique Médicale</span>
                        <span class="text-sm font-bold text-white tracking-tight">Dr. M. Haniballi</span>
                    </div>
                </a>
            </div>

            {{-- Main Navigation Links --}}
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                <div class="px-3 pb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Clinical Suite</span>
                </div>

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-emerald-600/15 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Overview & Metrics</span>
                </a>

                {{-- Appointments --}}
                <a href="{{ route('appointments.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('appointments.*') ? 'bg-emerald-600/15 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Calendar & Visits</span>
                </a>

                {{-- Nutrition --}}
                <a href="{{ route('nutrition.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('nutrition.*') ? 'bg-emerald-600/15 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Nutritional Protocol</span>
                </a>

                {{-- Training & Stretching --}}
                <a href="{{ route('training.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('training.*') ? 'bg-emerald-600/15 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Kinetic Studio & Burn</span>
                </a>

                {{-- Video Consultation --}}
                <a href="{{ route('video.room', ['appointmentId' => 1]) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('video.*') ? 'bg-emerald-600/15 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>Telehealth Video</span>
                </a>

                {{-- Doctor Admin Panel Link (Role Protected) --}}
                @if(auth()->check() && (auth()->user()->isDoctor() || auth()->user()->role === 'admin'))
                <div class="pt-6 pb-2 px-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Doctor Management</span>
                </div>
                <a href="/admin" target="_blank"
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold bg-emerald-950/60 text-emerald-300 hover:bg-emerald-900/60 border border-emerald-600/30 transition">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Filament Admin</span>
                    </div>
                    <span class="text-[10px] text-emerald-400/80 font-mono">↗</span>
                </a>
                @endif
            </nav>

            {{-- Authenticated Session Card in Sidebar --}}
            <div class="border-t border-slate-800/80 p-4">
                <div class="flex items-center gap-3 bg-slate-900/90 rounded-xl p-2.5 border border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-emerald-800 text-white flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-xs font-bold truncate">{{ auth()->user()->name ?? 'Patient' }}</p>
                        <p class="text-slate-400 text-[10px] truncate capitalize">{{ auth()->user()->role ?? 'patient' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Workspace Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Top Navbar --}}
            <header class="bg-white border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-slate-500 hover:text-slate-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-base font-bold text-slate-900 tracking-tight">@yield('title', 'Clinical Portal')</h1>
                        <span class="text-[11px] text-slate-400 block -mt-0.5">Cabinet Médical et Nutritionnel</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Doctor Office Indicator --}}
                    <div class="hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200/60 text-emerald-800 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                        <span>Clinical Server Active</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-rose-600 transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Main Scrollable Workspace --}}
            <main class="flex-1 overflow-y-auto">
                <div class="p-6 md:p-8 max-w-7xl mx-auto">
                    @if (session('status'))
                        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-xs font-medium flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
