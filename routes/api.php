<?php

use App\Http\Controllers\Api\SacramentApiController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentAvailabilityController;
use App\Http\Controllers\CertificateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with `/api/v1` – make sure your Android app's
| base URL includes the `/api/v1` part.
|
*/

Route::get('/test', function () {
    return response()->json([
        'status' => 'Server is reachable',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::prefix('v1')->group(function () {

    // ==================== PUBLIC ENDPOINTS ====================
    Route::prefix('auth')->group(function () {
        Route::post('/register', [SacramentApiController::class, 'registerMobileUser']);
        Route::post('/login', [SacramentApiController::class, 'loginMobileUser']);
    });

    // Public data endpoints (no authentication required)
    Route::get('/schedules', [ScheduleController::class, 'indexApi']);
    Route::get('/events', [ScheduleController::class, 'eventsApi']);

    // Optional – if Android ever needs to fetch all sacraments
    // Route::get('/sacraments', [RecordController::class, 'indexApi']);

    // ==================== PROTECTED ENDPOINTS (AUTHENTICATION REQUIRED) ====================
    Route::middleware('auth:sanctum')->group(function () {

        // ---- Authentication & Profile ----
        Route::post('/logout', [SacramentApiController::class, 'logoutMobileUser']);
        Route::get('/profile', [SacramentApiController::class, 'getUserProfile']);

        // ---- Appointments / Bookings ----
        Route::get('/my-appointments', [AppointmentController::class, 'myAppointments']);
        Route::get('/appointments', [AppointmentController::class, 'index']);
        Route::get('/booked-slots', [AppointmentAvailabilityController::class, 'bookedSlots']);

        // ---- Sacrament Booking (must be POST) ----
        Route::post('/book-baptism', [BookingController::class, 'storeBaptism']);
        Route::post('/book-communion', [BookingController::class, 'storeCommunion']);
        Route::post('/book-confirmation', [BookingController::class, 'storeConfirmation']);
        Route::post('/book-wedding', [BookingController::class, 'storeWedding']);
        Route::post('/book-funeral', [BookingController::class, 'storeFuneral']);

        // ---- Generic appointment & certificate (if used) ----
        Route::post('/appointment', [AppointmentController::class, 'store']);
        Route::post('/certificates', [CertificateController::class, 'store']);
    });

    // ==================== DEBUG ROUTE (remove in production) ====================
    // This will list all registered API routes – helpful for debugging
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