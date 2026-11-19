<?php

use App\Http\Controllers\Api\SacramentApiController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AppointmentAvailabilityController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\IdVerificationController;
use App\Http\Controllers\FaceRecognitionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayMongoController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\SupportApiController;
use App\Http\Controllers\MarriageBannController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Api\BibleVerseApiController; // 👈 BAGONG DAGDAG
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'status' => 'Server is reachable',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::prefix('v1')->group(function () {

    // ==================== PUBLIC ENDPOINTS ====================

    // Authentication
    Route::post('/register', [SacramentApiController::class, 'registerMobileUser']);
    Route::post('/login', [SacramentApiController::class, 'loginMobileUser']);

    // OTP
    Route::post('/otp/login/send', [OtpController::class, 'sendLoginOtp']);
    Route::post('/otp/login/verify', [OtpController::class, 'verifyLoginOtp']);
    Route::post('/otp/register/send', [OtpController::class, 'sendRegisterOtp']);
    Route::post('/otp/register/verify', [OtpController::class, 'verifyRegisterOtp']);
    Route::post('/otp/password/send', [OtpController::class, 'sendPasswordResetOtp']);
    Route::post('/otp/password/verify', [OtpController::class, 'verifyPasswordResetOtp']);

    // Public data
    Route::get('/schedules', [ScheduleController::class, 'indexApi']);
    Route::get('/events', [ScheduleController::class, 'eventsApi']);

    Route::get('/availability/month', [AppointmentAvailabilityController::class, 'apiGetMonthAvailability']);
    Route::get('/availability/{sacrament}', [AppointmentAvailabilityController::class, 'apiGetSlots']);

    Route::get('/requirements/{sacrament}', [RequirementController::class, 'apiGetRequirements']);
    Route::get('/banns', [MarriageBannController::class, 'apiIndex']);
    Route::get('/calendar/events', [CalendarController::class, 'events']);

    Route::post('/paymongo/webhook', [PayMongoController::class, 'webhook']);

    // Bible Verse API (Public) 👈 BAGONG DAGDAG
    Route::get('/bible-verse/today', [BibleVerseApiController::class, 'getTodayVerse']);

    // ==================== PROTECTED ENDPOINTS ====================

    Route::middleware('auth:sanctum')->group(function () {

        // Auth & Profile
        Route::post('/logout', [SacramentApiController::class, 'logoutMobileUser']);
        Route::get('/profile', [SacramentApiController::class, 'getUserProfile']);

        // Appointments / Bookings
        Route::get('/my-appointments', [AppointmentController::class, 'myAppointments']);
        Route::get('/appointments', [AppointmentController::class, 'index']);
        Route::get('/booked-slots', [AppointmentAvailabilityController::class, 'bookedSlots']);
        Route::get('/my-confirmed-appointments', [AppointmentController::class, 'getConfirmedAppointments']);

        // Sacrament Bookings
        Route::post('/book-baptism', [BookingController::class, 'storeBaptism']);
        Route::post('/book-communion', [BookingController::class, 'storeCommunion']);
        Route::post('/book-confirmation', [BookingController::class, 'storeConfirmation']);
        Route::post('/book-wedding', [BookingController::class, 'storeWedding']);
        Route::post('/book-funeral', [BookingController::class, 'storeFuneral']);

        Route::post('/booking/baptism', [BookingController::class, 'storeBaptism']);
        Route::post('/booking/communion', [BookingController::class, 'storeCommunion']);
        Route::post('/booking/confirmation', [BookingController::class, 'storeConfirmation']);
        Route::post('/booking/wedding', [BookingController::class, 'storeWedding']);
        Route::post('/booking/funeral', [BookingController::class, 'storeFuneral']);

        // Generic appointment & certificate
        Route::post('/appointment', [AppointmentController::class, 'store']);
        Route::post('/certificates', [CertificateController::class, 'apiStore']);

        // ID Verification & Face Recognition
        Route::post('/id-verifications', [IdVerificationController::class, 'apiStore']);
        Route::post('/face-recognitions', [FaceRecognitionController::class, 'apiStore']);

        // Payment
        Route::post('/payments', [PaymentController::class, 'apiStore']);
        Route::post('/paymongo/qrph', [PayMongoController::class, 'createQrPhPayment']);
        Route::get('/paymongo/status/{reference}', [PayMongoController::class, 'getStatus']);

        // Support
        Route::get('/support/conversation', [SupportApiController::class, 'getOrCreateConversation']);
        Route::get('/support/messages', [SupportApiController::class, 'getMessages']);
        Route::post('/support/messages', [SupportApiController::class, 'sendMessage']);
        Route::get('/support/info', [SupportApiController::class, 'getSupportInfo']);

        // Appointment Availability (Admin only)
        Route::get('/availability', [AppointmentAvailabilityController::class, 'index']);
        Route::post('/availability', [AppointmentAvailabilityController::class, 'store']);
        Route::put('/availability/{id}', [AppointmentAvailabilityController::class, 'update']);
        Route::delete('/availability/{id}', [AppointmentAvailabilityController::class, 'destroy']);
        Route::patch('/availability/{id}/toggle', [AppointmentAvailabilityController::class, 'toggleActive']);
    });

    // ==================== DEBUG ROUTE (local only) ====================
    if (app()->environment('local')) {
        Route::get('/routes', function () {
            $routes = collect(Route::getRoutes())->filter(function ($route) {
                return str_starts_with($route->uri(), 'api/v1');
            })->map(function ($route) {
                return [
                    'method' => implode('|', $route->methods()),
                    'uri'    => $route->uri(),
                    'name'   => $route->getName(),
                ];
            });
            return response()->json($routes);
        });
    }
});