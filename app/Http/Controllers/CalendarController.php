<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index()
    {
        return view('calendar.index');
    }

    /**
     * API: Get all events for FullCalendar.
     */
    public function events(Request $request)
    {
        try {
            $events = collect();

            // ==================== MASS SCHEDULES ====================
            $schedules = DB::table('schedules')
                ->where('status', 'pending')
                ->orWhereNull('status')
                ->get();

            foreach ($schedules as $schedule) {
                $date = $schedule->date ?? null;
                if (!$date) continue;

                // ✅ Format the date para maging Y-m-d
                $dateFormatted = \Carbon\Carbon::parse($date)->format('Y-m-d');
                $time = $schedule->time ?? '08:00:00';

                $events->push([
                    'id' => 'schedule-' . $schedule->id,
                    'title' => '⛪ ' . ($schedule->title ?? 'Mass Schedule'),
                    'start' => $dateFormatted . 'T' . $time,
                    'color' => '#3B82F6', // Blue
                    'extendedProps' => [
                        'type' => 'schedule',
                        'description' => $schedule->description ?? 'Mass schedule',
                        'time' => $time,
                    ],
                    'url' => route('schedules.index'),
                ]);
            }

            // ==================== APPOINTMENTS ====================
            $appointments = Appointment::whereNotNull('appointment_date')
                ->whereNotNull('appointment_time')
                ->get();

            foreach ($appointments as $appointment) {
                $color = match ($appointment->status) {
                    'approved' => '#10B981', // Green
                    'pending' => '#F59E0B',  // Amber
                    'cancelled', 'canceled' => '#EF4444', // Red
                    'expired' => '#6B7280',  // Gray
                    default => '#6B7280',
                };

                // ✅ Format the date para maging Y-m-d
                $dateFormatted = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
                $time = $appointment->appointment_time;

                $events->push([
                    'id' => 'appointment-' . $appointment->id,
                    'title' => '📅 ' . ucfirst($appointment->service_type ?? 'Appointment') . ' - ' . ($appointment->user_name ?? 'N/A'),
                    'start' => $dateFormatted . 'T' . $time,
                    'color' => $color,
                    'extendedProps' => [
                        'type' => 'appointment',
                        'status' => $appointment->status,
                        'service_type' => $appointment->service_type,
                        'user_name' => $appointment->user_name,
                        'contact_number' => $appointment->contact_number,
                        'time' => $time,
                    ],
                    'url' => route('appointments.index'),
                ]);
            }

            return response()->json($events);

        } catch (\Exception $e) {
            Log::error('CALENDAR_EVENTS_ERROR: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}