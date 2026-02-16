@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-8 border-b border-arc-steel dark:border-gray-800 pb-4 transition-colors duration-300">
        <h2 class="text-3xl font-bold uppercase tracking-tight text-arc-ink dark:text-white">
            <span class="text-arc-orange">>></span> Initiate Directive
        </h2>
        <a href="{{ route('tasks.index') }}" class="text-gray-500 hover:text-arc-orange font-mono text-sm uppercase tracking-widest transition-colors">
            [RETURN TO COMMAND]
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-500 text-red-700 dark:text-red-500 p-4 mb-8 font-mono text-sm" role="alert">
            <p class="font-bold mb-2 uppercase">>> SYSTEM ERROR DETECTED:</p>
            <ul class="list-none space-y-1">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tasks.store') }}" method="POST" class="max-w-3xl">
        @csrf
        <div class="mb-8 group">
            <label for="title" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2 group-focus-within:text-arc-orange transition-colors">Directive Title</label>
            <input type="text" name="title" id="title" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-4 focus:outline-none focus:border-arc-orange focus:ring-1 focus:ring-arc-orange transition-all duration-300 font-sans text-lg placeholder-gray-400 dark:placeholder-gray-700" value="{{ old('title') }}" required autofocus placeholder="ENTER DESIGNATION">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="group">
                 <label for="urgency" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2 group-focus-within:text-arc-orange transition-colors">Priority Level</label>
                <div class="relative">
                    <select name="urgency" id="urgency" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-4 focus:outline-none focus:border-arc-orange focus:ring-1 focus:ring-arc-orange transition-all duration-300 appearance-none font-sans" required>
                        <option value="" disabled selected>SELECT LEVEL</option>
                        <option value="low" {{ old('urgency') == 'low' ? 'selected' : '' }} class="bg-white dark:bg-gray-900">LOW</option>
                        <option value="medium" {{ old('urgency') == 'medium' ? 'selected' : '' }} class="bg-white dark:bg-gray-900">MEDIUM</option>
                        <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }} class="bg-white dark:bg-gray-900">HIGH</option>
                        <option value="critical" {{ old('urgency') == 'critical' ? 'selected' : '' }} class="bg-white dark:bg-gray-900">CRITICAL</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>
            </div>
            
            <div class="group">
                <label for="deadline" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2 group-focus-within:text-arc-orange transition-colors">Execution Deadline</label>
                <input type="date" name="deadline" id="deadline" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-4 focus:outline-none focus:border-arc-orange focus:ring-1 focus:ring-arc-orange transition-all duration-300 font-sans dark:[color-scheme:dark]" value="{{ old('deadline') }}">
            </div>
        </div>

        <div class="mb-12 group">
            <label for="description" class="block text-gray-600 dark:text-gray-500 text-xs font-mono uppercase tracking-widest mb-2 group-focus-within:text-arc-orange transition-colors">Operational Details</label>
            <textarea name="description" id="description" rows="5" class="w-full bg-white dark:bg-gray-900 border border-arc-steel dark:border-gray-700 text-arc-ink dark:text-white p-4 focus:outline-none focus:border-arc-orange focus:ring-1 focus:ring-arc-orange transition-all duration-300 font-sans placeholder-gray-400 dark:placeholder-gray-700" placeholder="ENTER BRIEFING DATA">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center justify-end border-t border-arc-steel dark:border-gray-800 pt-8 transition-colors duration-300">
            <button type="submit" class="bg-arc-orange text-black font-bold uppercase tracking-widest px-10 py-4 hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-black transition-colors duration-300 w-full md:w-auto shadow-md hover:shadow-lg">
                // INITIATE PROTOCOL
            </button>
        </div>
    </form>
@endsection
