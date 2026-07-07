<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use App\Models\Appointment;
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
            // Fetch all bookings from each sacrament table and map to a unified structure
            $baptisms = Baptism::select(
                'id',
                'first_name',
                'last_name',
                'baptism_date as date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Baptism';
                $item->name = trim($item->first_name . ' ' . $item->last_name);
                return $item;
            });

            $communions = Communion::select(
                'id',
                'candidate_name as name',
                'communion_date as date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Communion';
                $item->last_name = '';
                return $item;
            });

            $confirmations = Confirmation::select(
                'id',
                'candidate_name as name',
                'confirmation_date as date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Confirmation';
                $item->last_name = '';
                return $item;
            });

            $weddings = Wedding::select(
                'id',
                'groom_name as name',
                'bride_name as last_name',
                'wedding_date as date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Wedding';
                $item->name = $item->name . ' & ' . $item->last_name;
                return $item;
            });

            $funerals = Funeral::select(
                'id',
                'deceased_name as name',
                'burial_date as date',
                'category',
                'created_at',
                'remarks'
            )->get()->map(function ($item) {
                $item->type = 'Funeral';
                $item->last_name = '';
                return $item;
            });

            // Merge all collections and sort by date (newest first)
            $appointments = collect()
                ->merge($baptisms)
                ->merge($communions)
                ->merge($confirmations)
                ->merge($weddings)
                ->merge($funerals)
                ->sortByDesc('date')
                ->values();

            return view('appointments.index', compact('appointments'));

        } catch (\Exception $e) {
            Log::error('Appointment Index Error: ' . $e->getMessage());
            
            // Fallback: try using the Appointment model if it exists
            try {
                $appointments = Appointment::all();
                return view('appointments.index', compact('appointments'));
            } catch (\Exception $e2) {
                $appointments = collect();
                return view('appointments.index', compact('appointments'));
            }
        }
    }

    /**
     * Handle the POST request from the Android App (fallback).
     * Note: The actual booking is handled by BookingController.
     */
    public function store(Request $request)
    {
        Log::info('Android Appointment Request:', $request->all());

        $validated = $request->validate([
            'user_name'        => 'required|string|max:255',
            'service_type'     => 'required|string|max:255',
            'appointment_date' => 'required|date_format:Y-m-d H:i',
            'contact_number'   => 'required|string|max:20',
            'details'          => 'nullable|string',
        ]);

        $validated['status'] = $validated['status'] ?? 'pending';

        try {
            Appointment::create($validated);
            return response()->json([
                'status' => 'success',
                'message' => 'Appointment request sent successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Appointment Save Failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => 'Failed to save appointment'
            ], 500);
        }
    }
}