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

        // Normalisahin ang string kung sakaling may plural/singular mismatch galing sa Android
        $singularSacrament = rtrim(strtolower($sacrament), 's');
        if ($singularSacrament === 'communion') {
            $key = 'communion';
        } else {
            // hanapin kung alin ang tugma
            $key = null;
            foreach (array_keys($tableMap) as $tKey) {
                if (str_starts_with($sacrament, $tKey)) {
                    $key = $tKey;
                    break;
                }
            }
        }

        if (!$key || !array_key_exists($key, $tableMap)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid sacrament type.'
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

            // Bilangin kung ilan na ang naka-book sa date at oras na ito
            $bookedCount = DB::table($tableName)
                ->where('appointment_date', $dateStr)
                ->where('appointment_time', $slot->start_time)
                ->where(function($query) {
                    $query->where('status', '!=', 'cancelled')
                          ->orWhereNull('status');
                })
                ->count();

            $remainingSlots = max(0, $slot->max_slots - $bookedCount);

           return [
                'available_date' => $dateStr,
                'start_time' => is_object($slot->start_time) ? $slot->start_time->format('H:i') : substr($slot->start_time, 0, 5),
                'end_time' => is_object($slot->end_time) ? $slot->end_time->format('H:i') : substr($slot->end_time, 0, 5),
                'max_slots' => $slot->max_slots,
                'booked_count' => $bookedCount,
                'remaining_slots' => $remainingSlots,
                'is_fully_booked' => $remainingSlots <= 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}