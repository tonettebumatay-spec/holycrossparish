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
}