<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AppointmentController extends Controller
{
    // ============================================================
    // ADMIN - DISPLAY ALL APPOINTMENTS
    // ============================================================

    public function index(Request $request)
    {
        try {

            $search = $request->input('search');
            $statusFilter = $request->input('status');
            $typeFilter = $request->input('type');

            // ----------------------------------------------------
            // GET FROM appointments TABLE
            // ----------------------------------------------------

            $query = DB::table('appointments');

            // ----------------------------------------------------
            // SEARCH
            // ----------------------------------------------------

            if ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'user_name',
                        'ILIKE',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'service_type',
                        'ILIKE',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'contact_number',
                        'ILIKE',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'details',
                        'ILIKE',
                        "%{$search}%"
                    );
                });
            }

            // ----------------------------------------------------
            // STATUS FILTER
            // ----------------------------------------------------

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            // ----------------------------------------------------
            // SERVICE TYPE FILTER
            // ----------------------------------------------------

            if ($typeFilter) {

                $query->where(
                    'service_type',
                    $typeFilter
                );
            }

            // ----------------------------------------------------
            // GET RESULTS
            // ----------------------------------------------------

            $appointments = $query
                ->orderByDesc('created_at')
                ->get();

            // ----------------------------------------------------
            // RETURN VIEW
            // ----------------------------------------------------

            return view('appointments.index', [
                'appointments' => $appointments,
                'search' => $search,
                'statusFilter' => $statusFilter,
                'typeFilter' => $typeFilter,
            ]);

        } catch (\Exception $e) {

            Log::error(
                'Appointment Index Error: ' .
                $e->getMessage()
            );

            return view('appointments.index', [
                'appointments' => collect(),
                'search' => null,
                'statusFilter' => null,
                'typeFilter' => null,
            ]);
        }
    }

    // ============================================================
    // UPDATE STATUS
    // ============================================================

    public function updateStatus(
        Request $request,
        $type,
        $id
    ) {

        try {

            $request->validate([
                'status' => 'required|string|in:pending,approved,confirmed,cancelled,rejected',
            ]);

            $appointment = DB::table('appointments')
                ->where('id', $id)
                ->first();

            if (!$appointment) {

                return back()->with(
                    'error',
                    'Appointment not found.'
                );
            }

            // Check type
            if (
                strtolower($appointment->service_type) !==
                strtolower($type)
            ) {

                return back()->with(
                    'error',
                    'Invalid appointment type.'
                );
            }

            // ----------------------------------------------------
            // UPDATE
            // ----------------------------------------------------

            DB::table('appointments')
                ->where('id', $id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now(),
                ]);

            return back()->with(
                'success',
                'Appointment status updated successfully.'
            );

        } catch (\Exception $e) {

            Log::error(
                'Update Appointment Status Error: ' .
                $e->getMessage()
            );

            return back()->with(
                'error',
                'Failed to update appointment status.'
            );
        }
    }

    // ============================================================
    // CANCEL APPOINTMENT
    // ============================================================

    public function cancel(
        Request $request,
        $type,
        $id
    ) {

        try {

            $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);

            $appointment = DB::table('appointments')
                ->where('id', $id)
                ->first();

            if (!$appointment) {

                return back()->with(
                    'error',
                    'Appointment not found.'
                );
            }

            // ----------------------------------------------------
            // CHECK TYPE
            // ----------------------------------------------------

            if (
                strtolower($appointment->service_type) !==
                strtolower($type)
            ) {

                return back()->with(
                    'error',
                    'Invalid appointment type.'
                );
            }

            // ----------------------------------------------------
            // CHECK CURRENT STATUS
            // ----------------------------------------------------

            if ($appointment->status === 'cancelled') {

                return back()->with(
                    'error',
                    'Appointment is already cancelled.'
                );
            }

            // ----------------------------------------------------
            // UPDATE
            // ----------------------------------------------------

            DB::table('appointments')
                ->where('id', $id)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);

            return back()->with(
                'success',
                'Appointment cancelled successfully.'
            );

        } catch (\Exception $e) {

            Log::error(
                'Cancel Appointment Error: ' .
                $e->getMessage()
            );

            return back()->with(
                'error',
                'Failed to cancel appointment.'
            );
        }
    }

    // ============================================================
    // SCHEDULE APPOINTMENT
    // ============================================================

    public function schedule(
        Request $request,
        $type,
        $id
    ) {

        try {

            $request->validate([
                'appointment_date' => 'required|date',
                'appointment_time' => 'required',
            ]);

            $appointment = DB::table('appointments')
                ->where('id', $id)
                ->first();

            if (!$appointment) {

                return back()->with(
                    'error',
                    'Appointment not found.'
                );
            }

            // ----------------------------------------------------
            // CHECK TYPE
            // ----------------------------------------------------

            if (
                strtolower($appointment->service_type) !==
                strtolower($type)
            ) {

                return back()->with(
                    'error',
                    'Invalid appointment type.'
                );
            }

            // ----------------------------------------------------
            // CHECK IF appointment_time COLUMN EXISTS
            // ----------------------------------------------------

            $updateData = [
                'appointment_date' => $request->appointment_date,
                'status' => 'approved',
                'updated_at' => now(),
            ];

            if (
                Schema::hasColumn(
                    'appointments',
                    'appointment_time'
                )
            ) {

                $updateData['appointment_time'] =
                    $request->appointment_time;
            }

            // ----------------------------------------------------
            // UPDATE
            // ----------------------------------------------------

            DB::table('appointments')
                ->where('id', $id)
                ->update($updateData);

            return back()->with(
                'success',
                'Appointment schedule has been set successfully.'
            );

        } catch (\Exception $e) {

            Log::error(
                'Schedule Appointment Error: ' .
                $e->getMessage()
            );

            return back()->with(
                'error',
                'Failed to schedule appointment: ' .
                $e->getMessage()
            );
        }
    }

    // ============================================================
    // DELETE APPOINTMENT
    // ============================================================

    public function destroy(
        $type,
        $id
    ) {

        try {

            $appointment = DB::table('appointments')
                ->where('id', $id)
                ->first();

            if (!$appointment) {

                return back()->with(
                    'error',
                    'Appointment not found.'
                );
            }

            // ----------------------------------------------------
            // CHECK TYPE
            // ----------------------------------------------------

            if (
                strtolower($appointment->service_type) !==
                strtolower($type)
            ) {

                return back()->with(
                    'error',
                    'Invalid appointment type.'
                );
            }

            // ----------------------------------------------------
            // DELETE
            // ----------------------------------------------------

            DB::table('appointments')
                ->where('id', $id)
                ->delete();

            return back()->with(
                'success',
                'Appointment deleted successfully.'
            );

        } catch (\Exception $e) {

            Log::error(
                'Delete Appointment Error: ' .
                $e->getMessage()
            );

            return back()->with(
                'error',
                'Failed to delete appointment.'
            );
        }
    }

    // ============================================================
    // FALLBACK STORE
    // ============================================================

    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Use the booking endpoints.'
        ], 400);
    }

    // ============================================================
    // ANDROID - MY APPOINTMENTS
    // ============================================================

    public function myAppointments(Request $request)
    {
        try {

            $user = $request->user();

            // ----------------------------------------------------
            // CHECK USER
            // ----------------------------------------------------

            if (!$user) {

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            // ----------------------------------------------------
            // BUILD QUERY
            // ----------------------------------------------------

            $query = DB::table('appointments');

            /*
             * If appointments has user_id, use it.
             */
            if (
                Schema::hasColumn(
                    'appointments',
                    'user_id'
                )
            ) {

                $query->where(
                    'user_id',
                    $user->id
                );

            /*
             * Otherwise use user_name.
             */
            } else {

                $query->where(
                    'user_name',
                    $user->name
                );
            }

            // ----------------------------------------------------
            // GET APPOINTMENTS
            // ----------------------------------------------------

            $appointments = $query
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($item) {

                    return [
                        'id' => $item->id,

                        'type' =>
                            $item->service_type,

                        'name' =>
                            $item->user_name,

                        'date' =>
                            $item->appointment_date,

                        'appointment_date' =>
                            $item->appointment_date,

                        'appointment_time' =>
                            Schema::hasColumn(
                                'appointments',
                                'appointment_time'
                            )
                                ? $item->appointment_time
                                : null,

                        'scheduled_date' =>
                            $item->appointment_date,

                        'scheduled_time' =>
                            Schema::hasColumn(
                                'appointments',
                                'appointment_time'
                            )
                                ? $item->appointment_time
                                : null,

                        'is_scheduled' =>
                            !empty(
                                $item->appointment_date
                            ),

                        'status' =>
                            $item->status ?? 'pending',

                        'cancellation_reason' =>
                            null,

                        'is_locked' =>
                            false,

                        'submitted_at' =>
                            $item->created_at
                                ? date(
                                    'Y-m-d H:i:s',
                                    strtotime(
                                        $item->created_at
                                    )
                                )
                                : null,

                        'created_at' =>
                            $item->created_at,
                    ];
                });

            // ----------------------------------------------------
            // RESPONSE
            // ----------------------------------------------------

            return response()->json([
                'success' => true,
                'appointments' =>
                    $appointments->values(),
            ]);

        } catch (\Exception $e) {

            Log::error(
                'MY_APPOINTMENTS_ERROR: ' .
                $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Server error: ' .
                    $e->getMessage(),
            ], 500);
        }
    }

    // ============================================================
    // ANDROID - CONFIRMED / APPROVED APPOINTMENTS
    // ============================================================

    public function getConfirmedAppointments(
        Request $request
    ) {

        try {

            $user = $request->user();

            if (!$user) {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            $query = DB::table('appointments');

            // ----------------------------------------------------
            // FILTER USER
            // ----------------------------------------------------

            if (
                Schema::hasColumn(
                    'appointments',
                    'user_id'
                )
            ) {

                $query->where(
                    'user_id',
                    $user->id
                );

            } else {

                $query->where(
                    'user_name',
                    $user->name
                );
            }

            // ----------------------------------------------------
            // ONLY APPROVED WITH DATE
            // ----------------------------------------------------

            $query
                ->where('status', 'approved')
                ->whereNotNull('appointment_date');

            // ----------------------------------------------------
            // TIME IF COLUMN EXISTS
            // ----------------------------------------------------

            if (
                Schema::hasColumn(
                    'appointments',
                    'appointment_time'
                )
            ) {

                $query->whereNotNull(
                    'appointment_time'
                );
            }

            // ----------------------------------------------------
            // GET
            // ----------------------------------------------------

            $confirmed = $query
                ->orderByDesc('updated_at')
                ->get()
                ->map(function ($item) {

                    return [
                        'id' =>
                            $item->id,

                        'type' =>
                            $item->service_type,

                        'name' =>
                            $item->user_name,

                        'scheduled_date' =>
                            $item->appointment_date,

                        'scheduled_time' =>
                            Schema::hasColumn(
                                'appointments',
                                'appointment_time'
                            )
                                ? $item->appointment_time
                                : null,

                        'admin_notes' =>
                            $item->details,

                        'updated_at' =>
                            $item->updated_at
                                ? date(
                                    'Y-m-d H:i:s',
                                    strtotime(
                                        $item->updated_at
                                    )
                                )
                                : null,
                    ];
                });

            // ----------------------------------------------------
            // RESPONSE
            // ----------------------------------------------------

            return response()->json([
                'status' => 'success',
                'data' =>
                    $confirmed->values(),
                'count' =>
                    $confirmed->count(),
            ]);

        } catch (\Exception $e) {

            Log::error(
                'CONFIRMED_APPOINTMENTS_ERROR: ' .
                $e->getMessage()
            );

            return response()->json([
                'status' => 'error',
                'message' =>
                    'Failed to fetch confirmed appointments: ' .
                    $e->getMessage(),
            ], 500);
        }
    }
}