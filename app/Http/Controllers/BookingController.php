<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function storeBaptism(Request $request)
    {
        return $this->handleBooking($request, 'baptism');
    }

    public function storeCommunion(Request $request)
    {
        return $this->handleBooking($request, 'communion');
    }

    public function storeConfirmation(Request $request)
    {
        return $this->handleBooking($request, 'confirmation');
    }

    public function storeWedding(Request $request)
    {
        return $this->handleBooking($request, 'wedding');
    }

    public function storeFuneral(Request $request)
    {
        return $this->handleBooking($request, 'funeral');
    }

    private function handleBooking(Request $request, string $type)
    {
        try {
            Log::info("API_BOOKING_REQUEST_{$type}", $request->all());

            $validator = Validator::make($request->all(), [
                'user_name'        => 'required|string|max:255',
                'contact_number'   => 'required|string|max:20',
                'details'          => 'nullable|string',
                'appointment_date' => 'required|date',
                'time'             => 'required|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $userName = $request->input('user_name');
            $contactNumber = $request->input('contact_number');
            $details = $request->input('details') ?: '';

            $appointmentDate = $request->input('appointment_date');
            $appointmentTime = $request->input('time');

            $parsed = $this->parseDetails($details);

            $detailsJson = json_encode(array_merge(
                $parsed,
                [
                    'raw_details' => $details,
                    'submitted_by_name' => $userName,
                ]
            ));

            $booking = DB::transaction(function () use (
                $appointmentDate,
                $appointmentTime,
                $type,
                $userName,
                $contactNumber,
                $detailsJson,
                $user,
                $parsed
            ) {
                AppointmentAvailability::where('available_date', $appointmentDate)
                    ->where('is_active', true)
                    ->where(function ($q) use ($appointmentTime) {
                        $q->where('start_time', $appointmentTime)
                          ->orWhere('start_time', $appointmentTime . ':00')
                          ->orWhere('start_time', 'LIKE', $appointmentTime . '%');
                    })
                    ->lockForUpdate()
                    ->first();

                $bookedCount = DB::table('appointments')
                    ->where('appointment_date', $appointmentDate)
                    ->where(function ($q) use ($appointmentTime) {
                        $q->where('appointment_time', $appointmentTime)
                          ->orWhere('appointment_time', $appointmentTime . ':00');
                    })
                    ->where(function ($query) {
                        $query->where('status', '!=', 'cancelled')
                              ->orWhereNull('status');
                    })
                    ->count();

                if ($bookedCount >= 1) {
                    return [
                        'success' => false,
                        'message' => 'This time slot is already booked. Please choose another available time.',
                    ];
                }

                $record = Appointment::create([
                    'user_name'        => $userName,
                    'service_type'     => $type,
                    'appointment_date' => $appointmentDate,
                    'appointment_time' => $appointmentTime,
                    'contact_number'   => $contactNumber,
                    'details'          => $detailsJson,
                    'status'           => 'pending',
                    'user_id'          => $user?->id,
                    'email'            => $user?->email ?? ($parsed['email'] ?? null),
                ]);

                return [
                    'success' => true,
                    'booking' => $record,
                ];
            });

            if (!($booking['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $booking['message'] ?? 'Slot is unavailable.',
                ], 422);
            }

            $record = $booking['booking'];

            Log::info("API_BOOKING_CREATED_{$type}", [
                'booking_id' => $record->id ?? null,
                'appointment_date' => $record->appointment_date ?? null,
                'appointment_time' => $record->appointment_time ?? null,
                'status' => $record->status ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' request submitted successfully! The admin will schedule your appointment.',
                'booking' => $record,
            ], 201);

        } catch (\Exception $e) {
            Log::error("API_BOOKING_ERROR_{$type}", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function parseDetails(string $details): array
    {
        $result = [];

        if (empty($details)) {
            return $result;
        }

        $lines = explode("\n", $details);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            if (strpos($line, ':') !== false) {
                $parts = explode(':', $line, 2);

                $key = strtolower(trim($parts[0]));
                $value = trim($parts[1] ?? '');

                if (str_contains($key, 'father')) {
                    $result['father'] = $value;
                } elseif (str_contains($key, 'mother')) {
                    $result['mother'] = $value;
                } elseif (str_contains($key, 'groom')) {
                    $result['groom'] = $value;
                } elseif (str_contains($key, 'bride')) {
                    $result['bride'] = $value;
                } elseif (str_contains($key, 'age')) {
                    $result['age'] = intval($value);
                } elseif (str_contains($key, 'email')) {
                    $result['email'] = $value;
                } elseif (str_contains($key, 'child') || str_contains($key, 'name')) {
                    $result['child'] = $value;
                }
            }
        }

        return $result;
    }
}