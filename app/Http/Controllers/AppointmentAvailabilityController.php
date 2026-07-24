<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentAvailability;

class AppointmentAvailabilityController extends Controller
{
    public function apiGetSlots($sacrament)
    {
        $slots = AppointmentAvailability::where('sacrament_type', $sacrament)
            ->where('available_date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->orderBy('available_date')
            ->get(['available_date', 'start_time', 'end_time', 'max_slots']);

        return response()->json([
            'success' => true,
            'data' => $slots
        ]);
    }
}