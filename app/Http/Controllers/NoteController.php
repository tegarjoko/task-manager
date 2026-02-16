<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('notes.index', compact('notes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable',
            'color' => 'required|in:gray,blue,green,orange,red',
        ]);

        Note::create($request->all());

        return redirect()->route('notes.index')->with('success', 'Intel Recorded.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable',
            'color' => 'required|in:gray,blue,green,orange,red',
            'is_pinned' => 'boolean',
        ]);

        $note->update($request->all());

        return redirect()->route('notes.index')->with('success', 'Intel Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('notes.index')->with('success', 'Intel Purged.');
    }
}
