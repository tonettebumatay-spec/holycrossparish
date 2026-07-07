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
    public function index()
    {
        try {
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

            // Baptisms
            $baptisms = Baptism::all()->map(function ($item) use ($extractTime) {
                $item->type = 'Baptism';
                $item->name = trim($item->first_name . ' ' . $item->last_name);
                $item->appointment_date = $item->baptism_date ? Carbon::parse($item->baptism_date)->format('Y-m-d') : null;
                $item->time = $extractTime($item->remarks);
                $item->status = $item->status ?? 'pending';
                $item->category = $item->category ?? 'Baptism';
                return $item;
            });

            // Communions
            $communions = Communion::all()->map(function ($item) use ($extractTime) {
                $item->type = 'Communion';
                $item->name = $item->candidate_name;
                $item->appointment_date = $item->communion_date ? Carbon::parse($item->communion_date)->format('Y-m-d') : null;
                $item->time = $extractTime($item->remarks);
                $item->status = $item->status ?? 'pending';
                $item->category = $item->category ?? 'Communion';
                return $item;
            });

            // Confirmations
            $confirmations = Confirmation::all()->map(function ($item) use ($extractTime) {
                $item->type = 'Confirmation';
                $item->name = $item->candidate_name;
                $item->appointment_date = $item->confirmation_date ? Carbon::parse($item->confirmation_date)->format('Y-m-d') : null;
                $item->time = $extractTime($item->remarks);
                $item->status = $item->status ?? 'pending';
                $item->category = $item->category ?? 'Confirmation';
                return $item;
            });

            // Weddings – handle year+month_day
            $weddings = Wedding::all()->map(function ($item) use ($extractTime) {
                $item->type = 'Wedding';
                $item->name = $item->groom_name . ' & ' . $item->bride_name;
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
                return $item;
            });

            // Funerals
            $funerals = Funeral::all()->map(function ($item) use ($extractTime) {
                $item->type = 'Funeral';
                $item->name = $item->deceased_name;
                $item->appointment_date = $item->burial_date ? Carbon::parse($item->burial_date)->format('Y-m-d') : null;
                $item->time = $extractTime($item->remarks);
                $item->status = $item->status ?? 'pending';
                $item->category = $item->category ?? 'Funeral';
                return $item;
            });

            // Merge and sort
            $allAppointments = collect()
                ->merge($baptisms)
                ->merge($communions)
                ->merge($confirmations)
                ->merge($weddings)
                ->merge($funerals)
                ->sortByDesc('appointment_date')
                ->values();

            Log::info('AppointmentController total: ' . $allAppointments->count());

            return view('appointments.index', ['appointments' => $allAppointments]);

        } catch (\Exception $e) {
            Log::error('Appointment Index Error: ' . $e->getMessage());
            return view('appointments.index', ['appointments' => collect()]);
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
        $record->status = $request->status;
        $record->save();

        return back()->with('success', 'Appointment status updated.');
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