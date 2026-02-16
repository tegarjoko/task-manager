@extends('layouts.app')

@section('content')
    <!-- HEADER / TOOLBAR -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold uppercase tracking-widest text-arc-ink dark:text-white">
                <span class="text-arc-orange">>></span> Intel Archive
            </h2>
        </div>
        
        <!-- SEARCH & FILTER -->
        <form action="{{ route('notes.index') }}" method="GET" class="flex flex-grow gap-2 max-w-xl w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="// SEARCH INTEL DATABASE..." class="flex-grow bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 p-3 text-sm font-mono focus:border-arc-orange focus:outline-none text-arc-ink dark:text-white uppercase">
            
            <select name="color" onchange="this.form.submit()" class="bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 p-3 text-sm font-mono focus:border-arc-orange focus:outline-none text-arc-ink dark:text-white uppercase w-32">
                <option value="">ALL CLASS</option>
                <option value="gray" {{ request('color') == 'gray' ? 'selected' : '' }}>STANDARD</option>
                <option value="blue" {{ request('color') == 'blue' ? 'selected' : '' }}>TACTICAL</option>
                <option value="green" {{ request('color') == 'green' ? 'selected' : '' }}>SECURE</option>
                <option value="orange" {{ request('color') == 'orange' ? 'selected' : '' }}>WARNING</option>
                <option value="red" {{ request('color') == 'red' ? 'selected' : '' }}>CRITICAL</option>
            </select>
        </form>

        <button onclick="openCreateNoteModal()" class="relative group bg-arc-orange text-black font-bold uppercase tracking-wider px-6 py-3 border border-arc-orange hover:bg-transparent hover:text-arc-orange transition-all duration-300 whitespace-nowrap">
             <span class="absolute top-0 right-0 w-2 h-2 bg-arc-paper dark:bg-arc-slate -mr-1 -mt-1 group-hover:bg-arc-orange transition-colors"></span>
             <span class="absolute bottom-0 left-0 w-2 h-2 bg-arc-paper dark:bg-arc-slate -ml-1 -mb-1 group-hover:bg-arc-orange transition-colors"></span>
             // RECORD DATA
        </button>
    </div>

    @if (session('success'))
        <div id="flash-message" class="bg-arc-paper dark:bg-gray-800 border-l-4 border-arc-orange text-arc-ink dark:text-white p-4 mb-8 font-mono text-sm shadow-sm transition-opacity duration-500" role="alert">
            <span class="text-arc-orange mr-2">>></span> {{ session('success') }}
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

    <!-- NOTES GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($notes as $note)
            <div onclick='openEditNoteModal(@json($note))' class="group cursor-pointer relative bg-arc-card dark:bg-arc-slate border p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg
                {{ $note->is_pinned ? 'shadow-[0_0_15px_rgba(255,85,0,0.15)]' : '' }}
                {{ $note->color == 'gray' ? 'border-arc-steel dark:border-gray-700 hover:border-gray-500' : '' }}
                {{ $note->color == 'blue' ? 'border-blue-500 hover:border-blue-400' : '' }}
                {{ $note->color == 'green' ? 'border-green-500 hover:border-green-400' : '' }}
                {{ $note->color == 'orange' ? 'border-orange-500 hover:border-orange-400' : '' }}
                {{ $note->color == 'red' ? 'border-red-500 hover:border-red-400' : '' }}
            ">
                @if($note->is_pinned)
                    <div class="absolute -top-2 -right-2 w-4 h-4 bg-arc-orange rotate-45"></div>
                @endif

                <div class="flex justify-between items-start mb-4">
                    <h3 class="font-bold uppercase tracking-wider text-lg text-arc-ink dark:text-white group-hover:text-arc-orange transition-colors">
                        {{ $note->title }}
                    </h3>
                </div>
                
                <!-- Decryption Effect Area -->
                <div class="relative overflow-hidden font-mono text-sm text-gray-600 dark:text-gray-400 line-clamp-4 h-24 mb-4 encrypted-container" data-original="{{ Str::limit($note->content, 150) }}">
                   <p class="scrambled-text">{{ Str::limit($note->content, 150) }}</p>
                </div>

                <!-- Tags Display -->
                @if($note->tags && count($note->tags) > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($note->tags as $tag)
                        <span class="text-[10px] font-mono bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2 py-0.5 uppercase tracking-wider border border-gray-300 dark:border-gray-700">#{{ $tag }}</span>
                    @endforeach
                </div>
                @endif

                <div class="mt-auto pt-4 border-t border-arc-steel dark:border-gray-800 flex justify-between items-center text-xs font-mono text-gray-500">
                    <span>{{ $note->updated_at->format('Y.m.d H:i') }}</span>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity text-arc-orange">>> ACCESS</span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 dark:text-gray-600 font-mono italic border-2 border-dashed border-gray-800 rounded">
                // NO INTELLIGENCE DATA FOUND
            </div>
        @endforelse
    </div>

    <!-- CREATE MODAL -->
    <div id="create-note-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeCreateNoteModal()"></div>
        <div id="create-note-content" class="relative bg-arc-card dark:bg-arc-slate border border-arc-orange p-8 max-w-2xl w-full shadow-[0_0_30px_rgba(255,85,0,0.15)]">
            <h3 class="text-2xl font-bold uppercase tracking-widest text-arc-ink dark:text-white mb-6 border-b border-gray-800 pb-2">
                <span class="text-arc-orange">>></span> Record New Intel
            </h3>
            
            <form action="{{ route('notes.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block font-mono text-xs uppercase tracking-widest text-gray-500 mb-2">Subject</label>
                        <input type="text" name="title" required class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-sans transition-colors">
                    </div>
                    
                    <div>
                        <label class="block font-mono text-xs uppercase tracking-widest text-gray-500 mb-2">Data Packet</label>
                        <textarea name="content" rows="6" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-mono text-sm transition-colors" placeholder="Supports Markdown (e.g., **bold**, *italic*)"></textarea>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase tracking-widest text-gray-500 mb-2">Tags (#KEYWORDS)</label>
                        <input type="text" name="tags" placeholder="intel, mission, urgent" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-mono text-sm transition-colors">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block font-mono text-xs uppercase tracking-widest text-gray-500 mb-2">Classification</label>
                            <select name="color" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-mono text-sm">
                                <option value="gray">STANDARD (Gray)</option>
                                <option value="blue">TACTICAL (Blue)</option>
                                <option value="green">SECURE (Green)</option>
                                <option value="orange">WARNING (Orange)</option>
                                <option value="red">CRITICAL (Red)</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center cursor-pointer group">
                                <input type="hidden" name="is_pinned" value="0">
                                <input type="checkbox" name="is_pinned" value="1" class="hidden peer">
                                <div class="w-5 h-5 border border-gray-600 peer-checked:bg-arc-orange peer-checked:border-arc-orange transition-all relative">
                                    <div class="absolute inset-0 bg-arc-orange scale-0 peer-checked:scale-100 transition-transform origin-center"></div>
                                </div>
                                <span class="ml-3 font-mono text-xs uppercase tracking-widest text-gray-500 group-hover:text-arc-orange transition-colors">Pin to Top</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <button type="button" onclick="closeCreateNoteModal()" class="px-6 py-2 border border-gray-600 text-gray-400 hover:text-white hover:border-white font-mono text-xs uppercase tracking-widest transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-arc-orange text-black font-bold uppercase tracking-wider px-8 py-2 hover:bg-white transition-colors">
                        Save Intel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="edit-note-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeEditNoteModal()"></div>
        <div id="edit-note-content" class="relative bg-arc-card dark:bg-arc-slate border border-arc-orange p-8 max-w-2xl w-full shadow-[0_0_30px_rgba(255,85,0,0.15)]">
            <h3 class="text-2xl font-bold uppercase tracking-widest text-arc-ink dark:text-white mb-6 border-b border-gray-800 pb-2">
                <span class="text-arc-orange">>></span> Modify Intel
            </h3>
            
            <form id="edit-note-form" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label class="block font-mono text-xs uppercase tracking-widest text-gray-500 mb-2">Subject</label>
                        <input type="text" name="title" id="edit-title" required class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-sans transition-colors">
                    </div>
                    
                    <div>
                        <label class="block font-mono text-xs uppercase tracking-widest text-gray-500 mb-2">Data Packet</label>
                        <textarea name="content" id="edit-content" rows="6" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-mono text-sm transition-colors" placeholder="Supports Markdown"></textarea>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase tracking-widest text-gray-500 mb-2">Tags (#KEYWORDS)</label>
                        <input type="text" name="tags" id="edit-tags" placeholder="intel, mission, urgent" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-mono text-sm transition-colors">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block font-mono text-xs uppercase tracking-widest text-gray-500 mb-2">Classification</label>
                            <select name="color" id="edit-color" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-mono text-sm">
                                <option value="gray">STANDARD (Gray)</option>
                                <option value="blue">TACTICAL (Blue)</option>
                                <option value="green">SECURE (Green)</option>
                                <option value="orange">WARNING (Orange)</option>
                                <option value="red">CRITICAL (Red)</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center cursor-pointer group">
                                <input type="hidden" name="is_pinned" value="0">
                                <input type="checkbox" name="is_pinned" value="1" id="edit-is_pinned" class="hidden peer">
                                <div class="w-5 h-5 border border-gray-600 peer-checked:bg-arc-orange peer-checked:border-arc-orange transition-all relative">
                                    <div class="absolute inset-0 bg-arc-orange scale-0 peer-checked:scale-100 transition-transform origin-center"></div>
                                </div>
                                <span class="ml-3 font-mono text-xs uppercase tracking-widest text-gray-500 group-hover:text-arc-orange transition-colors">Pin to Top</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between gap-4">
                    <button type="button" onclick="submitDeleteNote()" class="text-red-500 font-mono text-xs uppercase tracking-widest hover:text-red-400">
                        [DELETE DATAPACK]
                    </button>
                    <div class="flex gap-4">
                        <!-- EXPORT BUTTON -->
                        <a id="export-link" href="#" class="px-6 py-2 border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white font-mono text-xs uppercase tracking-widest transition-colors flex items-center justify-center">
                            [DOWNLOAD]
                        </a>

                        <button type="button" onclick="closeEditNoteModal()" class="px-6 py-2 border border-gray-600 text-gray-400 hover:text-white hover:border-white font-mono text-xs uppercase tracking-widest transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-arc-orange text-black font-bold uppercase tracking-wider px-8 py-2 hover:bg-white transition-colors">
                            Update
                        </button>
                    </div>
                </div>
            </form>
            
            <form id="delete-note-form" method="POST" class="hidden">
                 @csrf
                 @method('DELETE')
            </form>
        </div>
    </div>

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

    <!-- SCRIPTS -->
    <script>
        // DECRYPTION EFFECT
        document.addEventListener('DOMContentLoaded', () => {
             const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*';
             
             document.querySelectorAll('.encrypted-container').forEach(container => {
                const originalText = container.getAttribute('data-original');
                const textElement = container.querySelector('.scrambled-text');
                let interval = null;

                container.parentElement.addEventListener('mouseenter', () => {
                    let iteration = 0;
                    clearInterval(interval);
                    
                    interval = setInterval(() => {
                        textElement.innerText = originalText
                            .split('')
                            .map((letter, index) => {
                                if(index < iteration) {
                                    return originalText[index];
                                }
                                return chars[Math.floor(Math.random() * chars.length)];
                            })
                            .join('');
                        
                        if(iteration >= originalText.length) { 
                            clearInterval(interval);
                        }
                        
                        iteration += 1/3;
                    }, 30);
                });

                // Reset on leave (Optional: keeps decrypted or re-scrambles?)
                // Choosing to keep decrypted on hover for readability, maybe reset if they leave very quickly?
                // For now, let's just make it scramble IN on hover.
             });
        });

        function openCreateNoteModal() {
            const modal = document.getElementById('create-note-modal');
            const content = document.getElementById('create-note-content');
            modal.classList.remove('hidden');
            content.classList.add('animate-glitch-entry');
        }

        function closeCreateNoteModal() {
            document.getElementById('create-note-modal').classList.add('hidden');
        }

        function openEditNoteModal(note) {
            const modal = document.getElementById('edit-note-modal');
            const content = document.getElementById('edit-note-content');
            
            // Populate Form
            document.getElementById('edit-note-form').action = `/notes/${note.id}`;
            document.getElementById('delete-note-form').action = `/notes/${note.id}`;
            document.getElementById('edit-title').value = note.title;
            document.getElementById('edit-content').value = note.content || '';
            document.getElementById('edit-color').value = note.color;
            document.getElementById('edit-is_pinned').checked = note.is_pinned;
            document.getElementById('edit-tags').value = note.tags ? note.tags.join(', ') : '';
            
            // Set Export Link
            document.getElementById('export-link').href = `/notes/${note.id}/export`;

            modal.classList.remove('hidden');
            content.classList.add('animate-glitch-entry');
        }

        function closeEditNoteModal() {
            document.getElementById('edit-note-modal').classList.add('hidden');
        }

        // New Delete Logic
        function submitDeleteNote() {
            // Close Edit Modal first
            closeEditNoteModal();
            // Open Delete Modal
            const modal = document.getElementById('delete-modal');
            const content = document.getElementById('delete-modal-content');
            modal.classList.remove('hidden');
            content.classList.add('animate-glitch-entry');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
            // Re-open Edit Modal
            const editModal = document.getElementById('edit-note-modal');
            const editContent = document.getElementById('edit-note-content');
            editModal.classList.remove('hidden');
            editContent.classList.add('animate-glitch-entry');
        }

        function confirmDelete() {
            document.getElementById('delete-note-form').submit();
        }
    </script>
@endsection
