<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function index()
    {
        try {
            // Union all sacrament tables
            $appointments = DB::table('baptisms')
                ->select(
                    DB::raw("'Baptism' as type"),
                    'id',
                    DB::raw("CONCAT(first_name, ' ', last_name) as name"),
                    'baptism_date as appointment_date',
                    'category',
                    'created_at',
                    'remarks'
                )
                ->unionAll(
                    DB::table('communions')
                        ->select(
                            DB::raw("'Communion' as type"),
                            'id',
                            'candidate_name as name',
                            'communion_date as appointment_date',
                            'category',
                            'created_at',
                            'remarks'
                        )
                )
                ->unionAll(
                    DB::table('confirmations')
                        ->select(
                            DB::raw("'Confirmation' as type"),
                            'id',
                            'candidate_name as name',
                            'confirmation_date as appointment_date',
                            'category',
                            'created_at',
                            'remarks'
                        )
                )
                ->unionAll(
                    DB::table('weddings')
                        ->select(
                            DB::raw("'Wedding' as type"),
                            'id',
                            DB::raw("CONCAT(groom_name, ' & ', bride_name) as name"),
                            'wedding_date as appointment_date',
                            'category',
                            'created_at',
                            'remarks'
                        )
                )
                ->unionAll(
                    DB::table('funerals')
                        ->select(
                            DB::raw("'Funeral' as type"),
                            'id',
                            'deceased_name as name',
                            'burial_date as appointment_date',
                            'category',
                            'created_at',
                            'remarks'
                        )
                )
                ->orderBy('appointment_date', 'desc')
                ->get();

            Log::info('Appointments fetched: ' . $appointments->count());

            return view('appointments.index', ['appointments' => $appointments]);

        } catch (\Exception $e) {
            Log::error('Appointment Index Error: ' . $e->getMessage());
            return view('appointments.index', ['appointments' => collect()]);
        }
    }

    public function store(Request $request)
    {
        return response()->json(['status' => 'error', 'message' => 'Use booking endpoints'], 400);
    }
}