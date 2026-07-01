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
        // 1. Validate fields coming from your Android app parameters
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            // Checks uniqueness explicitly against the 'email' column in the 'users' table
            'email'    => 'required|string|email|max:255|unique:users,email',
            // Checks uniqueness explicitly against the 'phone_number' column in the 'users' table, using the 'phone' key sent by Android
            'phone'    => 'required|string|max:20|unique:users,phone_number', 
            'password' => 'required|string|min:6', // Aligned to match Android's minimum requirement
        ]);

        // 2. Return a precise validation error back to your Android Toast if validation rules fail
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // 3. Create the mobile user entry inside your users table
            $user = User::create([
                'name'         => $request->input('name'),
                'email'        => $request->input('email'),
                'phone_number' => $request->input('phone'), // Maps Android layout 'phone' data directly to the database column 'phone_number'
                'password'     => Hash::make($request->input('password')),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Registration successful!',
                'token'   => 'dummy-auth-token', // Matches the response.body()?.token layout validation in Android
                'user'    => $user
            ], 201);

        } catch (\Exception $e) {
            // Catches any underlying table issues or missing structural array items
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all records for the Android List View
     */
    public function index()
    {
        $records = Sacrament::where('year', '>=', 2000)->get();
        return response()->json($records);
    }

    /**
     * Search specifically by Name or Book Number
     */
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

    /**
     * Verify a QR Code scan
     */
    public function verify($token)
    {
        $record = Sacrament::where('qr_code_token', $token)->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid Certificate'], 404);
        }

        return response()->json([
            'status'      => 'Verified',
            'parishioner' => $record->name,
            'details'     => "Book #{$record->book_number}, Page #{$record->page_number}"
        ]);
    }
}