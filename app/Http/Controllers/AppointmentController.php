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
     * Supports search, status filter, and type filter.
     */
    public function index(Request $request)
    {
        try {
            // Get filter inputs from the request
            $search = $request->input('search');
            $statusFilter = $request->input('status');
            $typeFilter = $request->input('type');

            // Helper to extract time from remarks
            $extractTime = function ($remarks) {
                if (empty($remarks)) return null;

                $patterns = [
                    '/Time:\s*([^|\n]+)/i',
                    '/\b(\d{1,2}:\d{2}\s*(?:AM|PM)?)\b/i',
                    '/at\s*(\d{1,2}:\d{2}\s*(?:AM|PM)?)/i',
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $remarks, $matches)) {
                        return trim($matches[1]);
                    }
                }
                return null;
            };

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
                $baptisms = $baptismsQuery->get()->map(function ($item) use ($extractTime) {
                    $item->type = 'Baptism';
                    $item->name = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
                    if (empty($item->name)) $item->name = 'N/A';
                    $item->appointment_date = $item->baptism_date ? Carbon::parse($item->baptism_date)->format('Y-m-d') : null;
                    $item->time = $extractTime($item->remarks);
                    $item->status = $item->status ?? 'pending';
                    $item->category = $item->category ?? 'Baptism';
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d h:i A') : 'N/A';
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
                $communions = $communionsQuery->get()->map(function ($item) use ($extractTime) {
                    $item->type = 'Communion';
                    $item->name = $item->candidate_name ?? 'N/A';
                    $item->appointment_date = $item->communion_date ? Carbon::parse($item->communion_date)->format('Y-m-d') : null;
                    $item->time = $extractTime($item->remarks);
                    $item->status = $item->status ?? 'pending';
                    $item->category = $item->category ?? 'Communion';
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d h:i A') : 'N/A';
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
                $confirmations = $confirmationsQuery->get()->map(function ($item) use ($extractTime) {
                    $item->type = 'Confirmation';
                    $item->name = $item->candidate_name ?? 'N/A';
                    $item->appointment_date = $item->confirmation_date ? Carbon::parse($item->confirmation_date)->format('Y-m-d') : null;
                    $item->time = $extractTime($item->remarks);
                    $item->status = $item->status ?? 'pending';
                    $item->category = $item->category ?? 'Confirmation';
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d h:i A') : 'N/A';
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
                $weddings = $weddingsQuery->get()->map(function ($item) use ($extractTime) {
                    $item->type = 'Wedding';
                    $groom = $item->groom_name ?? '';
                    $bride = $item->bride_name ?? '';
                    $item->name = ($groom ?: '') . ($groom && $bride ? ' & ' : '') . ($bride ?: '');
                    if (empty($item->name)) $item->name = 'N/A';
                    $item->status = $item->status ?? 'pending';
                    $item->category = $item->category ?? 'Wedding';

                    $year = $item->year;
                    $monthDay = $item->month_day;
                    if ($year && $monthDay) {
                        try {
                            $date = Carbon::parse("$year $monthDay")->format('Y-m-d');
                        } catch (\Exception $e) {
                            try {
                                $date = Carbon::parse("$year-$monthDay")->format('Y-m-d');
                            } catch (\Exception $e2) {
                                $date = null;
                            }
                        }
                    } else {
                        $date = null;
                    }
                    $item->appointment_date = $date;
                    $item->time = $extractTime($item->remarks);
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d h:i A') : 'N/A';
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
                $funerals = $funeralsQuery->get()->map(function ($item) use ($extractTime) {
                    $item->type = 'Funeral';
                    $item->name = $item->deceased_name ?? 'N/A';
                    $item->appointment_date = $item->burial_date ? Carbon::parse($item->burial_date)->format('Y-m-d') : null;
                    $item->time = $extractTime($item->remarks);
                    $item->status = $item->status ?? 'pending';
                    $item->category = $item->category ?? 'Funeral';
                    $item->submitted_at = $item->created_at ? $item->created_at->format('Y-m-d h:i A') : 'N/A';
                    $item->cancellation_reason = $item->cancellation_reason ?? null;
                    $item->is_locked = $item->is_locked ?? false;
                    return $item;
                });
            } else {
                $funerals = collect();
            }

            // Merge all collections and sort by appointment_date (newest first)
            $allAppointments = collect()
                ->merge($baptisms)
                ->merge($communions)
                ->merge($confirmations)
                ->merge($weddings)
                ->merge($funerals)
                ->sortByDesc('appointment_date')
                ->values();

            Log::info('AppointmentController total: ' . $allAppointments->count());

            // Pass filter values back to the view for retaining form values
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

        // Prevent updating if locked
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

    // Prevent cancellation if already cancelled or locked
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

        // Prevent deletion if locked (optional)
        if ($record->is_locked) {
            return back()->with('error', 'Locked appointments cannot be deleted.');
        }

        $record->delete();

        return back()->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Fallback store method (not used for Android – handled by BookingController).
     */
    public function store(Request $request)
    {
        return response()->json(['status' => 'error', 'message' => 'Use booking endpoints'], 400);
    }
}