<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    // Web methods (unchanged)
    public function index()
    {
        $user = Auth::user();

        $liveSchedules = Schedule::query()
            ->where('status', 'pending')
            ->where('date', '>=', now()->toDateString())
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

        // I-check kung ang napiling petsa ay lumipas na (past date)
        $selectedDate = Carbon::parse($validated['date'])->startOfDay();
        $today = Carbon::today();

        if ($selectedDate->lt($today)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hindi na maaaring i-post ang schedule para sa mga nakalipas na petsa!');
        }

        Schedule::create([
            'barangay' => $validated['location'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('schedules.index')->with('success', 'SCHEDULE POSTED SUCCESSFULLY!');
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

    // 🔥 API method for Android Events
    public function indexApi()
    {
        try {
            $schedules = Schedule::where('status', 'pending')
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get();

            $events = $schedules->map(function ($schedule) {
                return [
                    'id'          => $schedule->id,
                    'title'       => 'Mass Schedule',
                    'description' => $schedule->description ?? '',
                    'date'        => $schedule->date,
                    'time'        => $schedule->time,
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
                'message' => 'Failed to fetch schedules: ' . $e->getMessage()
            ], 500);
        }
    }
}