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
            // Sacramental records count (archives/books)
            // Mula sa 5 sacrament tables
            $sacramentalRecordCount = Baptism::count()
                + Communion::count()
                + Confirmation::count()
                + Wedding::count()
                + Funeral::count();

            // Appointments count (bookings)
            // Mula sa central appointments table
            $appointmentCount = Appointment::count();

            $data = [
                'bookCount' => 5,

                'sacramentalRecordCount' => $sacramentalRecordCount,

                // Count only active/pending schedules
                // whose date is today or in the future
                'massScheduleCount' => DB::table('schedules')
                    ->where('status', 'pending')
                    ->whereDate('date', '>=', now()->toDateString())
                    ->count(),

                'pendingCertificatesCount' => DB::table('certificates')->count() ?? 0,

                'appointmentCount' => $appointmentCount,

                'inventoryCount' => DB::table('inventories')->count() ?? 0,

                'onlineViewingCount' => DB::table('viewings')->count() ?? 0,
            ];

            return view('dashboard', $data);

        } catch (\Exception $e) {
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