<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SacramentApiController extends Controller
{
    public function registerMobileUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users', 
            'phone'    => 'required|string|max:20',        
            'password' => 'required|string|min:6', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $user = User::create([
            'name'         => $request->input('name'),
            'email'        => $request->input('email'),
            'phone_number' => $request->input('phone'), 
            'password'     => Hash::make($request->input('password')),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Registration successful!', 'user' => $user], 201);
    }

    public function loginMobileUser(Request $request) 
    {
        // DEBUGGING: Log everything received from Android
        Log::info('LOGIN_DEBUG_REQUEST', $request->all());

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation error: ' . $validator->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('LOGIN_DEBUG: User not found', ['email' => $request->email]);
            return response()->json(['status' => 'error', 'message' => 'Incorrect web credentials'], 401);
        }

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'success', 'user' => $user], 200);
        }

        Log::warning('LOGIN_DEBUG: Password mismatch', ['email' => $request->email]);
        return response()->json(['status' => 'error', 'message' => 'Incorrect web credentials'], 401);
    }
}