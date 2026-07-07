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
    /**
     * Display a listing of all appointments from all sacrament tables.
     */
    public function index()
    {
        try {
            Log::info('AppointmentController: Fetching all appointments from sacrament tables');

            // --- FETCH FROM BAPTISMS ---
            $baptisms = Baptism::select(
                'id',
                'first_name',
                'last_name',
                'baptism_date as appointment_date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Baptism';
                $item->name = trim($item->first_name . ' ' . $item->last_name);
                return $item;
            });

            // --- FETCH FROM COMMUNIONS ---
            $communions = Communion::select(
                'id',
                'candidate_name as name',
                'communion_date as appointment_date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Communion';
                $item->name = $item->name;
                return $item;
            });

            // --- FETCH FROM CONFIRMATIONS ---
            $confirmations = Confirmation::select(
                'id',
                'candidate_name as name',
                'confirmation_date as appointment_date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Confirmation';
                $item->name = $item->name;
                return $item;
            });

            // --- FETCH FROM WEDDINGS ---
            $weddings = Wedding::select(
                'id',
                'groom_name as groom',
                'bride_name as bride',
                'wedding_date as appointment_date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Wedding';
                $item->name = $item->groom . ' & ' . $item->bride;
                return $item;
            });

            // --- FETCH FROM FUNERALS ---
            $funerals = Funeral::select(
                'id',
                'deceased_name as name',
                'burial_date as appointment_date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Funeral';
                $item->name = $item->name;
                return $item;
            });

            // --- MERGE ALL COLLECTIONS ---
            $allAppointments = collect()
                ->merge($baptisms)
                ->merge($communions)
                ->merge($confirmations)
                ->merge($weddings)
                ->merge($funerals)
                ->sortByDesc('appointment_date')
                ->values();

            Log::info('AppointmentController: Found ' . $allAppointments->count() . ' total appointments');

            return view('appointments.index', ['appointments' => $allAppointments]);

        } catch (\Exception $e) {
            Log::error('Appointment Index Error: ' . $e->getMessage());
            
            // Return empty collection on error
            return view('appointments.index', ['appointments' => collect()]);
        }
    }

    /**
     * Handle the POST request from the Android App (deprecated/fallback).
     */
    public function store(Request $request)
    {
        Log::info('Appointment store called from Android:', $request->all());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Use the booking endpoints (/booking/*) instead'
        ], 400);
    }
}