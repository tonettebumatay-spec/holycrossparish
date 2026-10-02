<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    /**
     * Send OTP for login
     */
    public function sendLoginOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Check if admin login
        $purpose = $user->is_admin ? 'admin_login' : 'login';

        $otp = OtpCode::generate($user->id, $user->email, $purpose);

        Mail::to($user->email)->send(new OtpMail($otp->otp_code, $purpose, $user->name));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email.',
            'email' => $user->email,
        ]);
    }

    /**
     * Verify OTP and login
     */
    public function verifyLoginOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $purpose = $user->is_admin ? 'admin_login' : 'login';

        $verified = OtpCode::verify($user->id, $request->otp_code, $purpose);

        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 401);
        }

        // Delete old tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'user' => $user,
            'token' => $token,
            'is_admin' => $user->is_admin,
        ]);
    }

    /**
     * Send OTP for registration
     */
    public function sendRegisterOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Store temporarily in session o cache (hindi pa sa users table)
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // ✅ Save OTP to database (user_id = null para sa registration)
        OtpCode::create([
            'user_id' => null,
            'email' => $request->email,
            'otp_code' => $otp,
            'purpose' => 'register',
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
        ]);

        // Send email
        Mail::to($request->email)->send(new OtpMail($otp, 'register', $request->name));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email.',
            'email' => $request->email,
        ]);
    }

    /**
     * Verify OTP and register
     */
    public function verifyRegisterOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // ✅ Verify OTP gamit ang verification method (walang user_id)
        $verified = OtpCode::verifyRegistration($request->email, $request->otp_code);

        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 401);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'email_verified_at' => now(),
        ]);

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful!',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Send OTP for password reset
     */
    public function sendPasswordResetOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $otp = OtpCode::generate($user->id, $user->email, 'password_reset');

        Mail::to($user->email)->send(new OtpMail($otp->otp_code, 'password_reset', $user->name));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email.',
            'email' => $user->email,
        ]);
    }

    /**
     * Verify OTP and reset password
     */
    public function verifyPasswordResetOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6',
            'new_password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $verified = OtpCode::verify($user->id, $request->otp_code, 'password_reset');

        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 401);
        }

        $user->update(['password' => bcrypt($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successful!',
        ]);
    }
}