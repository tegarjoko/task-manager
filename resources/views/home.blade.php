@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[60vh]">
        
        <!-- HEADER -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl md:text-6xl font-bold uppercase tracking-tighter text-arc-ink dark:text-white mb-4">
                <span class="text-arc-orange">>></span> Command Hub
            </h1>
            <p class="font-mono text-gray-500 dark:text-gray-400 tracking-widest text-sm md:text-base">
                SELECT OPERATIONAL MODULE
            </p>
        </div>

        <!-- MODULE GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-4xl">
            
            <!-- ARC.TASK MODULE -->
            <a href="{{ route('tasks.index') }}" class="group relative bg-arc-card dark:bg-arc-slate border border-arc-steel dark:border-gray-700 p-8 hover:border-arc-orange transition-all duration-300 hover:shadow-[0_0_20px_rgba(255,85,0,0.15)] overflow-hidden">
                <!-- Glitch Overlay -->
                <div class="absolute inset-0 bg-arc-orange/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-arc-orange/10 rounded-full blur-xl group-hover:bg-arc-orange/20 transition-colors"></div>

                <div class="relative z-10 flex flex-col items-center">
                    <div class="mb-6 p-4 border border-arc-steel dark:border-gray-600 rounded-full group-hover:border-arc-orange group-hover:text-arc-orange text-gray-400 transition-colors">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold uppercase tracking-widest text-arc-ink dark:text-white group-hover:text-arc-orange transition-colors">ARC.TASK</h2>
                    <p class="font-mono text-xs text-gray-500 mt-2">DIRECTIVE MANAGEMENT SYSTEM</p>
                    <span class="mt-6 px-3 py-1 bg-green-900/20 text-green-500 border border-green-900/30 text-xs font-mono tracking-widest">STATUS: ONLINE</span>
                </div>
            </a>

            <!-- ARC.NOTES MODULE -->
            <a href="{{ url('/notes') }}" class="group relative bg-arc-card dark:bg-arc-slate border border-arc-steel dark:border-gray-700 p-8 hover:border-arc-orange transition-all duration-300 hover:shadow-[0_0_20px_rgba(255,85,0,0.15)] overflow-hidden">
                <!-- Glitch Overlay -->
                <div class="absolute inset-0 bg-arc-orange/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                <div class="absolute -left-4 -bottom-4 w-24 h-24 bg-arc-orange/10 rounded-full blur-xl group-hover:bg-arc-orange/20 transition-colors"></div>

                <div class="relative z-10 flex flex-col items-center">
                    <div class="mb-6 p-4 border border-arc-steel dark:border-gray-600 rounded-full group-hover:border-arc-orange group-hover:text-arc-orange text-gray-400 transition-colors">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold uppercase tracking-widest text-arc-ink dark:text-white group-hover:text-arc-orange transition-colors">ARC.NOTES</h2>
                    <p class="font-mono text-xs text-gray-500 mt-2">CLASSIFIED INTEL ARCHIVE</p>
                    <span class="mt-6 px-3 py-1 bg-green-900/20 text-green-500 border border-green-900/30 text-xs font-mono tracking-widest">STATUS: ONLINE</span>
                </div>
            </a>

            <!-- ARC.OPS MODULE -->
            <a href="{{ route('ops.index') }}" class="group relative bg-arc-card dark:bg-arc-slate border border-arc-steel dark:border-gray-700 p-8 hover:border-arc-orange transition-all duration-300 hover:shadow-[0_0_20px_rgba(255,85,0,0.15)] overflow-hidden md:col-span-2">
                <!-- Glitch Overlay -->
                <div class="absolute inset-0 bg-arc-orange/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 bg-arc-orange/5 rounded-full blur-2xl group-hover:bg-arc-orange/10 transition-colors"></div>

                <div class="relative z-10 flex flex-col items-center">
                    <div class="mb-6 p-4 border border-arc-steel dark:border-gray-600 rounded-full group-hover:border-arc-orange group-hover:text-arc-orange text-gray-400 transition-colors">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold uppercase tracking-widest text-arc-ink dark:text-white group-hover:text-arc-orange transition-colors">ARC.OPS</h2>
                    <p class="font-mono text-xs text-gray-500 mt-2">TACTICAL TIMELINE & DEADLINES</p>
                    <span class="mt-6 px-3 py-1 bg-green-900/20 text-green-500 border border-green-900/30 text-xs font-mono tracking-widest">STATUS: ONLINE</span>
                </div>
            </a>

        </div>

        <div class="mt-16 text-center">
            <p class="font-mono text-[10px] text-gray-600 dark:text-gray-500 uppercase tracking-[0.2em]">
                SYS.VER.3.0 // AUTHORIZED PERSONNEL ONLY
            </p>
        </div>
    </div>
@endsection
