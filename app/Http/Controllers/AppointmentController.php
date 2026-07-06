<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments (For the Dashboard view)
     */
    public function index()
    {
        $appointments = Appointment::all(); 
        return view('appointments.index', compact('appointments'));
    }

    /**
     * Handle the POST request from Android
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming data from Android
        // Adjust these field names to match exactly what your Android app sends
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'service_type' => 'required|string|max:255',
            'service_date' => 'required|date',
        ]);

        // 2. Save to the database
        Appointment::create($validated);

        // 3. Return JSON response for the Android app
        return response()->json([
            'status' => 'success',
            'message' => 'Appointment booked successfully'
        ], 201);
    }
}