<?php

use App\Http\Controllers\Api\SacramentApiController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;

Route::get('/test', function () {
    return response()->json(['status' => 'Server is reachable']);
});

Route::prefix('v1')->group(function () {

    // Public Auth
    Route::post('/register', [SacramentApiController::class, 'registerMobileUser']);
    Route::post('/login', [SacramentApiController::class, 'loginMobileUser']);

    // Public Data
    Route::get('/sacraments', [RecordController::class, 'indexApi']);
    Route::get('/verify/{id}', [RecordController::class, 'verifyApi']);
    Route::get('/records/{category}/{id}', [RecordController::class, 'showApi']);

    // 👇 Schedules (Events) endpoint – public
    Route::get('/schedules', [ScheduleController::class, 'indexApi']);

    // Protected (auth:sanctum)
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [SacramentApiController::class, 'logoutMobileUser']);
        Route::get('/profile', [SacramentApiController::class, 'getUserProfile']);

        Route::post('/booking/baptism', [BookingController::class, 'storeBaptism']);
        Route::post('/booking/wedding', [BookingController::class, 'storeWedding']);
        Route::post('/booking/communion', [BookingController::class, 'storeCommunion']);
        Route::post('/booking/confirmation', [BookingController::class, 'storeConfirmation']);
        Route::post('/booking/funeral', [BookingController::class, 'storeFuneral']);

        Route::post('/appointment', [AppointmentController::class, 'store']);
        Route::post('/certificates', [CertificateController::class, 'store']);
    });
});