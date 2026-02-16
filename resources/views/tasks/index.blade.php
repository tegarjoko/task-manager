@extends('layouts.app')

@section('content')
    <!-- COMMAND DASHBOARD HUD -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 font-mono text-sm">
        <!-- Module 1: System Time -->
        <div class="bg-arc-card dark:bg-arc-slate arc-border p-4 flex flex-col justify-between group hover:border-arc-orange transition-colors duration-300">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest">System Time // UTC+7</span>
            <div class="text-3xl font-bold text-arc-ink dark:text-white font-sans mt-2" id="hud-clock">00:00:00</div>
        </div>

        <!-- Module 2: Mission Status -->
        <div class="bg-arc-card dark:bg-arc-slate arc-border p-4 flex flex-col justify-between group hover:border-arc-orange transition-colors duration-300">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest">Mission Status</span>
            <div class="flex items-end justify-between mt-2">
                <span class="text-3xl font-bold text-arc-orange font-sans">{{ $tasks->where('is_completed', true)->count() }}<span class="text-lg text-gray-400">/{{ $tasks->count() }}</span></span>
                <span class="text-xs text-gray-400 mb-1">COMPLETED</span>
            </div>
            <!-- Progress Bar -->
            <div class="w-full h-1 bg-gray-200 dark:bg-gray-700 mt-2 overflow-hidden">
                <div class="h-full bg-arc-orange" style="width: {{ $tasks->count() > 0 ? ($tasks->where('is_completed', true)->count() / $tasks->count()) * 100 : 0 }}%"></div>
            </div>
        </div>

        <!-- Module 3: Active Threats (High Priority) -->
        @php
            // Count High OR Critical tasks that are NOT completed
            $criticalCount = $tasks->whereIn('urgency', ['high', 'critical'])->reject(function ($task) {
                return $task->is_completed;
            })->count();
        @endphp
        <div class="bg-arc-card dark:bg-arc-slate arc-border p-4 flex flex-col justify-between group hover:border-red-500 transition-colors duration-300">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest">Active Threats</span>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-3xl font-bold {{ $criticalCount > 0 ? 'text-red-600 dark:text-red-500 animate-pulse' : 'text-arc-ink dark:text-white' }} font-sans">{{ $criticalCount }}</span>
                <span class="text-xs text-gray-400">CRITICAL PRIORITY</span>
            </div>
        </div>

        <!-- Module 4: Productivity Efficiency -->
        <div class="bg-arc-card dark:bg-arc-slate arc-border p-4 flex flex-col justify-between group hover:border-arc-orange transition-colors duration-300">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest">Efficiency</span>
            <div class="flex items-center gap-2 mt-2">
                 <span class="text-3xl font-bold text-arc-ink dark:text-white font-sans">
                    {{ $tasks->count() > 0 ? round(($tasks->where('is_completed', true)->count() / $tasks->count()) * 100) : 0 }}%
                 </span>
                 <span class="text-xs text-gray-400">RATING</span>
            </div>
        </div>
    </div>

    <!-- Clock Script -->
    <script>
        function updateClock() {
            // Force Asia/Jakarta Time
            const now = new Date().toLocaleTimeString('en-US', {
                timeZone: 'Asia/Jakarta',
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('hud-clock').textContent = now;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold uppercase tracking-tight flex items-center text-arc-ink dark:text-white">
            <span class="w-2 h-8 bg-arc-orange mr-4"></span>
            Active Directives
        </h2>
        <button onclick="openCreateModal()" class="relative group bg-arc-orange text-black font-bold uppercase tracking-wider px-6 py-3 border border-arc-orange hover:bg-transparent hover:text-arc-orange transition-all duration-300">
             <span class="absolute top-0 right-0 w-2 h-2 bg-arc-paper dark:bg-arc-slate -mr-1 -mt-1 group-hover:bg-arc-orange transition-colors"></span>
             <span class="absolute bottom-0 left-0 w-2 h-2 bg-arc-paper dark:bg-arc-slate -ml-1 -mb-1 group-hover:bg-arc-orange transition-colors"></span>
             // Initiate Protocol
        </button>
    </div>

    <!-- SEARCH & FILTERS -->
    <div class="mb-6 flex justify-end">
        <form action="{{ route('tasks.index') }}" method="GET" class="relative group w-full md:w-1/3">
            <!-- Maintain active Sort options when searching -->
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif
            
            <input type="text" name="search" value="{{ request('search') }}" placeholder="SEARCH DIRECTIVES..." 
                class="w-full bg-transparent border-b border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white py-2 pl-2 pr-8 focus:outline-none focus:border-arc-orange font-mono uppercase tracking-widest transition-colors duration-300 placeholder-gray-500">
            <button type="submit" class="absolute right-0 top-0 h-full text-gray-500 hover:text-arc-orange transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>
    </div>

    @if ($message = Session::get('success'))
        <div id="flash-message" class="bg-arc-paper dark:bg-gray-800 border-l-4 border-arc-orange text-arc-ink dark:text-white p-4 mb-8 font-mono text-sm shadow-sm transition-opacity duration-500" role="alert">
            <span class="text-arc-orange mr-2">>></span> {{ $message }}
        </div>
        <script>
            setTimeout(() => {
                const flash = document.getElementById('flash-message');
                if (flash) {
                    flash.style.opacity = '0';
                    setTimeout(() => flash.remove(), 500);
                }
            }, 3000);
        </script>
    @endif

    <div class="bg-arc-card dark:bg-arc-slate border border-arc-steel dark:border-gray-800 transition-colors duration-300">
        <table class="min-w-full text-left font-mono text-sm">
            <thead>
                <tr class="bg-arc-paper dark:bg-gray-900 border-b border-arc-steel dark:border-gray-700 text-gray-600 dark:text-gray-500 uppercase tracking-wider transition-colors duration-300">
                    <th class="py-4 px-6 border-r border-arc-steel dark:border-gray-800">
                        <a href="{{ route('tasks.index', ['sort' => 'title', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center hover:text-arc-orange transition-colors">
                            # Title
                            @if(request('sort') == 'title')
                                <span class="ml-1 text-xs">{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-6 border-r border-arc-steel dark:border-gray-800">Details</th>
                    <th class="py-4 px-6 text-center border-r border-arc-steel dark:border-gray-800">
                         <a href="{{ route('tasks.index', ['sort' => 'urgency', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center hover:text-arc-orange transition-colors">
                            Priority
                            @if(request('sort') == 'urgency')
                                <span class="ml-1 text-xs">{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-6 text-center border-r border-arc-steel dark:border-gray-800">
                        <a href="{{ route('tasks.index', ['sort' => 'deadline', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center hover:text-arc-orange transition-colors">
                            Timeframe
                            @if(request('sort') == 'deadline')
                                <span class="ml-1 text-xs">{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-6 text-center border-r border-arc-steel dark:border-gray-800">
                        <a href="{{ route('tasks.index', ['sort' => 'is_completed', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center hover:text-arc-orange transition-colors">
                            Status
                            @if(request('sort') == 'is_completed')
                                <span class="ml-1 text-xs">{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-6 text-center">CMD</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-arc-steel dark:divide-gray-800 transition-colors duration-300">
                @forelse ($tasks as $task)
                <tr class="hover:bg-arc-paper dark:hover:bg-gray-800/50 transition duration-150 group">
                    <td class="py-4 px-6 border-r border-arc-steel dark:border-gray-800 font-sans font-bold text-arc-ink dark:text-white group-hover:text-arc-orange transition-colors">
                        {{ $task->title }}
                    </td>
                    <td class="py-4 px-6 border-r border-arc-steel dark:border-gray-800 text-gray-600 dark:text-gray-400">
                        {{ Str::limit($task->description, 50) }}
                    </td>
                    <td class="py-4 px-6 text-center border-r border-arc-steel dark:border-gray-800">
                        @if($task->urgency == 'critical')
                            <span class="inline-block px-3 py-1 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-500 border border-red-300 dark:border-red-900 uppercase text-xs tracking-wider animate-pulse">Critical</span>
                        @elseif($task->urgency == 'high')
                            <span class="inline-block px-3 py-1 bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-arc-orange border border-orange-300 dark:border-orange-900 uppercase text-xs tracking-wider">High</span>
                        @elseif($task->urgency == 'medium')
                            <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-300 dark:border-blue-900 uppercase text-xs tracking-wider">Med</span>
                        @elseif($task->urgency == 'low')
                            <span class="inline-block px-3 py-1 bg-gray-200 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 border border-gray-400 dark:border-gray-600 uppercase text-xs tracking-wider">Low</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-600">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center border-r border-arc-steel dark:border-gray-800 text-gray-600 dark:text-gray-400">
                        {{ $task->deadline ? $task->deadline->format('Y.m.d') : 'N/A' }}
                    </td>
                    <td class="py-4 px-6 text-center border-r border-arc-steel dark:border-gray-800">
                        @if($task->is_completed)
                            <span class="text-green-700 dark:text-green-500 uppercase tracking-widest text-xs border border-green-300 dark:border-green-900 px-2 py-1 bg-green-100 dark:bg-green-900/20">Complete</span>
                        @else
                            <span class="text-yellow-700 dark:text-yellow-500 uppercase tracking-widest text-xs border border-yellow-300 dark:border-yellow-900 px-2 py-1 bg-yellow-100 dark:bg-yellow-900/20">Pending</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center flex justify-center gap-4">
                        @if(!$task->is_completed)
                            <form id="complete-form-{{ $task->id }}" action="{{ route('tasks.update', $task->id) }}" method="POST" onsubmit="showCompleteModal(event, {{ $task->id }})" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_completed" value="1">
                                <button type="submit" class="text-gray-500 hover:text-green-700 dark:text-gray-500 dark:hover:text-green-500 transition-colors" title="EXECUTED">
                                    [CHK]
                                </button>
                            </form>
                        @endif
                        <button onclick='openEditModal(@json($task))' class="text-gray-500 hover:text-blue-700 dark:text-gray-500 dark:hover:text-blue-500 transition-colors" title="MODIFY">
                             [MOD]
                        </button>
                        <form id="delete-form-{{ $task->id }}" action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="showDeleteModal(event, {{ $task->id }})" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-500 hover:text-red-700 dark:text-gray-500 dark:hover:text-red-500 transition-colors" title="TERMINATE">
                                 [DEL]
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-gray-500 dark:text-gray-600 font-mono">
                        NO ACTIVE DIRECTIVES FOUND IN DATABASE.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    @if ($tasks->hasPages())
        <div class="flex justify-between items-center mt-6 font-mono text-sm">
            <!-- Previous Link -->
            @if ($tasks->onFirstPage())
                <span class="text-gray-600 dark:text-gray-700 cursor-not-allowed uppercase tracking-widest">[ < PREV SECTOR ]</span>
            @else
                <a href="{{ $tasks->previousPageUrl() }}&sort={{request('sort')}}&direction={{request('direction')}}&search={{request('search')}}" class="text-arc-orange hover:text-white uppercase tracking-widest transition-colors">[ < PREV SECTOR ]</a>
            @endif

            <!-- Page Info -->
            <span class="text-gray-500 uppercase tracking-widest">SECTOR {{ $tasks->currentPage() }} OF {{ $tasks->lastPage() }}</span>

            <!-- Next Link -->
            @if ($tasks->hasMorePages())
                <a href="{{ $tasks->nextPageUrl() }}&sort={{request('sort')}}&direction={{request('direction')}}&search={{request('search')}}" class="text-arc-orange hover:text-white uppercase tracking-widest transition-colors">[ NEXT SECTOR > ]</a>
            @else
                <span class="text-gray-600 dark:text-gray-700 cursor-not-allowed uppercase tracking-widest">[ NEXT SECTOR > ]</span>
            @endif
        </div>
    @endif
    <!-- GLITCH DELETE MODAL -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        
        <!-- Modal Content -->
        <div id="delete-modal-content" class="relative bg-arc-card dark:bg-arc-slate border-2 border-red-600 p-8 max-w-md w-full shadow-2xl skew-x-0">
            <!-- Glitch Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-1 bg-red-600 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-red-600 animate-pulse"></div>
            <div class="absolute -left-1 top-10 w-1 h-8 bg-red-600"></div>
            <div class="absolute -right-1 bottom-10 w-1 h-8 bg-red-600"></div>

            <h3 class="text-3xl font-bold text-red-600 uppercase tracking-tighter mb-4 animate-glitch-text">
                >> CRITICAL WARNING
            </h3>
            <p class="font-mono text-sm text-gray-700 dark:text-gray-300 mb-8 border-l-2 border-red-600 pl-4 py-2">
                Initiating termination protocol. Data integrity will be compromised. <br>
                <span class="text-red-500 font-bold">THIS ACTION IS IRREVERSIBLE.</span>
            </p>
            
            <div class="flex justify-end gap-4 font-mono text-sm">
                <button onclick="closeDeleteModal()" class="px-6 py-3 border border-gray-400 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors uppercase tracking-wider">
                    // ABORT
                </button>
                <button onclick="confirmDelete()" class="px-6 py-3 bg-red-600 text-white border border-red-600 hover:bg-red-700 hover:skew-x-6 transition-all duration-150 uppercase tracking-wider font-bold shadow-lg shadow-red-600/20">
                    >> EXECUTE
                </button>
            </div>
        </div>
    </div>

    <!-- GLITCH SUCCESS MODAL -->
    <div id="complete-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeCompleteModal()"></div>
        
        <!-- Modal Content -->
        <div id="complete-modal-content" class="relative bg-arc-card dark:bg-arc-slate border-2 border-green-500 p-8 max-w-md w-full shadow-2xl skew-x-0">
            <!-- Glitch Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-1 bg-green-500 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-green-500 animate-pulse"></div>
            <div class="absolute -left-1 top-10 w-1 h-8 bg-green-500"></div>
            <div class="absolute -right-1 bottom-10 w-1 h-8 bg-green-500"></div>

            <h3 class="text-3xl font-bold text-green-600 dark:text-green-500 uppercase tracking-tighter mb-4 animate-glitch-text">
                >> OBJECTIVE VERIFIED
            </h3>
            <p class="font-mono text-sm text-gray-700 dark:text-gray-300 mb-8 border-l-2 border-green-500 pl-4 py-2">
                Marking directive as complete. Status update will be pushed to central command. <br>
                <span class="text-green-600 dark:text-green-400 font-bold">CONFIRM PROTOCOL?</span>
            </p>
            
            <div class="flex justify-end gap-4 font-mono text-sm">
                <button onclick="closeCompleteModal()" class="px-6 py-3 border border-gray-400 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors uppercase tracking-wider">
                    // CANCEL
                </button>
                <button onclick="confirmComplete()" class="px-6 py-3 bg-green-600 text-white border border-green-600 hover:bg-green-700 hover:skew-x-6 transition-all duration-150 uppercase tracking-wider font-bold shadow-lg shadow-green-600/20">
                    >> CONFIRM
                </button>
            </div>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div id="create-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeCreateModal()"></div>
        <div class="relative bg-arc-card dark:bg-arc-slate border border-arc-steel dark:border-gray-700 w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <h2 class="text-2xl font-bold uppercase tracking-tight text-arc-ink dark:text-white mb-6">
                    <span class="text-arc-orange">>></span> Initiate Directive
                </h2>
                
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="mb-6 group">
                        <label for="create_title" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2">Directive Title</label>
                        <input type="text" name="title" id="create_title" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:outline-none focus:border-arc-orange font-sans text-lg" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="group">
                             <label for="create_urgency" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2">Priority Level</label>
                            <select name="urgency" id="create_urgency" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:outline-none focus:border-arc-orange font-sans" required>
                                <option value="low">LOW</option>
                                <option value="medium">MEDIUM</option>
                                <option value="high">HIGH</option>
                                <option value="critical">CRITICAL</option>
                            </select>
                        </div>
                        <div class="group">
                            <label for="create_deadline" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2">Execution Deadline</label>
                            <input type="date" name="deadline" id="create_deadline" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:outline-none focus:border-arc-orange font-sans dark:[color-scheme:dark]">
                        </div>
                    </div>

                    <div class="mb-8 group">
                        <label for="create_description" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2">Operational Details</label>
                        <textarea name="description" id="create_description" rows="4" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:outline-none focus:border-arc-orange font-sans"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <button type="button" onclick="closeCreateModal()" class="px-6 py-3 border border-gray-400 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 uppercase tracking-wider font-mono text-sm">
                            [CANCEL]
                        </button>
                        <button type="submit" class="bg-arc-orange text-black font-bold uppercase tracking-widest px-8 py-3 hover:bg-white transition-colors duration-300 shadow-md">
                            // INITIATE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="edit-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeEditModal()"></div>
        <div class="relative bg-arc-card dark:bg-arc-slate border border-arc-steel dark:border-gray-700 w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <h2 class="text-2xl font-bold uppercase tracking-tight text-arc-ink dark:text-white mb-6">
                    <span class="text-arc-gray dark:text-arc-gray">>></span> Modify Directive
                </h2>
                
                <form id="edit-form" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6 group">
                        <label for="edit_title" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2">Directive Title</label>
                        <input type="text" name="title" id="edit_title" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:outline-none focus:border-arc-orange font-sans text-lg" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="group">
                             <label for="edit_urgency" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2">Priority Level</label>
                            <select name="urgency" id="edit_urgency" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:outline-none focus:border-arc-orange font-sans" required>
                                <option value="low">LOW</option>
                                <option value="medium">MEDIUM</option>
                                <option value="high">HIGH</option>
                                <option value="critical">CRITICAL</option>
                            </select>
                        </div>
                        <div class="group">
                            <label for="edit_deadline" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2">Execution Deadline</label>
                            <input type="date" name="deadline" id="edit_deadline" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:outline-none focus:border-arc-orange font-sans dark:[color-scheme:dark]">
                        </div>
                    </div>

                    <div class="mb-6 group">
                        <label for="edit_description" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2">Operational Details</label>
                        <textarea name="description" id="edit_description" rows="4" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:outline-none focus:border-arc-orange font-sans"></textarea>
                    </div>

                    <div class="mb-8 group">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="hidden" name="is_completed" value="0">
                            <input type="checkbox" name="is_completed" id="edit_is_completed" value="1" class="form-checkbox h-5 w-5 text-arc-orange bg-white dark:bg-gray-900 border-arc-steel dark:border-gray-700 rounded-none focus:ring-offset-white dark:focus:ring-offset-gray-900">
                            <span class="ml-3 text-gray-600 dark:text-gray-400 font-bold uppercase tracking-wider text-sm">Directive Fulfilled</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <button type="button" onclick="closeEditModal()" class="px-6 py-3 border border-gray-400 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 uppercase tracking-wider font-mono text-sm">
                            [CANCEL]
                        </button>
                        <button type="submit" class="bg-gray-200 text-black font-bold uppercase tracking-widest px-8 py-3 hover:bg-arc-orange hover:text-black transition-colors duration-300 shadow-md">
                            // UPDATE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let targetFormId = null;

        // DELETE MODAL LOGIC
        function showDeleteModal(event, taskId) {
            event.preventDefault();
            targetFormId = 'delete-form-' + taskId;
            const modal = document.getElementById('delete-modal');
            const content = document.getElementById('delete-modal-content');
            modal.classList.remove('hidden');
            content.classList.add('animate-glitch-entry');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            const content = document.getElementById('delete-modal-content');
            modal.classList.add('hidden');
            content.classList.remove('animate-glitch-entry');
            targetFormId = null;
        }

        function confirmDelete() {
            if (targetFormId) document.getElementById(targetFormId).submit();
        }

        // COMPLETE MODAL LOGIC
        function showCompleteModal(event, taskId) {
            event.preventDefault();
            targetFormId = 'complete-form-' + taskId;
            const modal = document.getElementById('complete-modal');
            const content = document.getElementById('complete-modal-content');
            modal.classList.remove('hidden');
            content.classList.add('animate-glitch-entry');
        }

        function closeCompleteModal() {
            const modal = document.getElementById('complete-modal');
            const content = document.getElementById('complete-modal-content');
            modal.classList.add('hidden');
            content.classList.remove('animate-glitch-entry');
            targetFormId = null;
        }

        function confirmComplete() {
            if (targetFormId) document.getElementById(targetFormId).submit();
        }

        // CREATE MODAL LOGIC
        function openCreateModal() {
            const modal = document.getElementById('create-modal');
            modal.classList.remove('hidden');
            document.getElementById('create_title').focus();
        }

        function closeCreateModal() {
            document.getElementById('create-modal').classList.add('hidden');
        }

        // EDIT MODAL LOGIC
        function openEditModal(task) {
            const modal = document.getElementById('edit-modal');
            const form = document.getElementById('edit-form');
            
            // Set Form Action
            form.action = `/tasks/${task.id}`;
            
            // Populate Fields
            document.getElementById('edit_title').value = task.title;
            document.getElementById('edit_urgency').value = task.urgency;
            document.getElementById('edit_description').value = task.description || '';
            document.getElementById('edit_is_completed').checked = task.is_completed;
            
            // Handle Date (YYYY-MM-DD from YYYY-MM-DDTHH:mm:ss...)
            if (task.deadline) {
                const dateVal = task.deadline.split('T')[0];
                document.getElementById('edit_deadline').value = dateVal;
            } else {
                document.getElementById('edit_deadline').value = '';
            }

            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        // Close on Escape Key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeCreateModal();
                closeEditModal();
                closeDeleteModal();
                closeCompleteModal();
            }
        });
    </script>
@endsection
