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
            // Calculate total appointments from all sacrament tables
            $appointmentCount = Baptism::count() + 
                                Communion::count() + 
                                Confirmation::count() + 
                                Wedding::count() + 
                                Funeral::count();

            // If you also have a separate `Appointment` model for other purposes,
            // you can add it as well:
            // $appointmentCount += Appointment::count();

            $data = [
                'bookCount' => 5,
                'sacramentalRecordCount' => $appointmentCount, // same as appointments total
                'massScheduleCount' => DB::table('schedules')->count() ?? 0,
                'pendingCertificatesCount' => DB::table('certificates')->count() ?? 0,
                'appointmentCount' => $appointmentCount, // now includes Android bookings
                'inventoryCount' => DB::table('inventories')->count() ?? 0,
                'onlineViewingCount' => DB::table('viewings')->count() ?? 0,
            ];

            return view('dashboard', $data);

        } catch (\Exception $e) {
            // If any table is missing, log the error and return 0 values
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