<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    // Web methods (existing)
    public function index()
    {
        $user = Auth::user();

        $liveSchedules = Schedule::query()
            ->where('status', 'pending')
            ->orderBy('date', 'asc')
            ->get();

        $archivedSchedules = Schedule::query()
            ->whereIn('status', ['done', 'cancelled'])
            ->orderBy('date', 'desc')
            ->get();

        return view('schedules.index', compact('user', 'liveSchedules', 'archivedSchedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'description' => 'nullable|string|max:500',
        ]);

        Schedule::create([
            'barangay' => $validated['location'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('schedules.index')->with('success', 'Schedule posted successfully!');
    }

    public function archiveStatus(Request $request, Schedule $schedule, string $archive_status)
    {
        abort_unless(in_array($archive_status, ['done', 'cancelled'], true), 404);

        $schedule->status = $archive_status;
        $schedule->save();

        return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully!');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully!');
    }

    // ============================================================
    // NEW: API endpoint for Android app (Events)
    // ============================================================
    public function indexApi()
    {
        try {
            // Fetch only active (pending) schedules – you can change to 'live' or all if needed
            $schedules = Schedule::where('status', 'pending')
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get();

            // Transform to clean event format for the app
            $events = $schedules->map(function ($schedule) {
                return [
                    'id'          => $schedule->id,
                    'title'       => 'Mass Schedule', // or use location/description
                    'description' => $schedule->description ?? '',
                    'date'        => $schedule->date, // YYYY-MM-DD
                    'time'        => $schedule->time, // HH:MM:SS or H:i A
                    'location'    => $schedule->barangay ?? 'Holy Cross Parish',
                    'status'      => $schedule->status,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data'   => $events,
                'count'  => $events->count(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Schedule API Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch schedules'
            ], 500);
        }
    }
}