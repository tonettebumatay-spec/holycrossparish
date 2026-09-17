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
            $dateStr = is_string($slot->available_date)
                ? $slot->available_date
                : $slot->available_date->toDateString();

            // Count how many bookings already exist for this date + start_time
            // NOTE: appointment_time in sacrament tables is stored as "HH:MM:SS"
            // while $slot->start_time may be "HH:MM". We normalize both to "HH:MM".
            $slotStartTime = is_object($slot->start_time)
                ? $slot->start_time->format('H:i')
                : substr($slot->start_time, 0, 5);

            $bookedCount = DB::table($tableName)
                ->where('appointment_date', $dateStr)
                ->whereRaw('LEFT(appointment_time, 5) = ?', [$slotStartTime])
                ->where(function ($query) {
                    $query->where('status', '!=', 'cancelled')
                          ->orWhereNull('status');
                })
                ->count();

            $remainingSlots = max(0, $slot->max_slots - $bookedCount);

            return [
                'available_date'   => $dateStr,
                'start_time'       => $slotStartTime,
                'end_time'         => is_object($slot->end_time)
                                        ? $slot->end_time->format('H:i')
                                        : substr($slot->end_time, 0, 5),
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