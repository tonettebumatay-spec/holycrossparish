<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BibleVerse;
use Illuminate\Http\Request;

class BibleVerseController extends Controller
{
    public function index()
    {
        $verses = BibleVerse::orderByDesc('date_to_display')->get();
        return view('admin.bible-verses.index', compact('verses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'verse_text' => 'required|string',
            'reference' => 'nullable|string|max:255',
            'date_to_display' => 'required|date|unique:bible_verses,date_to_display',
        ], [
            'date_to_display.unique' => 'May naka-schedule nang verse para sa araw na ito.'
        ]);

        BibleVerse::create($request->all());

        return back()->with('success', 'Bible Verse added successfully!');
    }

    public function destroy($id)
    {
        BibleVerse::findOrFail($id)->delete();
        return back()->with('success', 'Bible Verse deleted.');
    }
}