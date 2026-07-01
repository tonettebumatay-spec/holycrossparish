<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sacrament;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SacramentApiController extends Controller
{
    // Register a new mobile user account from Android Studio
    public function registerMobileUser(Request $request)
    {
        // 1. Validate fields coming from your Android Layout
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'password'     => 'required|string|min:8',
        ]);

        // 2. Return a precise error back to your Android Toast message if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // 3. Create the user record in your PostgreSQL database
        $user = User::create([
            'name'         => $request->input('name'),
            'email'        => $request->input('email'),
            'phone_number'  => $request->input('phone_number'),
            'password'      => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful!',
            'user'    => $user
        ], 201);
    }

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
