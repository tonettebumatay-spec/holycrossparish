<?php

use App\Http\Controllers\Api\SacramentApiController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    
    // Auth
    Route::post('/register', [SacramentApiController::class, 'registerMobileUser']);
    Route::post('/login', [SacramentApiController::class, 'loginMobileUser']);
    
    // Public Data
    Route::get('/sacraments', [RecordController::class, 'indexApi']);
    Route::get('/verify/{id}', [RecordController::class, 'verifyApi']);
    Route::get('/records/{category}/{id}', [RecordController::class, 'showApi']);

    // Booking Endpoints
    // We separate these so the BookingController knows which model to save to
    Route::post('/booking/baptism', [BookingController::class, 'storeBaptism']);
    Route::post('/booking/wedding', [BookingController::class, 'storeWedding']);
    Route::post('/booking/communion', [BookingController::class, 'storeCommunion']);
    Route::post('/booking/confirmation', [BookingController::class, 'storeConfirmation']);
    Route::post('/booking/funeral', [BookingController::class, 'storeFuneral']);

    // In routes/api.php
    Route::post('/appointment', [AppointmentController::class, 'store']);
});