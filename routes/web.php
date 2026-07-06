<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ViewingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Route;

// --- Public/Redirect Routes ---
Route::get('/', function () {
    return redirect()->route('login');
});

// --- Authenticated Routes ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Portal Dashboard View
    Route::get('/dashboard', function () {
        $massScheduleCount = \App\Models\Schedule::count();
        $pendingCertificatesCount = \App\Models\Certificate::where('status', 'pending')->count();
        
        $bookCount = \App\Models\Baptism::count()
            + \App\Models\Communion::count()
            + \App\Models\Confirmation::count()
            + \App\Models\Wedding::count();

        if (class_exists('\App\Models\Funeral')) {
            try { $bookCount += \App\Models\Funeral::count(); } catch (\Exception $e) {}
        }

        return view('dashboard', compact('massScheduleCount', 'pendingCertificatesCount', 'bookCount'));
    })->name('dashboard');

    // --- Records Management ---
    // Certificate viewing routes (EACH CATEGORY HAS ITS OWN METHOD)
    Route::get('/records/baptism/{id}', [RecordController::class, 'showBaptism'])->name('records.baptism.show');
    Route::get('/records/communion/{id}', [RecordController::class, 'showCommunion'])->name('records.communion.show');
    Route::get('/records/confirmation/{id}', [RecordController::class, 'showConfirmation'])->name('records.confirmation.show');
    Route::get('/records/wedding/{id}', [RecordController::class, 'showWedding'])->name('records.wedding.show');
    Route::get('/records/marriage/{id}', [RecordController::class, 'showWedding'])->name('records.marriage.show');
    Route::get('/records/funeral/{id}', [RecordController::class, 'showFuneral'])->name('records.funeral.show');
    
    // Index and create routes
    Route::get('/records', [RecordController::class, 'index'])->name('records.index');
    Route::get('/records/create', [RecordController::class, 'create'])->name('records.create');
    Route::post('/records', [RecordController::class, 'store'])->name('records.store');
    
    // Edit routes - Generic edit for all record types
    Route::get('/records/{category}/{id}/edit', [RecordController::class, 'edit'])->name('records.edit');
    
    // Update routes
    Route::put('/records/baptism/{id}', [RecordController::class, 'update'])->name('records.baptism.update');
    Route::put('/records/communion/{id}', [RecordController::class, 'update'])->name('records.communion.update');
    Route::put('/records/confirmation/{id}', [RecordController::class, 'update'])->name('records.confirmation.update');
    Route::put('/records/wedding/{id}', [RecordController::class, 'update'])->name('records.wedding.update');
    Route::put('/records/funeral/{id}', [RecordController::class, 'update'])->name('records.funeral.update');
    
    // Delete route
    Route::delete('/records/{id}', [RecordController::class, 'destroy'])->name('records.destroy');

    // --- Certificates ---
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::post('/certificates/{id}/complete', [CertificateController::class, 'complete'])->name('certificates.complete');
    Route::post('/certificates/{id}/cancel', [CertificateController::class, 'cancel'])->name('certificates.cancel');
    
    // --- Schedules ---
   Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::post('/schedules/{schedule}/archive/{archive_status}', [ScheduleController::class, 'archiveStatus'])->name('schedules.archive_status');

    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    // --- Profiles ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Appointments ---
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');

    // --- Centralized Booking ---
    Route::get('/booking/create', [\App\Http\Controllers\BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [\App\Http\Controllers\BookingController::class, 'store'])->name('booking.store');


    // --- Online Viewings ---
    Route::get('/viewing', [ViewingController::class, 'index'])->name('viewing.index');
    Route::get('/viewing/create', [ViewingController::class, 'create'])->name('viewing.create');
    Route::post('/viewing', [ViewingController::class, 'store'])->name('viewing.store');

    Route::get('/viewing/{id}', [ViewingController::class, 'show'])->name('viewing.show');
    Route::delete('/viewing/{id}', [ViewingController::class, 'destroy'])->name('viewing.destroy');

    Route::get('/booking/create', [BookingController::class, 'create']);
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


// --- Public/Redirect Routes Fallback ---



Route::fallback(function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';