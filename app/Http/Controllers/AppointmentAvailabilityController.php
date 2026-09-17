<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentAvailability;
use Illuminate\Support\Facades\DB;

class AppointmentAvailabilityController extends Controller
{
    public function apiGetSlots($sacrament)
    {
        $tableMap = [
            'baptism' => 'baptisms',
            'communion' => 'communions',
            'confirmation' => 'confirmations',
            'wedding' => 'weddings',
            'funeral' => 'funerals',
        ];

        // Normalize input: lowercase, trim spaces, and remove trailing 's'
        // This handles: Baptism, baptism, BAPTISM, baptisms, Baptisms, etc.
        $normalized = strtolower(trim($sacrament));
        $singularSacrament = rtrim($normalized, 's');

        // Match against known sacrament types (case-insensitive)
        $key = null;
        foreach (array_keys($tableMap) as $tKey) {
            if ($singularSacrament === $tKey || $normalized === $tKey) {
                $key = $tKey;
                break;
            }
        }

        if (!$key || !array_key_exists($key, $tableMap)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid sacrament type.',
                'received' => $sacrament,
            ], 400);
        }

        $tableName = $tableMap[$key];

        $slots = AppointmentAvailability::where('sacrament_type', $key)
            ->where('available_date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->orderBy('available_date')
            ->get();

        $data = $slots->map(function ($slot) use ($tableName) {
            // Safely convert available_date to string (handles both Carbon and string)
            if ($slot->available_date instanceof \DateTimeInterface) {
                $dateStr = $slot->available_date->format('Y-m-d');
            } else {
                $dateStr = (string) $slot->available_date;
            }

            // Safely format start_time (handles both Carbon and string, with null checks)
            if ($slot->start_time instanceof \DateTimeInterface) {
                $slotStartTime = $slot->start_time->format('H:i');
            } elseif (is_string($slot->start_time) && strlen($slot->start_time) >= 5) {
                $slotStartTime = substr($slot->start_time, 0, 5);
            } else {
                $slotStartTime = '00:00';
            }

            // Safely format end_time (handles both Carbon and string, with null checks)
            if ($slot->end_time instanceof \DateTimeInterface) {
                $slotEndTime = $slot->end_time->format('H:i');
            } elseif (is_string($slot->end_time) && strlen($slot->end_time) >= 5) {
                $slotEndTime = substr($slot->end_time, 0, 5);
            } else {
                $slotEndTime = '23:59';
            }

            // Count existing bookings for this date + start_time.
            // PostgreSQL-compatible: no LEFT() function. Match both "HH:MM" and "HH:MM:SS" formats.
            $bookedCount = DB::table($tableName)
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

            $remainingSlots = max(0, $slot->max_slots - $bookedCount);

            return [
                'available_date'   => $dateStr,
                'start_time'       => $slotStartTime,
                'end_time'         => $slotEndTime,
                'max_slots'        => $slot->max_slots,
                'booked_count'     => $bookedCount,
                'remaining_slots'  => $remainingSlots,
                'is_fully_booked'  => $remainingSlots <= 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}