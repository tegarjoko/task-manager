@extends('layouts.app')

@section('content')
<div class="flex flex-col h-[calc(100vh-10rem)]">
    <!-- OPS HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-3xl font-bold uppercase tracking-widest text-arc-ink dark:text-white">
                <span class="text-arc-orange">>></span> OPS CALENDAR
            </h2>
            <p class="font-mono text-xs text-arc-orange mt-1">
                // TIMELINE: {{ strtoupper($date->format('F Y')) }}
            </p>
        </div>
        
        <!-- TACTICAL FILTERS -->
        <div class="flex space-x-2 bg-arc-card dark:bg-arc-slate p-1 border border-arc-steel dark:border-gray-700">
            <label class="flex items-center space-x-2 cursor-pointer px-3 py-1 hover:bg-white/5 transition-colors">
                <input type="checkbox" id="filter-critical" class="form-checkbox h-3 w-3 text-red-500 bg-transparent border-gray-500 rounded-none focus:ring-0" onclick="applyFilters()">
                <span class="text-[10px] uppercase font-mono text-gray-400">CRITICAL</span>
            </label>
            <label class="flex items-center space-x-2 cursor-pointer px-3 py-1 hover:bg-white/5 transition-colors">
                <input type="checkbox" id="filter-pending" class="form-checkbox h-3 w-3 text-arc-orange bg-transparent border-gray-500 rounded-none focus:ring-0" onclick="applyFilters()">
                <span class="text-[10px] uppercase font-mono text-gray-400">PENDING</span>
            </label>
        </div>

        <div class="flex gap-4">
            <!-- EXPORT BUTTON -->
            <a href="{{ route('ops.export') }}" class="px-4 py-2 border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white transition-colors font-mono text-xs uppercase flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                SYNC .ICS
            </a>

            <a href="{{ route('ops.index', ['date' => $date->copy()->subMonth()->format('Y-m-d')]) }}" class="px-2 py-2 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-gray-400 hover:text-white hover:bg-gray-800 transition-colors font-mono text-xs uppercase">
                <<
            </a>
            <a href="{{ route('ops.index') }}" class="px-4 py-2 border border-arc-orange text-arc-orange hover:bg-arc-orange hover:text-black transition-colors font-mono text-xs uppercase">
                TODAY
            </a>
            <a href="{{ route('ops.index', ['date' => $date->copy()->addMonth()->format('Y-m-d')]) }}" class="px-2 py-2 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-gray-400 hover:text-white hover:bg-gray-800 transition-colors font-mono text-xs uppercase">
                >>
            </a>
        </div>
    </div>

    <div class="flex flex-grow gap-6 overflow-hidden">
        <!-- LEFT: CALENDAR GRID -->
        <div class="flex-grow flex flex-col">
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
                        
                        // Heatmap Logic
                        $threatLevel = $threatMap[$day->format('Y-m-d')] ?? 'none';
                        $heatClass = '';
                        if ($threatLevel == 'low') $heatClass = 'shadow-[inset_0_0_10px_rgba(59,130,246,0.1)]'; // Blue tint
                        if ($threatLevel == 'medium') $heatClass = 'shadow-[inset_0_0_15px_rgba(249,115,22,0.15)] bg-orange-500/5'; // Orange tint
                        if ($threatLevel == 'high') $heatClass = 'shadow-[inset_0_0_20px_rgba(239,68,68,0.2)] bg-red-500/10 border-red-500/30'; // Red tint
                    @endphp
                    
                    <div 
                        class="relative group border {{ $isToday ? 'border-arc-orange bg-arc-orange/5' : 'border-arc-steel/20 dark:border-gray-800' }} {{ $isCurrentMonth ? 'bg-arc-card dark:bg-arc-slate' : 'bg-gray-50 dark:bg-gray-900 opacity-50' }} {{ $heatClass }} p-2 min-h-[100px] flex flex-col transition-all hover:border-arc-steel dark:hover:border-gray-600 drop-zone"
                        data-date="{{ $day->format('Y-m-d') }}"
                        ondrop="drop(event)" 
                        ondragover="allowDrop(event)"
                        onclick="handleCellClick(event, '{{ $day->format('Y-m-d') }}')"
                    >
                        
                        <!-- Date Number -->
                        <div class="flex justify-between items-start mb-2 pointer-events-none">
                            <span class="font-mono text-sm {{ $isToday ? 'text-arc-orange font-bold' : ($isCurrentMonth ? 'text-arc-ink dark:text-white' : 'text-gray-400') }}">
                                {{ $day->format('d') }}
                            </span>
                            @if($isToday)
                                <span class="w-2 h-2 bg-arc-orange rounded-full animate-pulse"></span>
                            @endif
                        </div>

                        <!-- Tasks List -->
                        <div class="flex-grow overflow-y-auto space-y-1 custom-scrollbar pointer-events-none">
                            @foreach($dayTasks as $task)
                                @php
                                    $isOverdue = \Carbon\Carbon::parse($task->deadline)->endOfDay()->isPast() && !$task->completed;
                                @endphp
                                <div 
                                    draggable="true" 
                                    ondragstart="drag(event)" 
                                    data-task-id="{{ $task->id }}"
                                    data-urgency="{{ $task->urgency }}"
                                    data-status="{{ $task->completed ? 'completed' : 'pending' }}"
                                    class="task-item pointer-events-auto cursor-move text-[10px] font-mono p-1 border-l-2 bg-white dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-700 truncate transition-colors
                                        {{ $task->urgency == 'critical' ? 'border-red-500 text-red-500' : '' }}
                                        {{ $task->urgency == 'high' ? 'border-orange-500 text-orange-500' : '' }}
                                        {{ $task->urgency == 'normal' ? 'border-blue-500 text-blue-500' : '' }}
                                        {{ $task->urgency == 'low' ? 'border-gray-500 text-gray-500' : '' }}
                                        {{ $isOverdue ? 'border-red-600 border-dashed animate-pulse text-red-600 font-bold' : '' }}
                                        {{ $task->completed ? 'opacity-50 line-through' : '' }}
                                    "
                                    onclick="openQuickEditModal(event, @json($task))"
                                >
                                    {{ $task->title }}
                                </div>
                            @endforeach
                        </div>

                        <!-- Add Task Button (Visible on Hover) -->
                        <button onclick="openQuickAddModal(event, '{{ $day->format('Y-m-d') }}')" class="absolute bottom-1 right-1 opacity-0 group-hover:opacity-100 text-arc-orange hover:text-white transition-opacity">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- RIGHT: MISSION AGENDA SIDEBAR -->
        <div class="w-72 flex-shrink-0 bg-arc-card dark:bg-arc-slate border border-arc-steel dark:border-gray-700 p-4 flex flex-col">
            <h3 class="text-lg font-bold uppercase tracking-widest text-arc-ink dark:text-white mb-4 border-b border-arc-steel dark:border-gray-700 pb-2">
                MISSION AGENDA
            </h3>
            
            <div class="flex-grow overflow-y-auto custom-scrollbar space-y-3">
                @if(count($agenda) > 0)
                    @foreach($agenda as $idx => $task)
                        <div class="group cursor-pointer p-3 border border-arc-steel/30 hover:border-arc-orange transition-colors relative" onclick="openQuickEditModal(event, @json($task))">
                            <span class="absolute top-0 right-0 text-[10px] bg-gray-800 text-gray-400 px-1 font-mono">
                                {{ \Carbon\Carbon::parse($task->deadline)->format('M d') }}
                            </span>
                            
                            <h4 class="text-sm font-bold text-arc-ink dark:text-white group-hover:text-arc-orange truncate">
                                {{ $task->title }}
                            </h4>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-[10px] uppercase font-mono px-2 py-0.5 
                                    {{ $task->urgency == 'critical' ? 'bg-red-900/30 text-red-500' : '' }}
                                    {{ $task->urgency == 'high' ? 'bg-orange-900/30 text-orange-500' : '' }}
                                    {{ $task->urgency == 'normal' ? 'bg-blue-900/30 text-blue-500' : '' }}
                                    {{ $task->urgency == 'low' ? 'bg-gray-800 text-gray-500' : '' }}
                                ">
                                    {{ $task->urgency }}
                                </span>
                                @if(\Carbon\Carbon::parse($task->deadline)->isToday())
                                    <span class="text-[10px] text-green-500 animate-pulse">TODAY</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500 font-mono text-xs">
                        NO ACTIVE DIRECTIVES
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-arc-steel dark:border-gray-700 text-center">
                <a href="{{ route('tasks.create') }}" class="block w-full py-2 bg-arc-orange/10 border border-arc-orange text-arc-orange hover:bg-arc-orange hover:text-black font-mono text-xs uppercase transition-colors">
                    + NEW DIRECTIVE
                </a>
            </div>
        </div>
    </div>
