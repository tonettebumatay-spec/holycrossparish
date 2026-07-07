<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function index()
    {
        try {
            $baptisms = Baptism::all()->map(function ($item) {
                $item->type = 'Baptism';
                $item->name = trim($item->first_name . ' ' . $item->last_name);
                $item->appointment_date = $item->baptism_date;
                $item->status = $item->status ?? 'pending';
                return $item;
            });

            $communions = Communion::all()->map(function ($item) {
                $item->type = 'Communion';
                $item->name = $item->candidate_name;
                $item->appointment_date = $item->communion_date;
                $item->status = $item->status ?? 'pending';
                return $item;
            });

            $confirmations = Confirmation::all()->map(function ($item) {
                $item->type = 'Confirmation';
                $item->name = $item->candidate_name;
                $item->appointment_date = $item->confirmation_date;
                $item->status = $item->status ?? 'pending';
                return $item;
            });

            $weddings = Wedding::all()->map(function ($item) {
                $item->type = 'Wedding';
                $item->name = $item->groom_name . ' & ' . $item->bride_name;
                $item->appointment_date = $item->year . '-' . $item->month_day;
                $item->status = $item->status ?? 'pending';
                return $item;
            });

            $funerals = Funeral::all()->map(function ($item) {
                $item->type = 'Funeral';
                $item->name = $item->deceased_name;
                $item->appointment_date = $item->burial_date;
                $item->status = $item->status ?? 'pending';
                return $item;
            });

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