<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointment; // Make sure to import your Appointment model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SacramentApiController extends Controller
{
    // --- KEEP YOUR EXISTING USER METHODS ---
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
        Log::info('LOGIN_DEBUG_REQUEST', $request->all());

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation error: ' . $validator->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning('LOGIN_DEBUG: Authentication failed', ['email' => $request->email]);
            return response()->json(['status' => 'error', 'message' => 'Incorrect web credentials'], 401);
        }

        return response()->json(['status' => 'success', 'user' => $user], 200);
    }

    // --- NEW APPOINTMENT BOOKING METHOD ---
    public function bookAppointment(Request $request) 
    {
        Log::info('APPOINTMENT_DEBUG_REQUEST', $request->all());

        $validator = Validator::make($request->all(), [
            'user_name'        => 'required|string',
            'service_type'     => 'required|string',
            'appointment_date' => 'required|date',
            'contact_number'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        // Create the appointment
        $appointment = Appointment::create([
            'user_name'        => $request->input('user_name'),
            'service_type'     => $request->input('service_type'),
            'appointment_date' => $request->input('appointment_date'),
            'contact_number'   => $request->input('contact_number'),
            'details'          => $request->input('details'),
            'status'           => 'pending',
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Appointment booked successfully!', 
            'appointment' => $appointment
        ], 201);
    }
}