<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sacrament;
use Illuminate\Http\Request;

class SacramentApiController extends Controller
{
    // Get all records for the Android List View
    public function index()
    {
        $records = Sacrament::where('year', '>=', 2000)->get();
        return response()->json($records);
    }

    // Search specifically by Name or Book Number
    public function search(Request $request)
    {
        $query = $request->input('query');
        $records = Sacrament::where('year', '>=', 2000)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('book_number', $query);
            })->get();

        return response()->json($records);
    }

    // Verify a QR Code scan
    public function verify($token)
    {
        $record = Sacrament::where('qr_code_token', $token)->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid Certificate'], 404);
        }

        return response()->json([
            'status' => 'Verified',
            'parishioner' => $record->name,
            'details' => "Book #{$record->book_number}, Page #{$record->page_number}"
        ]);
    }
}