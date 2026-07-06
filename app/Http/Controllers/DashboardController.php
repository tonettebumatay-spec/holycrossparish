<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate appointment count from all sacrament models
        $appointmentCount = Baptism::count() + 
                           Communion::count() + 
                           Confirmation::count() + 
                           Wedding::count() + 
                           Funeral::count();

        // Get other counts from database
        $data = [
            'bookCount' => 5, // Hardcoded since you have 5 core sacramental books
            'massScheduleCount' => DB::table('schedules')->count() ?? 0,
            'pendingCertificatesCount' => DB::table('certificates')->count() ?? 0,
            'appointmentCount' => $appointmentCount,
            'inventoryCount' => DB::table('inventories')->count() ?? 0,
            'onlineViewingCount' => DB::table('viewing')->count() ?? 0,
        ];

        return view('dashboard', $data);
    }
}