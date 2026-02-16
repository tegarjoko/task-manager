@extends('layouts.app')

@section('content')
    <!-- HEADER / TOOLBAR -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold uppercase tracking-widest text-arc-ink dark:text-white">
                <span class="text-arc-orange">>></span> Intel Archive
            </h2>
        </div>
        
        <button onclick="openCreateNoteModal()" class="relative group bg-arc-orange text-black font-bold uppercase tracking-wider px-6 py-3 border border-arc-orange hover:bg-transparent hover:text-arc-orange transition-all duration-300">
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
                {{ $note->is_pinned ? 'border-arc-orange shadow-[0_0_10px_rgba(255,85,0,0.1)]' : 'border-arc-steel dark:border-gray-700 hover:border-arc-orange' }}
            ">
                @if($note->is_pinned)
                    <div class="absolute -top-2 -right-2 w-4 h-4 bg-arc-orange rotate-45"></div>
                @endif

                <div class="flex justify-between items-start mb-4">
                    <h3 class="font-bold uppercase tracking-wider text-lg text-arc-ink dark:text-white group-hover:text-arc-orange transition-colors">
                        {{ $note->title }}
                    </h3>
                    <div class="w-2 h-2 rounded-full 
                        {{ $note->color == 'blue' ? 'bg-blue-500' : '' }}
                        {{ $note->color == 'green' ? 'bg-green-500' : '' }}
                        {{ $note->color == 'orange' ? 'bg-orange-500' : '' }}
                        {{ $note->color == 'red' ? 'bg-red-500' : '' }}
                        {{ $note->color == 'gray' ? 'bg-gray-500' : '' }}
                    "></div>
                </div>
                
                <p class="font-mono text-sm text-gray-600 dark:text-gray-400 line-clamp-4 whitespace-pre-wrap">
                    {{ Str::limit($note->content, 150) }}
                </p>

                <div class="mt-4 pt-4 border-t border-arc-steel dark:border-gray-800 flex justify-between items-center text-xs font-mono text-gray-500">
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
                        <textarea name="content" rows="6" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-mono text-sm transition-colors"></textarea>
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
                        <textarea name="content" id="edit-content" rows="6" class="w-full bg-arc-paper dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-3 focus:border-arc-orange focus:outline-none font-mono text-sm transition-colors"></textarea>
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

    <!-- SCRIPTS -->
    <script>
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

            modal.classList.remove('hidden');
            content.classList.add('animate-glitch-entry');
        }

        function closeEditNoteModal() {
            document.getElementById('edit-note-modal').classList.add('hidden');
        }

        function submitDeleteNote() {
            if(confirm('CONFIRM DELETION? THIS ACTION CANNOT BE UNDONE.')) {
                document.getElementById('delete-note-form').submit();
            }
        }
    </script>
@endsection
