<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $statusFilter = $request->input('status');
            $typeFilter = $request->input('type');

            $query = Appointment::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('user_name', 'LIKE', "%{$search}%")
                      ->orWhere('contact_number', 'LIKE', "%{$search}%")
                      ->orWhere('details', 'LIKE', "%{$search}%");
                });
            }

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            if ($typeFilter) {
                $query->where('service_type', strtolower($typeFilter));
            }

            $allAppointments = $query->orderByDesc('created_at')->get()->map(function ($item) {
                $item->type = ucfirst($item->service_type ?? 'Unknown');
                $item->name = $this->extractName($item);
                $item->submitted_at = $item->created_at
                    ? $item->created_at->format('Y-m-d h:i A')
                    : 'N/A';
                return $item;
            });

            $today = now()->toDateString();

            $activeAppointments = $allAppointments->filter(function ($app) use ($today) {
                $appointmentDate = $app->appointment_date ?? null;
                $status = strtolower($app->status ?? 'pending');

                if (empty($appointmentDate)) {
                    return true;
                }

                $dateStr = $appointmentDate instanceof \DateTimeInterface
                    ? $appointmentDate->format('Y-m-d')
                    : (string) $appointmentDate;

                if ($dateStr >= $today) {
                    return true;
                }

                if (!in_array($status, ['approved', 'cancelled', 'canceled'])) {
                    return true;
                }

                return false;
            })->values();

            $archivedAppointments = $allAppointments->filter(function ($app) use ($today) {
                $appointmentDate = $app->appointment_date ?? null;
                $status = strtolower($app->status ?? 'pending');

                if (empty($appointmentDate)) {
                    return false;
                }

                $dateStr = $appointmentDate instanceof \DateTimeInterface
                    ? $appointmentDate->format('Y-m-d')
                    : (string) $appointmentDate;

                if ($dateStr >= $today) {
                    return false;
                }

                return in_array($status, ['approved', 'cancelled', 'canceled']);
            })->values();

            return view('appointments.index', [
                'appointments'         => $allAppointments,
                'activeAppointments'   => $activeAppointments,
                'archivedAppointments' => $archivedAppointments,
                'search'               => $search,
                'statusFilter'         => $statusFilter,
                'typeFilter'           => $typeFilter,
            ]);

        } catch (\Exception $e) {
            Log::error('Appointment Index Error: ' . $e->getMessage());
            return view('appointments.index', [
                'appointments'         => collect(),
                'activeAppointments'   => collect(),
                'archivedAppointments' => collect(),
                'search'               => null,
                'statusFilter'         => null,
                'typeFilter'           => null,
            ]);
        }
    }

    private function extractName($appointment): string
    {
        $type = strtolower($appointment->service_type ?? '');
        $details = $appointment->details;

        $parsed = [];
        if (!empty($details)) {
            $decoded = json_decode($details, true);
            if (is_array($decoded)) {
                $parsed = $decoded;
            }
        }

        switch ($type) {
            case 'baptism':
            case 'communion':
            case 'confirmation':
                if (!empty($parsed['child'])) {
                    return trim($parsed['child']);
                }
                return $appointment->user_name ?? 'N/A';

            case 'wedding':
                $groom = $parsed['groom'] ?? '';
                $bride = $parsed['bride'] ?? '';
                if ($groom || $bride) {
                    return trim(($groom ?: '') . ($groom && $bride ? ' & ' : '') . ($bride ?: ''));
                }
                return $appointment->user_name ?? 'N/A';

            case 'funeral':
                return $parsed['child'] ?? $appointment->user_name ?? 'N/A';

            default:
                return $appointment->user_name ?? 'N/A';
        }
    }

    public function cancel(Request $request, $id)
    {
        $record = Appointment::findOrFail($id);

        if ($record->status === 'cancelled') {
            return back()->with('error', 'Appointment is already cancelled.');
        }

        $record->status = 'cancelled';
        $record->cancellation_reason = $request->input('reason');
        $record->is_locked = true;
        $record->save();

        return back()->with('success', 'Appointment cancelled successfully.');
    }

    public function schedule(Request $request, $id)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $record = Appointment::findOrFail($id);

        $record->appointment_date = $request->appointment_date;
        $record->appointment_time = $request->appointment_time;
        $record->status = 'approved';

        $record->save();

        return back()->with('success', 'Appointment schedule has been set.');
    }

    public function destroy($id)
    {
        $record = Appointment::findOrFail($id);
        $record->delete();

        return back()->with('success', 'Appointment deleted successfully.');
    }

    public function myAppointments(Request $request)
    {
        try {
            $user = $request->user();

            $query = Appointment::query();

            if ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('email', $user->email);
                });
            }

            $appointments = $query->orderByDesc('created_at')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => ucfirst($item->service_type ?? 'Unknown'),
                    'name' => $this->extractName($item),
                    'appointment_date' => $item->appointment_date,
                    'appointment_time' => $item->appointment_time,
                    'scheduled_date' => $item->appointment_date,
                    'scheduled_time' => $item->appointment_time,
                    'is_scheduled' => !empty($item->appointment_date),
                    'status' => $item->status ?? 'pending',
                    'cancellation_reason' => $item->cancellation_reason,
                    'is_locked' => (bool) ($item->is_locked ?? false),
                    'submitted_at' => $item->created_at
                        ? $item->created_at->format('Y-m-d H:i:s')
                        : null,
                    'created_at' => $item->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'appointments' => $appointments,
            ]);

        } catch (\Exception $e) {
            Log::error('MY_APPOINTMENTS_ERROR: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getConfirmedAppointments(Request $request)
    {
        try {
            $user = $request->user();

            $confirmed = Appointment::query()
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereNotNull('appointment_date')
                ->whereNotNull('appointment_time')
                ->orderByDesc('updated_at')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => ucfirst($item->service_type ?? 'Unknown'),
                        'name' => $this->extractName($item),
                        'scheduled_date' => $item->appointment_date,
                        'scheduled_time' => $item->appointment_time,
                        'admin_notes' => null,
                        'updated_at' => $item->updated_at
                            ? $item->updated_at->toDateTimeString()
                            : null,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $confirmed,
                'count' => $confirmed->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('CONFIRMED_APPOINTMENTS_ERROR: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch confirmed appointments: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $record = Appointment::findOrFail($id);
        $record->status = $request->status;
        $record->save();

        return back()->with('success', 'Appointment status updated.');
    }

    public function store(Request $request)
    {
        return response()->json(['status' => 'error', 'message' => 'Use booking endpoints'], 400);
    }
}