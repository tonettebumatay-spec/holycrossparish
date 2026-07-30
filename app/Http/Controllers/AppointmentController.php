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
                          ->orWhere('mother_name', 'LIKE', "%{$search}%");
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
                    return $q->where('candidate_name', 'LIKE', "%{$search}%");
                })
                ->when($statusFilter, function ($q, $status) {
                    return $q->where('status', $status);
                });

            if (!$typeFilter || $typeFilter === 'Communion') {
                $communions = $communionsQuery->get()->map(function ($item) {
                    $item->type = 'Communion';
                    $item->name = $item->candidate_name ?? 'N/A';
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
                    return $q->where('candidate_name', 'LIKE', "%{$search}%");
                })
                ->when($statusFilter, function ($q, $status) {
                    return $q->where('status', $status);
                });

            if (!$typeFilter || $typeFilter === 'Confirmation') {
                $confirmations = $confirmationsQuery->get()->map(function ($item) {
                    $item->type = 'Confirmation';
                    $item->name = $item->candidate_name ?? 'N/A';
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
     * Get authenticated user's appointments across all sacraments.
     */
    public function myAppointments(Request $request)
    {
        try {
            $user = $request->user();
            $appointments = collect();

            // Ginamit ang email para mahanap ang bookings ng user (Siguraduhing may 'email' column ang tables mo)
            $identifier = $user->email ?? $user->name;

            // Baptism
            $appointments = $appointments->merge(
                Baptism::where('email', $identifier)->orWhere('user_id', $user->id ?? null)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'type' => 'Baptism',
                            'name' => trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')),
                            'date' => $item->baptism_date ?? null,
                            'status' => $item->status ?? 'pending',
                            'created_at' => $item->created_at,
                        ];
                    })
            );

            // Communion
            $appointments = $appointments->merge(
                Communion::where('email', $identifier)->orWhere('user_id', $user->id ?? null)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'type' => 'Communion',
                            'name' => $item->candidate_name ?? 'N/A',
                            'date' => $item->communion_date ?? null,
                            'status' => $item->status ?? 'pending',
                            'created_at' => $item->created_at,
                        ];
                    })
            );

            // Confirmation
            $appointments = $appointments->merge(
                Confirmation::where('email', $identifier)->orWhere('user_id', $user->id ?? null)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'type' => 'Confirmation',
                            'name' => $item->candidate_name ?? 'N/A',
                            'date' => $item->confirmation_date ?? null,
                            'status' => $item->status ?? 'pending',
                            'created_at' => $item->created_at,
                        ];
                    })
            );

            // Wedding
            $appointments = $appointments->merge(
                Wedding::where('email', $identifier)->orWhere('user_id', $user->id ?? null)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'type' => 'Wedding',
                            'name' => ($item->groom_name ?? '') . ' & ' . ($item->bride_name ?? ''),
                            'date' => $item->wedding_date ?? null,
                            'status' => $item->status ?? 'pending',
                            'created_at' => $item->created_at,
                        ];
                    })
            );

            // Funeral
            $appointments = $appointments->merge(
                Funeral::where('email', $identifier)->orWhere('user_id', $user->id ?? null)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'type' => 'Funeral',
                            'name' => $item->deceased_name ?? 'N/A',
                            'date' => $item->burial_date ?? null,
                            'status' => $item->status ?? 'pending',
                            'created_at' => $item->created_at,
                        ];
                    })
            );

            return response()->json([
                'success' => true,
                'appointments' => $appointments->sortByDesc('created_at')->values(),
            ]);

        } catch (\Exception $e) {
            Log::error('MY_APPOINTMENTS_ERROR: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}