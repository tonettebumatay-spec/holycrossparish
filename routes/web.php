<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentAvailabilityController extends Controller
{
    /**
     * Display a listing of availability slots.
     */
    public function index()
    {
        $today = now()->toDateString();

        $activeAvailabilities = AppointmentAvailability::whereDate('available_date', '>=', $today)
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->get();

        $archivedAvailabilities = AppointmentAvailability::whereDate('available_date', '<', $today)
            ->orderByDesc('available_date')
            ->orderBy('start_time')
            ->get();

        return view('admin.availability.index', compact('activeAvailabilities', 'archivedAvailabilities'));
    }

    /**
     * Store bulk availability (4 default time slots for a date).
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'available_date' => 'required|date|after_or_equal:today',
        ]);

        $date = $request->available_date;

        $slots = [
            ['start_time' => '08:00:00', 'end_time' => '09:00:00'],
            ['start_time' => '10:00:00', 'end_time' => '11:00:00'],
            ['start_time' => '13:00:00', 'end_time' => '14:00:00'],
            ['start_time' => '15:00:00', 'end_time' => '16:00:00'],
        ];

        $created = 0;
        foreach ($slots as $slot) {
            $exists = AppointmentAvailability::where('available_date', $date)
                ->where('start_time', $slot['start_time'])
                ->exists();

            if (!$exists) {
                AppointmentAvailability::create([
                    'available_date' => $date,
                    'start_time'     => $slot['start_time'],
                    'end_time'       => $slot['end_time'],
                    'is_active'      => true,
                ]);
                $created++;
            }
        }

        return redirect()->route('admin.availability.index')
            ->with('success', "{$created} slot(s) added for {$date}.");
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($id)
    {
        $slot = AppointmentAvailability::findOrFail($id);
        $slot->is_active = !$slot->is_active;
        $slot->save();

        return back()->with('success', 'Slot status updated.');
    }

    /**
     * Delete a single slot.
     */
    public function destroy($id)
    {
        $slot = AppointmentAvailability::findOrFail($id);
        $slot->delete();

        return back()->with('success', 'Slot deleted.');
    }

    /**
     * ✅ BAGO: Delete ALL archived slots (past dates).
     */
    public function deleteArchived()
    {
        try {
            $today = now()->toDateString();

            $count = AppointmentAvailability::whereDate('available_date', '<', $today)->delete();

            Log::info('AVAILABILITY_ARCHIVED_DELETED', [
                'count' => $count,
                'date'  => $today,
            ]);

            return redirect()->route('admin.availability.index')
                ->with('success', "✅ {$count} archived slot(s) deleted successfully.");

        } catch (\Exception $e) {
            Log::error('AVAILABILITY_DELETE_ARCHIVED_ERROR', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('admin.availability.index')
                ->with('error', 'Failed to delete archived slots: ' . $e->getMessage());
        }
    }
}