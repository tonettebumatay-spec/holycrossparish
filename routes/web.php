<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ViewingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentRecordController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Admin\AppointmentAvailabilityController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/verify/{type}/{id}', [RecordController::class, 'verify'])->name('records.verify');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('availability/bulk', [AppointmentAvailabilityController::class, 'storeBulk'])->name('availability.bulk');
        Route::resource('availability', AppointmentAvailabilityController::class)->except(['show', 'edit', 'update']);
        Route::patch('availability/{id}/toggle', [AppointmentAvailabilityController::class, 'toggleActive'])->name('availability.toggle');
    });

    Route::get('/records/search', [RecordController::class, 'search'])->name('records.search');
    Route::get('/records', [RecordController::class, 'index'])->name('records.index');
    Route::get('/records/create', [RecordController::class, 'create'])->name('records.create');
    Route::post('/records', [RecordController::class, 'store'])->name('records.store');
    Route::get('/records/{category}/{id}/edit', [RecordController::class, 'edit'])->name('records.edit');
    Route::delete('/records/{id}', [RecordController::class, 'destroy'])->name('records.destroy');
    
    Route::get('/records/baptism/{id}', [RecordController::class, 'showBaptism'])->name('records.baptism.show');
    Route::get('/records/communion/{id}', [RecordController::class, 'showCommunion'])->name('records.communion.show');
    Route::get('/records/confirmation/{id}', [RecordController::class, 'showConfirmation'])->name('records.confirmation.show');
    Route::get('/records/wedding/{id}', [RecordController::class, 'showWedding'])->name('records.wedding.show');
    Route::get('/records/marriage/{id}', [RecordController::class, 'showWedding'])->name('records.marriage.show');
    Route::get('/records/funeral/{id}', [RecordController::class, 'showFuneral'])->name('records.funeral.show');
    
    Route::put('/records/baptism/{id}', [RecordController::class, 'update'])->name('records.baptism.update');
    Route::put('/records/communion/{id}', [RecordController::class, 'update'])->name('records.communion.update');
    Route::put('/records/confirmation/{id}', [RecordController::class, 'update'])->name('records.confirmation.update');
    Route::put('/records/wedding/{id}', [RecordController::class, 'update'])->name('records.wedding.update');
    Route::put('/records/funeral/{id}', [RecordController::class, 'update'])->name('records.funeral.update');

    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::post('/certificates/{id}/complete', [CertificateController::class, 'complete'])->name('certificates.complete');
    Route::post('/certificates/{id}/cancel', [CertificateController::class, 'cancel'])->name('certificates.cancel');
    
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::post('/schedules/{schedule}/archive/{archive_status}', [ScheduleController::class, 'archiveStatus'])->name('schedules.archive_status');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Appointments Management ---
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::put('/appointments/{id}/schedule', [AppointmentController::class, 'schedule'])->name('appointments.schedule');
    Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::put('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{id}/restore', [AppointmentController::class, 'restore'])->name('appointments.restore');
    Route::post('/appointments/{id}/force-expire', [AppointmentController::class, 'forceExpire'])->name('appointments.forceExpire');
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

    // --- Payment Records ---
    Route::get('/payments', [PaymentRecordController::class, 'index'])->name('payments.index');
    Route::get('/payments/export', [PaymentRecordController::class, 'export'])->name('payments.export');
    Route::get('/payments/{id}/{source}', [PaymentRecordController::class, 'show'])->name('payments.show');
    Route::post('/payments/{id}/mark-paid', [PaymentRecordController::class, 'markAsPaid'])->name('payments.markPaid');
    Route::post('/payments/{id}/{source}/archive', [PaymentRecordController::class, 'archive'])->name('payments.archive');
    Route::post('/payments/{id}/{source}/restore', [PaymentRecordController::class, 'restore'])->name('payments.restore');
    Route::delete('/payments/{id}/{source}', [PaymentRecordController::class, 'destroy'])->name('payments.destroy');

    // --- Booking Requirements (Admin) ---
    Route::get('/requirements', [RequirementController::class, 'index'])->name('requirements.index');
    Route::get('/requirements/create', [RequirementController::class, 'create'])->name('requirements.create');
    Route::post('/requirements', [RequirementController::class, 'store'])->name('requirements.store');
    Route::get('/requirements/{id}/edit', [RequirementController::class, 'edit'])->name('requirements.edit');
    Route::put('/requirements/{id}', [RequirementController::class, 'update'])->name('requirements.update');
    Route::delete('/requirements/{id}', [RequirementController::class, 'destroy'])->name('requirements.destroy');
    Route::post('/requirements/{id}/toggle', [RequirementController::class, 'toggle'])->name('requirements.toggle');

    // --- Calendar View ---
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    Route::get('/viewing', [ViewingController::class, 'index'])->name('viewing.index');
    Route::get('/viewing/create', [ViewingController::class, 'create'])->name('viewing.create');
    Route::post('/viewing', [ViewingController::class, 'store'])->name('viewing.store');
    Route::get('/viewing/{id}', [ViewingController::class, 'show'])->name('viewing.show');
    Route::delete('/viewing/{id}', [ViewingController::class, 'destroy'])->name('viewing.destroy');
});

Route::fallback(function () {
    return redirect()->route('login');
});

Route::get('/test-route', function () {
    return "The server is working!";
});