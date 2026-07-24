<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentAvailabilityController extends Controller
{
    public function index()
    {
        $availabilities = AppointmentAvailability::orderBy('available_date')->get();
        return view('admin.availability.index', compact('availabilities'));
    }

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