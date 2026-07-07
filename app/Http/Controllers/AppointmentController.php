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
            // -----------------------------------------------------------------
            // 1. FETCH ALL RECORDS FROM EACH SACRAMENT TABLE
            // -----------------------------------------------------------------
            $baptisms = Baptism::all()->map(function ($item) {
                $item->type = 'Baptism';
                $item->name = trim($item->first_name . ' ' . $item->last_name);
                $item->appointment_date = $item->baptism_date;
                return $item;
            });

            $communions = Communion::all()->map(function ($item) {
                $item->type = 'Communion';
                $item->name = $item->candidate_name;
                $item->appointment_date = $item->communion_date;
                return $item;
            });

            $confirmations = Confirmation::all()->map(function ($item) {
                $item->type = 'Confirmation';
                $item->name = $item->candidate_name;
                $item->appointment_date = $item->confirmation_date;
                return $item;
            });

            $weddings = Wedding::all()->map(function ($item) {
                $item->type = 'Wedding';
                $item->name = $item->groom_name . ' & ' . $item->bride_name;
                // Build date from year and month_day
                $item->appointment_date = $item->year . '-' . $item->month_day;
                return $item;
            });

            $funerals = Funeral::all()->map(function ($item) {
                $item->type = 'Funeral';
                $item->name = $item->deceased_name;
                $item->appointment_date = $item->burial_date;
                return $item;
            });

            // -----------------------------------------------------------------
            // 2. MERGE ALL COLLECTIONS AND SORT
            // -----------------------------------------------------------------
            $allAppointments = collect()
                ->merge($baptisms)
                ->merge($communions)
                ->merge($confirmations)
                ->merge($weddings)
                ->merge($funerals)
                ->sortByDesc('appointment_date')
                ->values();

            // Log the count for debugging
            Log::info('AppointmentController found ' . $allAppointments->count() . ' total appointments.');

            // -----------------------------------------------------------------
            // 3. RETURN TO VIEW
            // -----------------------------------------------------------------
            return view('appointments.index', ['appointments' => $allAppointments]);

        } catch (\Exception $e) {
            // Log the full error
            Log::error('AppointmentController error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Return empty collection
            return view('appointments.index', ['appointments' => collect()]);
        }
    }

    public function store(Request $request)
    {
        return response()->json(['status' => 'error', 'message' => 'Use booking endpoints'], 400);
    }
}