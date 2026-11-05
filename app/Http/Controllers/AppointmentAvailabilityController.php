<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentAvailability;
use Illuminate\Support\Facades\DB;

class AppointmentAvailabilityController extends Controller
{
    /**
     * API: return active availability slots for a specific date.
     */
    public function apiGetSlots(Request $request, $sacrament = null)
    {
        $date = $request->query('date');

        $query = AppointmentAvailability::where('is_active', true);

        if (!empty($date)) {
            $query->where('available_date', $date);
        } else {
            $query->where('available_date', '>=', now()->toDateString());
        }

        $slots = $query
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->get();

        $data = $slots->map(function ($slot) {
            if ($slot->available_date instanceof \DateTimeInterface) {
                $dateStr = $slot->available_date->format('Y-m-d');
            } else {
                $dateStr = (string) $slot->available_date;
            }

            if ($slot->start_time instanceof \DateTimeInterface) {
                $slotStartTime = $slot->start_time->format('H:i');
            } elseif (is_string($slot->start_time) && strlen($slot->start_time) >= 5) {
                $slotStartTime = substr($slot->start_time, 0, 5);
            } else {
                $slotStartTime = '00:00';
            }

            if ($slot->end_time instanceof \DateTimeInterface) {
                $slotEndTime = $slot->end_time->format('H:i');
            } elseif (is_string($slot->end_time) && strlen($slot->end_time) >= 5) {
                $slotEndTime = substr($slot->end_time, 0, 5);
            } else {
                $slotEndTime = '23:59';
            }

            $bookedCount = DB::table('appointments')
                ->where('appointment_date', $dateStr)
                ->where(function ($q) use ($slotStartTime) {
                    $q->where('appointment_time', $slotStartTime)
                      ->orWhere('appointment_time', $slotStartTime . ':00');
                })
                ->where(function ($query) {
                    $query->where('status', '!=', 'cancelled')
                          ->orWhereNull('status');
                })
                ->count();

            $remainingSlots = $bookedCount >= 1 ? 0 : 1;
            $isFullyBooked = $bookedCount >= 1;

            return [
                'available_date'  => $dateStr,
                'start_time'      => $slotStartTime,
                'end_time'        => $slotEndTime,
                'max_slots'       => 1,
                'booked_count'    => $bookedCount,
                'remaining_slots' => $remainingSlots,
                'is_fully_booked' => $isFullyBooked,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * ✅ BAGO: API para sa Green/Red Calendar (Android).
     * Nagre-return ng listahan ng araw sa isang buwan at kung available ba ito.
     */
    public function apiGetMonthAvailability(Request $request)
    {
        try {
            $monthStr = $request->query('month'); // Format: YYYY-MM
            if (!$monthStr) return response()->json(['success' => false, 'message' => 'Month required'], 400);

            $dateObj = \Carbon\Carbon::parse($monthStr . '-01');
            $year = $dateObj->year;
            $month = $dateObj->month;

            // ✅ Kunin lahat ng availability (Safe query para hindi maapektuhan ng oras)
            $availabilities = AppointmentAvailability::whereYear('available_date', $year)
                ->whereMonth('available_date', $month)
                ->where('is_active', true)
                ->get()
                ->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->available_date)->format('Y-m-d');
                });

            // ✅ Kunin lahat ng na-book na appointments
            $bookedCounts = DB::table('appointments')
                ->whereYear('appointment_date', $year)
                ->whereMonth('appointment_date', $month)
                ->whereNotIn('status', ['cancelled', 'expired'])
                ->get()
                ->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->appointment_date)->format('Y-m-d');
                });

            $result = [];
            $today = now()->toDateString();
            $daysInMonth = $dateObj->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = \Carbon\Carbon::createFromDate($year, $month, $day);
                $dateStr = $currentDate->format('Y-m-d');
                
                $totalSlots = isset($availabilities[$dateStr]) ? $availabilities[$dateStr]->count() : 0;
                $booked = isset($bookedCounts[$dateStr]) ? $bookedCounts[$dateStr]->count() : 0;
                
                // Available kung may slots at hindi pa puno, at hindi past date
                $isAvailable = ($totalSlots > $booked) && ($dateStr >= $today);

                $result[] = [
                    'date' => $dateStr,
                    'is_available' => $isAvailable,
                    'total_slots' => $totalSlots,
                    'booked_slots' => $booked,
                ];
            }

            return response()->json([
                'success' => true,
                'month' => $monthStr,
                'dates' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}