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

        // Combine into one collection for easier handling in the view
        $appointments = collect()
            ->concat($baptisms)
            ->concat($communions)
            ->concat($confirmations)
            ->concat($weddings)
            ->concat($funerals)
            ->sortByDesc('created_at'); // Sort by most recent

        return view('appointments.index', compact('appointments'));
    }

    // Keep your existing create() and store() methods below...
}