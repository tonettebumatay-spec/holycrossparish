<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SacramentApiController extends Controller
{
    /**
     * Register a new mobile user.
     * Expects: name, email, password, and phone or phone_number.
     */
    public function registerMobileUser(Request $request)
    {
        try {
            Log::info('REGISTER_DEBUG_REQUEST', $request->all());

            $phone = $request->input('phone') ?? $request->input('phone_number');

            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $validator->after(function ($validator) use ($phone) {
                if (empty($phone)) {
                    $validator->errors()->add('phone', 'Phone number is required (field: phone or phone_number)');
                    return;
                }
                if (User::where('phone_number', $phone)->exists()) {
                    $validator->errors()->add('phone', 'The phone number has already been taken.');
                }
            });

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name'         => $request->input('name'),
                'email'        => $request->input('email'),
                'phone_number' => $phone,
                'password'     => Hash::make($request->input('password')),
            ]);

            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'message' => 'Registration successful!',
                'user'    => $user,
                'token'   => $token,
            ], 201);

        } catch (\Exception $e) {
            Log::error('REGISTER_ERROR', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login a mobile user.
     * Expects: email, password.
     */
    public function loginMobileUser(Request $request)
    {
        try {
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

            // Revoke old tokens and create a new one
            $user->tokens()->delete();
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'message' => 'Login successful',
                'user'    => $user,
                'token'   => $token,
            ], 200);

        } catch (\Exception $e) {
            Log::error('LOGIN_ERROR', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout a mobile user (revoke the current token).
     */
    public function logoutMobileUser(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Logged out successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('LOGOUT_ERROR', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the authenticated user's profile.
     */
    public function getUserProfile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user'   => $request->user()
        ], 200);
    }

    /**
     * Book an appointment.
     */
    public function bookAppointment(Request $request)
    {
        try {
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
                'status'           => 'pending',
            ]);

            return response()->json([
                'status'      => 'success',
                'message'     => 'Appointment booked successfully!',
                'appointment' => $appointment,
            ], 201);

        } catch (\Exception $e) {
            Log::error('APPOINTMENT_ERROR', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Appointment booking failed: ' . $e->getMessage()
            ], 500);
        }
    }
}