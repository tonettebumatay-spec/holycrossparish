<?php

use App\Http\Controllers\Api\SacramentApiController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Version 1)
|--------------------------------------------------------------------------
|
| All endpoints are prefixed with /api/v1.
| These are used by the Android mobile app.
|
*/
Route::get('/test', function () {
    return response()->json(['status' => 'Server is reachable']);
});

Route::prefix('v1')->group(function () {

    // ============================================================
    // 1. PUBLIC AUTHENTICATION ENDPOINTS
    // ============================================================
    // These do not require a token.
    // ------------------------------------------------------------
    Route::post('/register', [SacramentApiController::class, 'registerMobileUser']);
    Route::post('/login', [SacramentApiController::class, 'loginMobileUser']);

    // ============================================================
    // 2. PUBLIC DATA ENDPOINTS (No authentication)
    // ============================================================
    // Used to fetch sacraments, verify records, etc.
    // ------------------------------------------------------------
    Route::get('/sacraments', [RecordController::class, 'indexApi']);
    Route::get('/verify/{id}', [RecordController::class, 'verifyApi']);
    Route::get('/records/{category}/{id}', [RecordController::class, 'showApi']);

    // ============================================================
    // 3. PROTECTED ENDPOINTS (Require valid Sanctum token)
    // ============================================================
    // All routes inside this group must include the Authorization header:
    //   Authorization: Bearer <token>
    // ------------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {

        // --- AUTHENTICATION (Logout, Profile) ---
        Route::post('/logout', [SacramentApiController::class, 'logoutMobileUser']);
        Route::get('/profile', [SacramentApiController::class, 'getUserProfile']);

        // --- BOOKING ENDPOINTS (for each sacrament) ---
        // These store a booking in the corresponding sacrament table.
        Route::post('/booking/baptism', [BookingController::class, 'storeBaptism']);
        Route::post('/booking/wedding', [BookingController::class, 'storeWedding']);
        Route::post('/booking/communion', [BookingController::class, 'storeCommunion']);
        Route::post('/booking/confirmation', [BookingController::class, 'storeConfirmation']);
        Route::post('/booking/funeral', [BookingController::class, 'storeFuneral']);

        // --- GENERIC APPOINTMENT ENDPOINT ---
        // Allows booking any type of appointment (with a 'type' field).
        Route::post('/appointment', [AppointmentController::class, 'store']);
    });
});