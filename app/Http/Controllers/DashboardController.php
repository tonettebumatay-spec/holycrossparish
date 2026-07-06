<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Using try-catch ensures that if a single table is missing,
            // the dashboard still loads instead of throwing a 500 error
            // which causes the redirect loop.
            
            $data = [
                'bookCount' => 5,
                'sacramentalRecordCount' => (
                    Baptism::count() + 
                    Communion::count() + 
                    Confirmation::count() + 
                    Wedding::count() + 
                    Funeral::count()
                ),
                'massScheduleCount' => DB::table('schedules')->count(),
                'pendingCertificatesCount' => DB::table('certificates')->count(),
                'appointmentCount' => Appointment::count(),
                'inventoryCount' => DB::table('inventories')->count(),
                'onlineViewingCount' => DB::table('viewings')->count(),
            ];

            return view('dashboard', $data);

        } catch (\Exception $e) {
            // If any table is missing or query fails, log the error and 
            // return the dashboard with 0 values so the user stays logged in.
            Log::error('Dashboard Error: ' . $e->getMessage());
            
            return view('dashboard', [
                'bookCount' => 0,
                'sacramentalRecordCount' => 0,
                'massScheduleCount' => 0,
                'pendingCertificatesCount' => 0,
                'appointmentCount' => 0,
                'inventoryCount' => 0,
                'onlineViewingCount' => 0,
            ]);
        }
    }
}