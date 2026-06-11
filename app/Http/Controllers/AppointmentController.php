<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment; // Siguraduhin na may model ka nito later

class AppointmentController extends Controller
{
    public function index()
    {
        // Pansamantala, static muna para makita ang design
        return view('appointments.index');
    }
}