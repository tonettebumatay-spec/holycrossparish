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

Route::prefix('v1')->group(function () {

    // ============================================================
    // 1. AUTHENTICATION ENDPOINTS (Public)
    // ============================================================
    // These do not require a token; they register and log in users.
    // The Android app stores the returned token for subsequent requests.
    // ------------------------------------------------------------
    Route::post('/register', [SacramentApiController::class, 'registerMobileUser']);
    Route::post('/login', [SacramentApiController::class, 'loginMobileUser']);

    // ============================================================
    // 2. PUBLIC DATA ENDPOINTS (No authentication required)
    // ============================================================
    // These are used to fetch list of sacraments, verify records, etc.
    // ------------------------------------------------------------
    Route::get('/sacraments', [RecordController::class, 'indexApi']);
    Route::get('/verify/{id}', [RecordController::class, 'verifyApi']);
    Route::get('/records/{category}/{id}', [RecordController::class, 'showApi']);

    // ============================================================
    // 3. BOOKING ENDPOINTS (for each sacrament)
    // ============================================================
    // These accept a POST request with the booking details in JSON.
    // Each maps to a specific sacrament model (Baptism, Wedding, etc.)
    // The BookingController handles the storage.
    // ------------------------------------------------------------
    Route::post('/booking/baptism', [BookingController::class, 'storeBaptism']);
    Route::post('/booking/wedding', [BookingController::class, 'storeWedding']);
    Route::post('/booking/communion', [BookingController::class, 'storeCommunion']);
    Route::post('/booking/confirmation', [BookingController::class, 'storeConfirmation']);
    Route::post('/booking/funeral', [BookingController::class, 'storeFuneral']);

    // ============================================================
    // 4. GENERIC APPOINTMENT ENDPOINT
    // ============================================================
    // This can be used for a unified appointment booking.
    // The AppointmentController::store should accept a 'type' field
    // to differentiate between baptism, wedding, etc., if needed.
    // ------------------------------------------------------------
    Route::post('/appointment', [AppointmentController::class, 'store']);
});

// ================================================================
// OPTIONAL: Protected Routes (if you decide to add authentication)
// ================================================================
// You can wrap any of the above routes with the auth:sanctum middleware
// to require a valid token. For example:
//
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/booking/baptism', [BookingController::class, 'storeBaptism']);
//     // ... etc.
// });
//
// Then your Android app must include the token in the Authorization header.
// ================================================================