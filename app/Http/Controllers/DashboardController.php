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
use App\Models\SupportConversation;
use App\Models\MarriageBann;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // ==================== SACRAMENTAL RECORDS ====================
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

            // ==================== CALENDAR EVENTS ====================
            $calendarEventCount = Appointment::whereNotNull('appointment_date')->count()
                + DB::table('schedules')->where('status', 'pending')->count();

            // ==================== SUPPORT CONVERSATIONS ====================
            $supportCount = SupportConversation::where('is_archived', false)->count();

            // ==================== MARRIAGE BANNS ====================
            MarriageBann::updateExpiredStatus();
            $bannsCount = MarriageBann::where('status', 'active')->count();

            // ==================== DASHBOARD DATA ====================
            $data = [
                'bookCount' => 5,

                'sacramentalRecordCount' => $sacramentalRecordCount,

                'massScheduleCount' => DB::table('schedules')
                    ->where('status', 'pending')
                    ->whereDate('date', '>=', now()->toDateString())
                    ->count(),

                'pendingCertificatesCount' => DB::table('certificates')
                    ->where('status', 'pending')
                    ->count() ?? 0,

                'appointmentCount' => $appointmentCount,

                'paymentCount' => $paymentCount,

                'onlineViewingCount' => DB::table('viewings')->count() ?? 0,

                'calendarEventCount' => $calendarEventCount,

                'supportCount' => $supportCount,

                // ✅ BAGO: Marriage Banns Count
                'bannsCount' => $bannsCount,
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
                'calendarEventCount' => 0,
                'supportCount' => 0,
                'bannsCount' => 0,
            ]);
        }
    }
}