</div>

<!-- QUICK ADD MODAL -->
<div id="quick-add-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeQuickAddModal()"></div>
    <div class="relative bg-arc-card dark:bg-arc-slate border border-arc-orange p-6 max-w-sm w-full shadow-2xl animate-glitch-entry">
        <h3 class="text-xl font-bold uppercase text-arc-orange mb-4">>> Rapid Deployment</h3>
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            <input type="hidden" name="deadline" id="quick-add-deadline">
            <input type="hidden" name="redirect_to" value="ops.index">
            
            <div class="space-y-4">
                <input type="text" name="title" required placeholder="DIRECTIVE TITLE" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-2 font-mono text-sm focus:border-arc-orange focus:outline-none">
                
                <select name="urgency" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-2 font-mono text-sm focus:border-arc-orange focus:outline-none uppercase">
                    <option value="normal">Review (Normal)</option>
                    <option value="high">Priority (High)</option>
                    <option value="critical">Critical (Red)</option>
                </select>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeQuickAddModal()" class="px-4 py-2 border border-gray-600 text-gray-400 text-xs font-mono hover:text-white">CANCEL</button>
                <button type="submit" class="bg-arc-orange text-black font-bold px-6 py-2 text-xs uppercase hover:bg-white transition-colors">DEPLOY</button>
            </div>
        </form>
    </div>
