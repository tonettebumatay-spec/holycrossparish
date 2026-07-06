<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        // Fetching data from all tables
        $baptisms = Baptism::all();
        $communions = Communion::all();
        $confirmations = Confirmation::all();
        $weddings = Wedding::all();
        $funerals = Funeral::all();


    // Collect all appointments from all five tables
    $appointments = collect()
        ->concat(\App\Models\Baptism::all())
        ->concat(\App\Models\Communion::all())
        ->concat(\App\Models\Confirmation::all())
        ->concat(\App\Models\Wedding::all())
        ->concat(\App\Models\Funeral::all());

    return view('appointments.index', compact('appointments'));
}

    // Keep your existing create() and store() methods below...
}