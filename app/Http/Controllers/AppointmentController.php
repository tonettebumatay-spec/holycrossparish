<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class AppointmentController extends Controller
{
    /**
     * Display a listing of all appointments from all sacrament tables.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $statusFilter = $request->input('status');
            $typeFilter = $request->input('type');

            // ----- BAPTISMS -----
            $baptismsQuery = Baptism::query()
                ->when($search, function ($q, $search) {
                    return $q->where(function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                          ->orWhere('last_name', 'LIKE', "%{$search}%")
                          ->orWhere('father_name', 'LIKE', "%{$search}%")
                          ->orWhere('mother_maiden_name', 'LIKE', "%{$search}%");
                    });
                })
                ->when($statusFilter, function ($q, $status) {
                    return $q->where('status', $status);
                });

            if (!$typeFilter || $typeFilter === 'Baptism') {
                $baptisms = $baptismsQuery->get()->map(function ($item) {
                    $item->type = 'Baptism';
                    $item->name = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
                    if (empty($item->name)) $item->name = 'N/A';
                    $item->status = $item->status ?? 'pending';
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d H:i A') : 'N/A';
                    $item->cancellation_reason = $item->cancellation_reason ?? null;
                    $item->is_locked = $item->is_locked ?? false;
                    return $item;
                });
            } else {
                $baptisms = collect();
            }

            // ----- COMMUNIONS -----
            $communionsQuery = Communion::query()
                ->when($search, function ($q, $search) {
                    return $q->where('first_name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                ->when($statusFilter, function ($q, $status) {
                    return $q->where('status', $status);
                });

            if (!$typeFilter || $typeFilter === 'Communion') {
                $communions = $communionsQuery->get()->map(function ($item) {
                    $item->type = 'Communion';
                    $item->name = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'N/A';
                    $item->status = $item->status ?? 'pending';
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d H:i A') : 'N/A';
                    $item->cancellation_reason = $item->cancellation_reason ?? null;
                    $item->is_locked = $item->is_locked ?? false;
                    return $item;
                });
            } else {
                $communions = collect();
            }

            // ----- CONFIRMATIONS -----
            $confirmationsQuery = Confirmation::query()
                ->when($search, function ($q, $search) {
                    return $q->where('first_name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                ->when($statusFilter, function ($q, $status) {
                    return $q->where('status', $status);
                });

            if (!$typeFilter || $typeFilter === 'Confirmation') {
                $confirmations = $confirmationsQuery->get()->map(function ($item) {
                    $item->type = 'Confirmation';
                    $item->name = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'N/A';
                    $item->status = $item->status ?? 'pending';
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d H:i A') : 'N/A';
                    $item->cancellation_reason = $item->cancellation_reason ?? null;
                    $item->is_locked = $item->is_locked ?? false;
                    return $item;
                });
            } else {
                $confirmations = collect();
            }

            // ----- WEDDINGS -----
            $weddingsQuery = Wedding::query()
                ->when($search, function ($q, $search) {
                    return $q->where(function ($q) use ($search) {
                        $q->where('groom_name', 'LIKE', "%{$search}%")
                          ->orWhere('bride_name', 'LIKE', "%{$search}%");
                    });
                })
                ->when($statusFilter, function ($q, $status) {
                    return $q->where('status', $status);
                });

            if (!$typeFilter || $typeFilter === 'Wedding') {
                $weddings = $weddingsQuery->get()->map(function ($item) {
                    $item->type = 'Wedding';
                    $groom = $item->groom_name ?? '';
                    $bride = $item->bride_name ?? '';
                    $item->name = ($groom ?: '') . ($groom && $bride ? ' & ' : '') . ($bride ?: '');
                    if (empty($item->name)) $item->name = 'N/A';
                    $item->status = $item->status ?? 'pending';
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d H:i A') : 'N/A';
                    $item->cancellation_reason = $item->cancellation_reason ?? null;
                    $item->is_locked = $item->is_locked ?? false;
                    return $item;
                });
            } else {
                $weddings = collect();
            }

            // ----- FUNERALS -----
            $funeralsQuery = Funeral::query()
                ->when($search, function ($q, $search) {
                    return $q->where('deceased_name', 'LIKE', "%{$search}%");
                })
                ->when($statusFilter, function ($q, $status) {
                    return $q->where('status', $status);
                });

            if (!$typeFilter || $typeFilter === 'Funeral') {
                $funerals = $funeralsQuery->get()->map(function ($item) {
                    $item->type = 'Funeral';
                    $item->name = $item->deceased_name ?? 'N/A';
                    $item->status = $item->status ?? 'pending';
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d H:i A') : 'N/A';
                    $item->cancellation_reason = $item->cancellation_reason ?? null;
                    $item->is_locked = $item->is_locked ?? false;
                    return $item;
                });
            } else {
                $funerals = collect();
            }

            $allAppointments = collect()
                ->merge($baptisms)
                ->merge($communions)
                ->merge($confirmations)
                ->merge($weddings)
                ->merge($funerals)
                ->sortByDesc('created_at')
                ->values();

            return view('appointments.index', [
                'appointments' => $allAppointments,
                'search'       => $search,
                'statusFilter' => $statusFilter,
                'typeFilter'   => $typeFilter,
            ]);

        } catch (\Exception $e) {
            Log::error('Appointment Index Error: ' . $e->getMessage());
            return view('appointments.index', [
                'appointments' => collect(),
                'search'       => null,
                'statusFilter' => null,
                'typeFilter'   => null,
            ]);
        }
    }

    /**
     * Update appointment status (confirm/cancel)
     */
    public function updateStatus(Request $request, $type, $id)
    {
        $modelMap = [
            'baptism'     => Baptism::class,
            'communion'   => Communion::class,
            'confirmation'=> Confirmation::class,
            'wedding'     => Wedding::class,
            'funeral'     => Funeral::class,
        ];

        $model = $modelMap[$type] ?? null;
        if (!$model) {
            return back()->with('error', 'Invalid appointment type.');
        }

        $record = $model::findOrFail($id);

        if ($record->is_locked) {
            return back()->with('error', 'This appointment is locked and cannot be modified.');
        }

        $record->status = $request->status;
        $record->save();

        return back()->with('success', 'Appointment status updated.');
    }

    /**
     * Cancel an appointment with a reason.
     */
    public function cancel(Request $request, $type, $id)
    {
        $modelMap = [
            'baptism' => Baptism::class,
            'communion' => Communion::class,
            'confirmation' => Confirmation::class,
            'wedding' => Wedding::class,
            'funeral' => Funeral::class,
        ];

        $model = $modelMap[$type] ?? abort(404);
        $record = $model::findOrFail($id);

        if ($record->status === 'cancelled' || $record->is_locked) {
            return back()->with('error', 'Appointment cannot be cancelled.');
        }

        $record->status = 'cancelled';
        $record->cancellation_reason = $request->input('reason');
        $record->is_locked = true;
        $record->save();

        return back()->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Set appointment schedule (date and time) and approve it.
     */
    public function schedule(Request $request, $type, $id)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $modelMap = [
            'baptism' => Baptism::class,
            'communion' => Communion::class,
            'confirmation' => Confirmation::class,
            'wedding' => Wedding::class,
            'funeral' => Funeral::class,
        ];

        $model = $modelMap[$type] ?? abort(404);

        $record = $model::findOrFail($id);

        $record->appointment_date = $request->appointment_date;
        $record->appointment_time = $request->appointment_time;
        $record->status = 'approved';

        $record->save();

        return back()->with('success', 'Appointment schedule has been set.');
    }

    /**
     * Delete an appointment
     */
    public function destroy($type, $id)
    {
        $modelMap = [
            'baptism'     => Baptism::class,
            'communion'   => Communion::class,
            'confirmation'=> Confirmation::class,
            'wedding'     => Wedding::class,
            'funeral'     => Funeral::class,
        ];

        $model = $modelMap[$type] ?? null;
        if (!$model) {
            return back()->with('error', 'Invalid appointment type.');
        }

        $record = $model::findOrFail($id);

        if ($record->is_locked) {
            return back()->with('error', 'Locked appointments cannot be deleted.');
        }

        $record->delete();

        return back()->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Fallback store method
     */
    public function store(Request $request)
    {
        return response()->json(['status' => 'error', 'message' => 'Use booking endpoints'], 400);
    }

    /**
     * Get authenticated user's appointments across all sacraments safely.
     */
    public function myAppointments(Request $request)
    {
        try {
            $user = $request->user();
            $appointments = collect();
            $identifier = $user->email ?? ($user->name ?? null);

            /*
            |--------------------------------------------------------------------------
            | Helper
            |--------------------------------------------------------------------------
            | Gets appointments while checking if email/user_id columns exist.
            | Also returns the scheduled date/time for the Android app.
            */
            $safeQuery = function (
                $modelClass,
                $type,
                $nameCallback,
                $originalDateField
            ) use ($user, $identifier) {

                try {
                    $query = $modelClass::query();

                    $modelInstance = new $modelClass;
                    $table = $modelInstance->getTable();

                    // Check available columns
                    $hasEmail = Schema::hasColumn($table, 'email');
                    $hasUserId = Schema::hasColumn($table, 'user_id');
                    $hasAppointmentDate = Schema::hasColumn($table, 'appointment_date');
                    $hasAppointmentTime = Schema::hasColumn($table, 'appointment_time');
                    $hasStatus = Schema::hasColumn($table, 'status');
                    $hasCancellationReason = Schema::hasColumn($table, 'cancellation_reason');
                    $hasIsLocked = Schema::hasColumn($table, 'is_locked');

                    // Filter appointments belonging to the logged-in user
                    if ($user && ($hasEmail || $hasUserId)) {
                        $query->where(function ($q) use (
                            $hasEmail,
                            $hasUserId,
                            $identifier,
                            $user
                        ) {
                            if ($hasEmail && $identifier) {
                                $q->orWhere('email', $identifier);
                            }

                            if ($hasUserId) {
                                $q->orWhere('user_id', $user->id);
                            }
                        });
                    }

                    return $query->get()->map(function ($item) use (
                        $type,
                        $nameCallback,
                        $originalDateField,
                        $hasAppointmentDate,
                        $hasAppointmentTime,
                        $hasCancellationReason,
                        $hasIsLocked
                    ) {

                        // Original sacrament date
                        $originalDate = null;

                        if (
                            $originalDateField &&
                            Schema::hasColumn($item->getTable(), $originalDateField)
                        ) {
                            $originalDate = $item->{$originalDateField} ?? null;
                        }

                        // Admin scheduled date
                        $appointmentDate = $hasAppointmentDate
                            ? ($item->appointment_date ?? null)
                            : null;

                        // Admin scheduled time
                        $appointmentTime = $hasAppointmentTime
                            ? ($item->appointment_time ?? null)
                            : null;

                        // Determine if appointment has been scheduled
                        $isScheduled = !empty($appointmentDate);

                        // Normalize status
                        $status = $item->status ?? 'pending';

                        return [
                            'id' => $item->id,

                            'type' => $type,

                            'name' => $nameCallback($item),

                            // Original requested date
                            'date' => $originalDate,

                            // Date used by Android Booking model
                            'appointment_date' => $appointmentDate ?? $originalDate,

                            // Time set by admin
                            'appointment_time' => $appointmentTime,

                            // Fields expected by Android
                            'scheduled_date' => $appointmentDate,

                            'scheduled_time' => $appointmentTime,

                            'is_scheduled' => $isScheduled,

                            'status' => $status,

                            'cancellation_reason' => $hasCancellationReason
                                ? ($item->cancellation_reason ?? null)
                                : null,

                            'is_locked' => $hasIsLocked
                                ? (bool) ($item->is_locked ?? false)
                                : false,

                            'submitted_at' => $item->created_at
                                ? $item->created_at->format('Y-m-d H:i:s')
                                : null,

                            'created_at' => $item->created_at,
                        ];
                    });

                } catch (\Exception $ex) {

                    Log::error(
                        "Error fetching {$type} appointments: " .
                        $ex->getMessage()
                    );

                    return collect();
                }
            };

            /*
            |--------------------------------------------------------------------------
            | Baptism
            |--------------------------------------------------------------------------
            */
            $appointments = $appointments->merge(
                $safeQuery(
                    Baptism::class,
                    'Baptism',
                    fn($item) =>
                        trim(
                            ($item->first_name ?? '') .
                            ' ' .
                            ($item->last_name ?? '')
                        ) ?: 'N/A',
                    'baptism_date'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Communion
            |--------------------------------------------------------------------------
            */
            $appointments = $appointments->merge(
                $safeQuery(
                    Communion::class,
                    'Communion',
                    fn($item) =>
                        trim(
                            ($item->first_name ?? '') .
                            ' ' .
                            ($item->last_name ?? '')
                        ) ?: ($item->candidate_name ?? 'N/A'),
                    'communion_date'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Confirmation
            |--------------------------------------------------------------------------
            */
            $appointments = $appointments->merge(
                $safeQuery(
                    Confirmation::class,
                    'Confirmation',
                    fn($item) =>
                        trim(
                            ($item->first_name ?? '') .
                            ' ' .
                            ($item->last_name ?? '')
                        ) ?: ($item->candidate_name ?? 'N/A'),
                    'month_day'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Wedding
            |--------------------------------------------------------------------------
            */
            $appointments = $appointments->merge(
                $safeQuery(
                    Wedding::class,
                    'Wedding',
                    fn($item) =>
                        trim(
                            ($item->groom_name ?? '') .
                            ' & ' .
                            ($item->bride_name ?? '')
                        ),
                    'month_day'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Funeral
            |--------------------------------------------------------------------------
            */
            $appointments = $appointments->merge(
                $safeQuery(
                    Funeral::class,
                    'Funeral',
                    fn($item) =>
                        $item->deceased_name ?? 'N/A',
                    'burial_date'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Return API response
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'success' => true,

                'appointments' => $appointments
                    ->sortByDesc('created_at')
                    ->values(),
            ]);

        } catch (\Exception $e) {

            Log::error(
                'MY_APPOINTMENTS_ERROR: ' .
                $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kunin ang mga NA-APPROVE / CONFIRMED na appointments lang (May petsa at oras na).
     */
    public function getConfirmedAppointments(Request $request)
    {
        try {
            $user = $request->user();

            $fetchConfirmed = function ($modelClass, $type, $nameCallback) use ($user) {
                try {
                    $query = $modelClass::query()
                        ->where('user_id', $user->id)
                        ->where('status', 'approved')
                        ->whereNotNull('appointment_date')
                        ->whereNotNull('appointment_time');

                    return $query->get()->map(function ($item) use ($type, $nameCallback) {
                        return [
                            'id' => $item->id,
                            'type' => $type,
                            'name' => $nameCallback($item),
                            'scheduled_date' => $item->appointment_date,
                            'scheduled_time' => $item->appointment_time,
                            'admin_notes' => $item->admin_notes ?? $item->remarks ?? null,
                            'updated_at' => $item->updated_at
                                ? $item->updated_at->toDateTimeString()
                                : null,
                        ];
                    });

                } catch (\Exception $ex) {
                    Log::error(
                        "Error fetching confirmed {$type}: " . $ex->getMessage()
                    );

                    return collect();
                }
            };

            $confirmed = collect()
                ->merge($fetchConfirmed(
                    Baptism::class,
                    'Baptism',
                    fn($item) =>
                        trim(
                            ($item->first_name ?? '') . ' ' .
                            ($item->last_name ?? '')
                        ) ?: 'N/A'
                ))
                ->merge($fetchConfirmed(
                    Communion::class,
                    'Communion',
                    fn($item) =>
                        trim(
                            ($item->first_name ?? '') . ' ' .
                            ($item->last_name ?? '')
                        ) ?: ($item->candidate_name ?? 'N/A')
                ))
                ->merge($fetchConfirmed(
                    Confirmation::class,
                    'Confirmation',
                    fn($item) =>
                        trim(
                            ($item->first_name ?? '') . ' ' .
                            ($item->last_name ?? '')
                        ) ?: ($item->candidate_name ?? 'N/A')
                ))
                ->merge($fetchConfirmed(
                    Wedding::class,
                    'Wedding',
                    fn($item) =>
                        trim(
                            ($item->groom_name ?? '') . ' & ' .
                            ($item->bride_name ?? '')
                        )
                ))
                ->merge($fetchConfirmed(
                    Funeral::class,
                    'Funeral',
                    fn($item) =>
                        $item->deceased_name ?? 'N/A'
                ))
                ->sortByDesc('updated_at')
                ->values();

            return response()->json([
                'status' => 'success',
                'data' => $confirmed,
                'count' => $confirmed->count(),
            ]);

        } catch (\Exception $e) {
            Log::error(
                'CONFIRMED_APPOINTMENTS_ERROR: ' .
                $e->getMessage()
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch confirmed appointments: ' .
                    $e->getMessage(),
            ], 500);
        }
    }
}