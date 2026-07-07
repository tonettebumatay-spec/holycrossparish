<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Viewing;
use Illuminate\Http\Request;

class ViewingController extends Controller
{
    public function index()
    {
        $viewings = Viewing::query()->latest()->get();
        return view('viewing.index', compact('viewings'));
    }

    public function create()
    {
        return view('viewing.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|max:5120', // max 5MB – now works with symfony/mime installed
        ]);

        $file = $request->file('image');

        // Convert uploaded image to Base64 string
        $imageBase64 = base64_encode(file_get_contents($file->getRealPath()));

        Viewing::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imageBase64,
        ]);

        return redirect()->route('viewing.index')->with('success', 'Viewing saved successfully!');
    }

    public function show(int $id)
    {
        $viewing = Viewing::query()->findOrFail($id);
        return view('viewing.show', compact('viewing'));
    }

    public function destroy(int $id)
    {
        $viewing = Viewing::query()->findOrFail($id);
        $viewing->delete();

        return redirect()->route('viewing.index')->with('success', 'Viewing deleted successfully!');
    }
}