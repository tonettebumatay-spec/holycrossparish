<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use App\Models\Appointment;
use App\Models\PayMongoPayment;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // ==================== SACRAMENTAL RECORDS ====================
            // Mula sa 5 sacrament tables
            $sacramentalRecordCount = Baptism::count()
                + Communion::count()
                + Confirmation::count()
                + Wedding::count()
                + Funeral::count();

            // ==================== APPOINTMENTS ====================
            $appointmentCount = Appointment::count();

            // ==================== PAYMENT RECORDS (GCash + Cash) ====================
            $paymongoCount = PayMongoPayment::count();
            $cashCount = Payment::count();
            $paymentCount = $paymongoCount + $cashCount;

            // ==================== DASHBOARD DATA ====================
            $data = [
                'bookCount' => 5,

                'sacramentalRecordCount' => $sacramentalRecordCount,

                // Count only active/pending schedules
                // whose date is today or in the future
                'massScheduleCount' => DB::table('schedules')
                    ->where('status', 'pending')
                    ->whereDate('date', '>=', now()->toDateString())
                    ->count(),

                'pendingCertificatesCount' => DB::table('certificates')
                    ->where('status', 'pending')
                    ->count() ?? 0,

                'appointmentCount' => $appointmentCount,

                // ✅ Payment Records count (GCash + Cash)
                'paymentCount' => $paymentCount,

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
                'paymentCount' => 0,
                'onlineViewingCount' => 0,
            ]);
        }
    }
}