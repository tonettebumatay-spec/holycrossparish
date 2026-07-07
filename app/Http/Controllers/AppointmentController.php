<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    /**
     * Display a listing of all appointments from all sacrament tables.
     */
    public function index()
    {
        try {
            // Query each table and union them
            $baptisms = DB::table('baptisms')
                ->select(
                    DB::raw("'Baptism' as type"),
                    'id',
                    DB::raw("CONCAT(first_name, ' ', last_name) as name"),
                    'baptism_date as appointment_date',
                    'category',
                    'created_at',
                    'remarks'
                );

            $communions = DB::table('communions')
                ->select(
                    DB::raw("'Communion' as type"),
                    'id',
                    'candidate_name as name',
                    'communion_date as appointment_date',
                    'category',
                    'created_at',
                    'remarks'
                );

            $confirmations = DB::table('confirmations')
                ->select(
                    DB::raw("'Confirmation' as type"),
                    'id',
                    'candidate_name as name',
                    'confirmation_date as appointment_date',
                    'category',
                    'created_at',
                    'remarks'
                );

            $weddings = DB::table('weddings')
                ->select(
                    DB::raw("'Wedding' as type"),
                    'id',
                    DB::raw("CONCAT(groom_name, ' & ', bride_name) as name"),
                    'wedding_date as appointment_date',
                    'category',
                    'created_at',
                    'remarks'
                );

            $funerals = DB::table('funerals')
                ->select(
                    DB::raw("'Funeral' as type"),
                    'id',
                    'deceased_name as name',
                    'burial_date as appointment_date',
                    'category',
                    'created_at',
                    'remarks'
                );

            // Union all queries and order by appointment date (newest first)
            $appointments = $baptisms
                ->unionAll($communions)
                ->unionAll($confirmations)
                ->unionAll($weddings)
                ->unionAll($funerals)
                ->orderBy('appointment_date', 'desc')
                ->get();

            // Log the count for debugging
            Log::info('AppointmentController fetched ' . $appointments->count() . ' appointments from union query.');

            return view('appointments.index', ['appointments' => $appointments]);

        } catch (\Exception $e) {
            Log::error('Appointment Index Error: ' . $e->getMessage());

            // On error, return empty collection so the page shows "No appointments"
            return view('appointments.index', ['appointments' => collect()]);
        }
    }

    /**
     * Fallback store method (not used for Android bookings – handled by BookingController).
     */
    public function store(Request $request)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Use the booking endpoints (/booking/*) instead'
        ], 400);
    }
}