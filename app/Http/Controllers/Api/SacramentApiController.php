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
    /**
     * Handle registration requests originating from the Android client application.
     */
    public function registerMobileUser(Request $request)
    {
        // 1. Check for 'phone' to match Android's parameter key precisely
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:20', // 🟢 Changed from phone_number to phone
            'password' => 'required|string|min:6',   // Aligned to match Android's minimum of 6 chars
        ]);

        // 2. Return validation errors explicitly back to your Android layout handler
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // 3. Create the mobile user entry inside your users table
            // Note: If your actual database column name is 'phone_number', we assign it the incoming 'phone' value here.
            $user = User::create([
                'name'         => $request->input('name'),
                'email'        => $request->input('email'),
                'phone_number' => $request->input('phone'), // 🟢 Maps Android 'phone' to database 'phone_number'
                'password'     => Hash::make($request->input('password')),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Registration successful!',
                'token'   => 'dummy-auth-token', // Matches the response.body()?.token validation loop in Android
                'user'    => $user
            ], 201);

        } catch (\Exception $e) {
            // Safe fallback catcher if there is a missing field migration conflict
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
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