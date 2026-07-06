<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments for the dashboard.
     */
    public function index()
    {
        // Fetch all appointments to display on your web portal
        $appointments = Appointment::all(); 
        return view('appointments.index', compact('appointments'));
    }

    /**
     * Handle the POST request from the Android App
     */
    public function store(Request $request)
    {
        // 1. Log incoming request for debugging purposes (check storage/logs/laravel.log)
        Log::info('Android Appointment Request:', $request->all());

        // 2. Validate incoming data
        // Ensure keys match your Android @Field definitions
        $validated = $request->validate([
            'user_name'        => 'required|string|max:255',
            'service_type'     => 'required|string|max:255',
            'appointment_date' => 'required|date_format:Y-m-d H:i',
            'contact_number'   => 'required|string|max:20', // Matches your DB column
            'details'          => 'nullable|string',
        ]);

        // 3. Set default status if not provided
        $validated['status'] = $validated['status'] ?? 'pending';

        try {
            // 4. Save to the database
            Appointment::create($validated);

            // 5. Return success to the Android app
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