</div>

<!-- QUICK EDIT MODAL -->
<div id="quick-edit-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeQuickEditModal()"></div>
    <div class="relative bg-arc-card dark:bg-arc-slate border border-blue-500 p-6 max-w-sm w-full shadow-2xl animate-glitch-entry">
        <h3 class="text-xl font-bold uppercase text-blue-500 mb-4">>> Mission Briefing</h3>
        <form id="quick-edit-form" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_to" value="ops.index">
            
            <div class="space-y-4">
                <input type="text" name="title" id="quick-edit-title" required class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-2 font-mono text-sm focus:border-blue-500 focus:outline-none">
                
                <select name="urgency" id="quick-edit-urgency" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-2 font-mono text-sm focus:border-blue-500 focus:outline-none uppercase">
                    <option value="normal">Review (Normal)</option>
                    <option value="high">Priority (High)</option>
                    <option value="critical">Critical (Red)</option>
                </select>

                <select name="status" id="quick-edit-status" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-2 font-mono text-sm focus:border-blue-500 focus:outline-none uppercase">
                    <option value="0">Pending</option>
                    <option value="1">Completed</option>
                </select>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <a id="quick-edit-full-link" href="#" class="text-[10px] text-blue-400 hover:text-white underline font-mono">FULL BRIEFING >></a>
                <div class="flex gap-2">
                    <button type="button" onclick="closeQuickEditModal()" class="px-4 py-2 border border-gray-600 text-gray-400 text-xs font-mono hover:text-white">CANCEL</button>
                    <button type="submit" class="bg-blue-500 text-black font-bold px-6 py-2 text-xs uppercase hover:bg-white transition-colors">UPDATE</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // ... Drag Logic (Existing) ...
    let draggedTaskId = null;

    function allowDrop(ev) {
        ev.preventDefault();
        ev.currentTarget.classList.add('border-arc-orange', 'bg-arc-orange/10');
    }

    function drag(ev) {
        draggedTaskId = ev.currentTarget.getAttribute('data-task-id');
        ev.currentTarget.style.opacity = '0.4';
    }

    function drop(ev) {
        ev.preventDefault();
        ev.currentTarget.classList.remove('border-arc-orange', 'bg-arc-orange/10');
        const targetDate = ev.currentTarget.getAttribute('data-date');
        if (!draggedTaskId || !targetDate) return;

        fetch('{{ route("ops.update-date") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({task_id: draggedTaskId, date: targetDate})
        }).then(r => r.json()).then(d => { if(d.success) window.location.reload(); });
    }

    // Modal Logic
    function handleCellClick(e, date) {
        // Only trigger if clicking empty space, not a task
        if(e.target === e.currentTarget) {
            openQuickAddModal(e, date);
        }
    }

    function openQuickAddModal(e, date) {
        e.stopPropagation();
        document.getElementById('quick-add-deadline').value = date;
        document.getElementById('quick-add-modal').classList.remove('hidden');
    }

    function closeQuickAddModal() {
        document.getElementById('quick-add-modal').classList.add('hidden');
    }

    function openQuickEditModal(e, task) {
        e.stopPropagation(); // Prevent cell click
        e.preventDefault(); // Prevent link nav
        
        const form = document.getElementById('quick-edit-form');
        form.action = `/tasks/${task.id}`;
        
        document.getElementById('quick-edit-title').value = task.title;
        document.getElementById('quick-edit-urgency').value = task.urgency;
        document.getElementById('quick-edit-status').value = task.completed ? '1' : '0';
        document.getElementById('quick-edit-full-link').href = `/tasks/${task.id}/edit`;

        document.getElementById('quick-edit-modal').classList.remove('hidden');
    }

    function closeQuickEditModal() {
        document.getElementById('quick-edit-modal').classList.add('hidden');
    }

    // Filter Logic
    function applyFilters() {
        const criticalOnly = document.getElementById('filter-critical').checked;
        const pendingOnly = document.getElementById('filter-pending').checked;
        
        document.querySelectorAll('.task-item').forEach(task => {
            let show = true;
            const urgency = task.getAttribute('data-urgency');
            const status = task.getAttribute('data-status');

            if (criticalOnly && urgency !== 'critical') show = false;
            if (pendingOnly && status !== 'pending') show = false;

            // Simple visual toggle (opacity/display)
            if (show) {
                task.classList.remove('opacity-20', 'blur-[1px]');
                task.classList.add('opacity-100');
            } else {
                task.classList.remove('opacity-100');
                task.classList.add('opacity-20', 'blur-[1px]');
            }
        });
    }

    // ... Drag Listeners ...
</script>

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
