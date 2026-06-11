<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Communion;

class CommunionController extends Controller
{
   public function store(Request $request) {
    $validated = $request->validate([
        'book_number' => 'required|integer',
        'page_number' => 'required|integer',
        'line_number' => 'required|integer',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'communion_date' => 'required|date',
        'minister_name' => 'required|string',
        'place_of_baptism' => 'required|string',
    ]);

    \App\Models\Communion::create($validated);
    return redirect()->back()->with('success', 'Communion record archived.');
}
}