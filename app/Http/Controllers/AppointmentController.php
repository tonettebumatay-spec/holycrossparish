<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
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
        // 1. Validate the incoming data to match your Model's $fillable fields
        $validated = $request->validate([
            'user_name'        => 'required|string|max:255',
            'service_type'     => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'contact_number'   => 'required|string|max:20',
            'details'          => 'nullable|string',
            'status'           => 'nullable|string', // Default to 'pending' if not provided
        ]);

        // 2. Set a default status if the Android app doesn't send one
        if (!isset($validated['status'])) {
            $validated['status'] = 'pending';
        }

        // 3. Save to the database
        Appointment::create($validated);

        // 4. Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Appointment request sent successfully'
        ], 201);
    }
}