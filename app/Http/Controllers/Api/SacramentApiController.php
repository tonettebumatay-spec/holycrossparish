<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointment; // Make sure your Appointment model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SacramentApiController extends Controller
{
    /**
     * Register a new mobile user.
     * 
     * Expects: name, email, phone, password
     * (password_confirmation is NOT required – Android sends only password)
     * 
     * Returns: user data with success message or validation errors.
     */
    public function registerMobileUser(Request $request)
    {
        // Log incoming request for debugging
        Log::info('REGISTER_DEBUG_REQUEST', $request->all());

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:20|unique:users,phone_number', // unique in the table
            'password' => 'required|string|min:6', // No confirmation needed
        ]);

        if ($validator->fails()) {
            // Return all errors for better debugging
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'         => $request->input('name'),
            'email'        => $request->input('email'),
            'phone_number' => $request->input('phone'), // map 'phone' to 'phone_number'
            'password'     => Hash::make($request->input('password')),
        ]);

        // Optionally generate a token (if you use Sanctum)
        // $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful!',
            'user'    => $user,
            // 'token' => $token ?? null,
        ], 201);
    }

    /**
     * Login a mobile user.
     * 
     * Expects: email, password
     * Returns: user data on success, error on failure.
     */
    public function loginMobileUser(Request $request)
    {
        Log::info('LOGIN_DEBUG_REQUEST', $request->all());

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning('LOGIN_DEBUG: Authentication failed', ['email' => $request->email]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Optionally generate a token
        // $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user'   => $user,
            // 'token' => $token,
        ], 200);
    }

    /**
     * Book an appointment from the mobile app.
     * 
     * Expects: user_name, service_type, appointment_date, contact_number, details (optional)
     * Returns: created appointment data.
     */
    public function bookAppointment(Request $request)
    {
        Log::info('APPOINTMENT_DEBUG_REQUEST', $request->all());

        $validator = Validator::make($request->all(), [
            'user_name'        => 'required|string|max:255',
            'service_type'     => 'required|string|max:100',
            'appointment_date' => 'required|date|after_or_equal:today',
            'contact_number'   => 'required|string|max:20',
            'details'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $appointment = Appointment::create([
            'user_name'        => $request->input('user_name'),
            'service_type'     => $request->input('service_type'),
            'appointment_date' => $request->input('appointment_date'),
            'contact_number'   => $request->input('contact_number'),
            'details'          => $request->input('details'),
            'status'           => 'pending', // default status
        ]);

        return response()->json([
            'status'      => 'success',
            'message'     => 'Appointment booked successfully!',
            'appointment' => $appointment,
        ], 201);
    }
}