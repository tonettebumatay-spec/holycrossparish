<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use App\Models\Appointment; // Ensure this model exists
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Count ONLY the new pending appointments
        $appointmentCount = Appointment::count() ?? 0;

        // 2. Count your total sacramental records (History)
        $totalSacraments = Baptism::count() + 
                           Communion::count() + 
                           Confirmation::count() + 
                           Wedding::count() + 
                           Funeral::count();

        // 3. Get other counts safely
        $data = [
            'bookCount' => 5,
            'sacramentalRecordCount' => $totalSacraments, // Renamed for clarity
            'massScheduleCount' => DB::table('schedules')->count() ?? 0,
            'pendingCertificatesCount' => DB::table('certificates')->count() ?? 0,
            'appointmentCount' => $appointmentCount,
            'inventoryCount' => DB::table('inventories')->count() ?? 0,
            'onlineViewingCount' => DB::table('viewings')->count() ?? 0,
        ];

        return view('dashboard', $data);
    }
}