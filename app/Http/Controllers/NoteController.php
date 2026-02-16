<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Note::query();

        // Search Filter (Title, Content, Tags)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Color Filter
        if ($request->filled('color')) {
            $query->where('color', $request->color);
        }

        $notes = $query->orderBy('is_pinned', 'desc')
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
            'tags' => 'nullable|string',
        ]);

        $data = $request->all();
        // Convert comma-separated tags to array
        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }
        else {
            $data['tags'] = [];
        }

        Note::create($data);

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
            'tags' => 'nullable|string',
        ]);

        $data = $request->all();
        // Convert comma-separated tags to array
        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }
        else {
            $data['tags'] = [];
        }

        $note->update($data);

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

    public function export(Note $note)
    {
        $content = "ARC RAIDERS INTEL EXPORT\n";
        $content .= "------------------------\n";
        $content .= "SUBJECT: " . strtoupper($note->title) . "\n";
        $content .= "DATE: " . $note->updated_at . "\n";
        $content .= "CLASS: " . strtoupper($note->color) . "\n";
        $content .= "TAGS: " . ($note->tags ? implode(', ', $note->tags) : 'N/A') . "\n";
        $content .= "------------------------\n\n";
        $content .= $note->content;

        $filename = 'intel_' . $note->id . '_' . date('Ymd') . '.txt';

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
