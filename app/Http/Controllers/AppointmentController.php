<?php

namespace App\Http\Controllers;

use App\Models\Appointment; 
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        // Only fetch the new, pending requests
        $appointments = Appointment::all(); 

        return view('appointments.index', compact('appointments'));
    }
}