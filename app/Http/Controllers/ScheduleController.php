<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
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

        // INAYOS DITO: Tinanggal ang 'archived_at' para hindi mag-error kapag nag-save ng bagong schedule
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
        // Tinitiyak na 'done' o 'cancelled' lang ang tatanggapin (Inayos ang spelling para tugma sa whereIn ng index)
        abort_unless(in_array($archive_status, ['done', 'cancelled'], true), 404);

        // INAYOS DITO: Tanging status na lang ang ia-update natin at tinanggal si archived_at para ligtas sa database
        $schedule->status = $archive_status;
        $schedule->save();

        return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully!');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully!');
    }
}