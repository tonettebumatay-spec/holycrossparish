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
            // ==================== DATA QUERIES ====================
            $today = now()->toDateString();

            // ✅ ACTIVE APPOINTMENTS (Pareho sa logic ng Appointments Page)
            // Kukunin lang natin yung mga walang appointment date (pending) 
            // O kaya yung may appointment date na today o future, at hindi cancelled/expired.
            $appointmentCount = Appointment::query()
                ->where(function ($query) use ($today) {
                    // 1. Walang petsa (Pending)
                    $query->whereNull('appointment_date')
                          ->orWhere(function ($q) use ($today) {
                              // 2. May petsa, today o future
                              $q->where('appointment_date', '>=', $today)
                                ->whereNotIn('status', ['cancelled', 'expired']);
                          });
                })
                ->whereNotIn('status', ['cancelled', 'expired'])
                ->count();

            // ✅ CALENDAR EVENTS (Active Events lang)
            $calendarEventCount = Appointment::whereNotNull('appointment_date')
                ->where('appointment_date', '>=', $today)
                ->whereNotIn('status', ['cancelled', 'expired'])
                ->count() 
                + DB::table('schedules')->where('status', 'pending')->count();

            $paymentCount = PayMongoPayment::count() + Payment::count();
            $supportCount = SupportConversation::where('is_archived', false)->count();

            // ✅ Marriage Banns Count
            MarriageBann::updateExpiredStatus();
            $bannsCount = MarriageBann::where('status', 'active')->count();

            // ==================== CARDS ARRAY ====================
            $cards = [
                [
                    'title' => 'Indexed Books',
                    'count' => 5,
                    'icon' => '📖',
                    'link_text' => 'Open Records',
                    'link_url' => route('records.index')
                ],
                [
                    'title' => 'Mass Schedules',
                    'count' => DB::table('schedules')->where('status', 'pending')->whereDate('date', '>=', $today)->count(),
                    'icon' => '⛪',
                    'link_text' => 'View Schedules',
                    'link_url' => route('schedules.index')
                ],
                [
                    'title' => 'Certificates',
                    'count' => DB::table('certificates')->where('status', 'pending')->count() ?? 0,
                    'icon' => '📜',
                    'link_text' => 'Pending Requests',
                    'link_url' => route('certificates.index')
                ],
                [
                    'title' => 'Appointments',
                    'count' => $appointmentCount, // ✅ Tamang bilang na
                    'icon' => '📅',
                    'link_text' => 'Manage Bookings',
                    'link_url' => route('appointments.index')
                ],
                [
                    'title' => 'Payment Records',
                    'count' => $paymentCount,
                    'icon' => '💳',
                    'link_text' => 'View Payments',
                    'link_url' => route('payments.index')
                ],
                [
                    'title' => 'Calendar',
                    'count' => $calendarEventCount, // ✅ Tamang bilang na
                    'icon' => '🗓️',
                    'link_text' => 'View Calendar',
                    'link_url' => route('calendar.index')
                ],
                [
                    'title' => 'Customer Support',
                    'count' => $supportCount,
                    'icon' => '🎧',
                    'link_text' => 'View Messages',
                    'link_url' => route('support.index')
                ],
                [
                    'title' => 'Marriage Banns',
                    'count' => $bannsCount,
                    'icon' => '💍',
                    'link_text' => 'View Banns',
                    'link_url' => route('banns.index')
                ]
            ];

            return view('dashboard', compact('cards'));

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return view('dashboard', ['cards' => []]);
        }
    }
}