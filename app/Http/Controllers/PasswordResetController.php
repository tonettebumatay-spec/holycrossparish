<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\PasswordResetOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // 1. Ipakita ang form para sa Email
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Send OTP sa Email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(100000, 999999); // 6-digit code

        $user = User::where('email', $request->email)->first();
        $user->update([
            'reset_otp' => $otp,
            'reset_otp_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Mag-send ng Email
        Mail::to($user->email)->send(new PasswordResetOtpMail($otp));

        // I-save ang email sa session
        session(['reset_email' => $user->email]);

        // Ipinapasa ang email sa URL/View
        return redirect()->route('password.verify-otp-form', ['email' => $user->email])
                         ->with('success', 'OTP sent to your email.');
    }

    // 3. Ipakita ang form para sa OTP
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email') ?? session('reset_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp-step', compact('email'));
    }

    // 4. I-verify ang OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'User not found.'])->withInput();
        }

        // ✅ ALISIN LAHAT NG ESPASYO GAMIT ANG preg_replace PARA SIGURADONG MAGTUGMA
        $savedOtp = preg_replace('/\s+/', '', (string) $user->reset_otp);
        $inputOtp = preg_replace('/\s+/', '', (string) $request->otp);

        // Check kung tugma ang OTP
        if ($savedOtp !== $inputOtp) {
            return back()->withErrors(['otp' => 'Invalid OTP code. Please check your email again.'])
                         ->withInput();
        }

        // Check kung expired
        if (!$user->reset_otp_expires_at || Carbon::now()->greaterThan($user->reset_otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.'])
                         ->withInput();
        }

        // ✅ Kapag tama, itago ang email sa session
        session(['verified_email' => $user->email]);

        return redirect()->route('password.reset-form');
    }

    // 5. Ipakita ang New Password form
    public function showResetForm()
    {
        if (!session('verified_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password-final');
    }

    // 6. I-save ang bagong password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        if (!session('verified_email')) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', session('verified_email'))->first();

        // I-update ang password at linisin ang OTP
        $user->update([
            'password'             => Hash::make($request->password),
            'reset_otp'            => null,
            'reset_otp_expires_at' => null,
        ]);

        // Linisin ang session
        session()->forget(['reset_email', 'verified_email']);

        return redirect()->route('login')->with('success', 'Password reset successfully. You can now login.');
    }
}