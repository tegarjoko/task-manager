@extends('layouts.app')

@section('content')
<div class="flex flex-col h-[calc(100vh-10rem)]">
    <!-- OPS HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold uppercase tracking-widest text-arc-ink dark:text-white">
                <span class="text-arc-orange">>></span> OPS CALENDAR
            </h2>
            <p class="font-mono text-xs text-arc-orange mt-1">
                // TIMELINE: {{ strtoupper($date->format('F Y')) }}
            </p>
        </div>
        
        <div class="flex gap-4">
            <a href="{{ route('ops.index', ['date' => $date->copy()->subMonth()->format('Y-m-d')]) }}" class="px-4 py-2 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-gray-400 hover:text-white hover:bg-gray-800 transition-colors font-mono text-xs uppercase">
                << PREV
            </a>
            <a href="{{ route('ops.index') }}" class="px-4 py-2 border border-arc-orange text-arc-orange hover:bg-arc-orange hover:text-black transition-colors font-mono text-xs uppercase">
                TODAY
            </a>
            <a href="{{ route('ops.index', ['date' => $date->copy()->addMonth()->format('Y-m-d')]) }}" class="px-4 py-2 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-gray-400 hover:text-white hover:bg-gray-800 transition-colors font-mono text-xs uppercase">
                NEXT >>
            </a>
        </div>
    </div>

    <!-- CALENDAR GRID HEADER -->
    <div class="grid grid-cols-7 border-b border-arc-steel dark:border-gray-700 mb-2">
        @foreach(['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'] as $day)
            <div class="text-center font-mono text-xs text-gray-500 pb-2">{{ $day }}</div>
        @endforeach
    </div>

    <!-- CALENDAR GRID BODY -->
    <div class="grid grid-cols-7 grid-rows-5 gap-1 flex-grow">
        @foreach($calendar as $day)
            @php
                $isToday = $day->isToday();
                $isCurrentMonth = $day->month == $date->month;
                $dayTasks = $tasks[$day->format('Y-m-d')] ?? [];
            @endphp
            
            <div class="relative group border {{ $isToday ? 'border-arc-orange bg-arc-orange/5' : 'border-arc-steel/20 dark:border-gray-800' }} {{ $isCurrentMonth ? 'bg-arc-card dark:bg-arc-slate' : 'bg-gray-50 dark:bg-gray-900 opacity-50' }} p-2 min-h-[100px] flex flex-col transition-all hover:border-arc-steel dark:hover:border-gray-600">
                
                <!-- Date Number -->
                <div class="flex justify-between items-start mb-2">
                    <span class="font-mono text-sm {{ $isToday ? 'text-arc-orange font-bold' : ($isCurrentMonth ? 'text-arc-ink dark:text-white' : 'text-gray-400') }}">
                        {{ $day->format('d') }}
                    </span>
                    @if($isToday)
                        <span class="w-2 h-2 bg-arc-orange rounded-full animate-pulse"></span>
                    @endif
                </div>

                <!-- Tasks List -->
                <div class="flex-grow overflow-y-auto space-y-1 custom-scrollbar">
                    @foreach($dayTasks as $task)
                        <a href="{{ route('tasks.edit', $task) }}" class="block text-[10px] font-mono p-1 border-l-2 bg-white dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-700 truncate transition-colors
                            {{ $task->urgency == 'critical' ? 'border-red-500 text-red-500' : '' }}
                            {{ $task->urgency == 'high' ? 'border-orange-500 text-orange-500' : '' }}
                            {{ $task->urgency == 'normal' ? 'border-blue-500 text-blue-500' : '' }}
                            {{ $task->urgency == 'low' ? 'border-gray-500 text-gray-500' : '' }}
                        ">
                            {{ $task->title }}
                        </a>
                    @endforeach
                </div>

                <!-- Add Task Button (Visible on Hover) -->
                <a href="{{ route('tasks.create', ['deadline' => $day->format('Y-m-d')]) }}" class="absolute bottom-1 right-1 opacity-0 group-hover:opacity-100 text-arc-orange hover:text-white transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            </div>
        @endforeach
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #374151; 
        border-radius: 2px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #FF5500; 
    }
</style>
@endsection
