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
            // Helper function to extract time from remarks
            $extractTime = function ($remarks) {
                if (empty($remarks)) return null;
                preg_match('/Time:\s*([^|]+)/i', $remarks, $matches);
                return isset($matches[1]) ? trim($matches[1]) : null;
            };

            // Baptisms
            $baptisms = Baptism::all()->map(function ($item) use ($extractTime) {
                $item->type = 'Baptism';
                $item->name = trim($item->first_name . ' ' . $item->last_name);
                $item->appointment_date = $item->baptism_date ? Carbon::parse($item->baptism_date)->format('Y-m-d') : null;
                $item->time = $extractTime($item->remarks);
                $item->status = $item->status ?? 'pending';
                return $item;
            });

            // Communions
            $communions = Communion::all()->map(function ($item) use ($extractTime) {
                $item->type = 'Communion';
                $item->name = $item->candidate_name;
                $item->appointment_date = $item->communion_date ? Carbon::parse($item->communion_date)->format('Y-m-d') : null;
                $item->time = $extractTime($item->remarks);
                $item->status = $item->status ?? 'pending';
                return $item;
            });

            // Confirmations
            $confirmations = Confirmation::all()->map(function ($item) use ($extractTime) {
                $item->type = 'Confirmation';
                $item->name = $item->candidate_name;
                $item->appointment_date = $item->confirmation_date ? Carbon::parse($item->confirmation_date)->format('Y-m-d') : null;
                $item->time = $extractTime($item->remarks);
                $item->status = $item->status ?? 'pending';
                return $item;
            });

            // Weddings
            $weddings = Wedding::all()->map(function ($item) use ($extractTime) {
                $item->type = 'Wedding';
                $item->name = $item->groom_name . ' & ' . $item->bride_name;
                $item->status = $item->status ?? 'pending';

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

            return view('appointments.index', ['appointments' => $allAppointments]);

        } catch (\Exception $e) {
            Log::error('Appointment Index Error: ' . $e->getMessage());
            return view('appointments.index', ['appointments' => collect()]);
        }
    }

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
}