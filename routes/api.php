<?php

use App\Http\Controllers\Api\SacramentApiController;
use App\Http\Controllers\RecordController;
use Illuminate\Support\Facades\Route;

// Standard API group
Route::prefix('v1')->group(function () {
    
    // Mobile client authentication routes
    Route::post('/register', [SacramentApiController::class, 'registerMobileUser']);
    
    // ADDED: Mobile client login route
    Route::post('/login', [SacramentApiController::class, 'loginMobileUser']);
    
    // Public routes (Keep these for your QR/Search functionality)
    Route::get('/sacraments', [RecordController::class, 'indexApi']);
    Route::get('/verify/{id}', [RecordController::class, 'verifyApi']);
    
    // Protected routes
    Route::get('/records/{category}/{id}', [RecordController::class, 'showApi']);

    // Make sure it is POST, not GET
    Route::post('/book-appointment', [SacramentApiController::class, 'bookAppointment']);
});