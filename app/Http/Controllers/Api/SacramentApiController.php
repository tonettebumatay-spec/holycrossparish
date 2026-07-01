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
        // 🟢 REMOVED unique checks completely to break the 422 loop
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255', 
            'phone'    => 'required|string|max:20',        
            'password' => 'required|string|min:6', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // 2. Create the mobile user entry inside your users table
            $user = User::create([
                'name'         => $request->input('name'),
                'email'        => $request->input('email'),
                'phone_number' => $request->input('phone'), 
                'password'     => Hash::make($request->input('password')),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Registration successful!',
                'token'   => 'dummy-auth-token', 
                'user'    => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $records = Sacrament::where('year', '>=', 2000)->get();
        return response()->json($records);
    }

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