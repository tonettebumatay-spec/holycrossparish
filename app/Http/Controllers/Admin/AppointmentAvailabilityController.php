<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentAvailabilityController extends Controller
{
    /**
     * Fixed 4 time slots that are added for every date.
     * Times are in 24-hour format (H:i) for the DB.
     */
    private const FIXED_SLOTS = [
        ['start' => '08:00', 'end' => '09:00'],
        ['start' => '10:00', 'end' => '11:00'],
        ['start' => '13:00', 'end' => '14:00'],
        ['start' => '15:00', 'end' => '16:00'],
    ];

    /**
     * List all availability records, split into:
     * - Active: today and future dates
     * - Archived: past dates (before today)
     */
    public function index()
    {
        $today = now()->toDateString();

        $activeAvailabilities = AppointmentAvailability::where('available_date', '>=', $today)
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->get();

        $archivedAvailabilities = AppointmentAvailability::where('available_date', '<', $today)
            ->orderByDesc('available_date')
            ->orderBy('start_time')
            ->get();

        return view('admin.availability.index', compact(
            'activeAvailabilities',
            'archivedAvailabilities'
        ));
    }

    /**
     * Bulk store: creates the fixed 4 slots for a given date.
     * Skips slots that already exist for that date (start_time match).
     */
    public function storeBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'available_date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $date = $request->input('available_date');
        $created = 0;
        $skipped = 0;

        foreach (self::FIXED_SLOTS as $slot) {
            // Check kung existing na yung date + start_time combination
            $exists = AppointmentAvailability::where('available_date', $date)
                ->where(function ($q) use ($slot) {
                    // Match both "08:00" and "08:00:00" formats
                    $q->where('start_time', $slot['start'])
                      ->orWhere('start_time', $slot['start'] . ':00');
                })
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            AppointmentAvailability::create([
                // Legacy column — kept for DB constraint.
                // All slots are generic (available to all sacraments).
                'sacrament_type' => 'baptism',
                'available_date' => $date,
                'start_time'     => $slot['start'],
                'end_time'       => $slot['end'],
                'max_slots'      => 1,
                'is_active'      => true,
            ]);

            $created++;
        }

        $message = "Added {$created} slot(s) for " . \Carbon\Carbon::parse($date)->format('M d, Y') . ".";
        if ($skipped > 0) {
            $message .= " {$skipped} slot(s) already existed and were skipped.";
        }

        return redirect()->route('admin.availability.index')->with('success', $message);
    }

    /**
     * Legacy single-slot store (kept for backward compatibility).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sacrament_type' => 'required|in:baptism,communion,confirmation,wedding,funeral',
            'available_date' => 'required|date|after_or_equal:today',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
            'max_slots'      => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        AppointmentAvailability::create($request->all());
        return redirect()->route('admin.availability.index')->with('success', 'Slot added.');
    }

    public function destroy($id)
    {
        $availability = AppointmentAvailability::findOrFail($id);
        $availability->delete();
        return back()->with('success', 'Slot removed.');
    }

    public function toggleActive($id)
    {
        $availability = AppointmentAvailability::findOrFail($id);
        $availability->is_active = !$availability->is_active;
        $availability->save();
        return back()->with('success', 'Slot status updated.');
    }
}