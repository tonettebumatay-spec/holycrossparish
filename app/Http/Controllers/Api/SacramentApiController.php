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
     * Accepts:
     * - name (required)
     * - email (required, unique)
     * - phone OR phone_number (required, unique)
     * - password (required, min:6)
     * 
     * Returns: user data with success message or validation errors.
     */
    public function registerMobileUser(Request $request)
    {
        // Log incoming request for debugging
        Log::info('REGISTER_DEBUG_REQUEST', $request->all());

        // Get phone from either 'phone' or 'phone_number' field
        $phone = $request->input('phone') ?? $request->input('phone_number');

        // Basic validation first
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Add custom validation for phone number presence and uniqueness
        $validator->after(function ($validator) use ($phone, $request) {
            if (empty($phone)) {
                $validator->errors()->add('phone', 'Phone number is required (field name: phone or phone_number)');
                return;
            }

            // Check uniqueness in users table (column: phone_number)
            if (User::where('phone_number', $phone)->exists()) {
                $validator->errors()->add('phone', 'The phone number has already been taken.');
            }
        });

        if ($validator->fails()) {
            // Return all errors for better debugging
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Create the user
        $user = User::create([
            'name'         => $request->input('name'),
            'email'        => $request->input('email'),
            'phone_number' => $phone, // store in the correct column
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