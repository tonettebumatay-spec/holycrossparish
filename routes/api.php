<?php

use App\Http\Controllers\Api\SacramentApiController;
use App\Http\Controllers\RecordController;
use Illuminate\Support\Facades\Route;

// Standard API group
Route::prefix('v1')->group(function () {
    
    // Mobile client authentication routes
    Route::post('/register', [SacramentApiController::class, 'registerMobileUser']);
    
    // Public routes (Keep these for your QR/Search functionality)
    Route::get('/sacraments', [RecordController::class, 'indexApi']);
    Route::get('/verify/{id}', [RecordController::class, 'verifyApi']);
    
    // Protected routes (You can add ->middleware('auth:sanctum') later for security)
    Route::get('/records/{category}/{id}', [RecordController::class, 'showApi']);
});
