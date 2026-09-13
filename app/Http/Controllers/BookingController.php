<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use App\Models\AppointmentAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // ============================================================
    // API METHODS – Called by Android app
    // ============================================================

    public function storeBaptism(Request $request)
    {
        return $this->handleBooking($request, 'baptism', Baptism::class);
    }

    public function storeCommunion(Request $request)
    {
        return $this->handleBooking($request, 'communion', Communion::class);
    }

    public function storeConfirmation(Request $request)
    {
        return $this->handleBooking($request, 'confirmation', Confirmation::class);
    }

    public function storeWedding(Request $request)
    {
        return $this->handleBooking($request, 'wedding', Wedding::class);
    }

    public function storeFuneral(Request $request)
    {
        return $this->handleBooking($request, 'funeral', Funeral::class);
    }

    /**
     * Core booking logic – safely inserts data and appointment schedule.
     */
    private function handleBooking(Request $request, string $type, string $modelClass)
    {
        try {
            Log::info("API_BOOKING_REQUEST_{$type}", $request->all());

            // 1. Validate request data
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

            Log::info('AUTH HEADER', [
                'authorization' => $request->header('Authorization'),
            ]);

            Log::info('AUTH USER', [
                'user' => $request->user(),
            ]);

            $user = $request->user();

            $userName = $request->input('user_name');
            $contactNumber = $request->input('contact_number');
            $details = $request->input('details') ?: '';

            // Appointment schedule sent by Android
            $appointmentDate = $request->input('appointment_date');
            $appointmentTime = $request->input('time');

            $parsed = $this->parseDetails($details);

            // 2. Build raw data array
            $rawData = $this->buildDataArray(
                $type,
                $userName,
                $contactNumber,
                $parsed,
                $details
            );

            // Add appointment schedule
            $rawData['appointment_date'] = $appointmentDate;
            $rawData['appointment_time'] = $appointmentTime;

            // Add logged-in user's information
            if ($user) {
                $rawData['user_id'] = $user->id;
                $rawData['email'] = $user->email;
            } elseif (isset($parsed['email'])) {
                $rawData['email'] = $parsed['email'];
            }

            // 3. Filter data based on the actual model/table columns
            $model = new $modelClass();
            $table = $model->getTable();
            $fillable = $model->getFillable();

            $filteredData = [];

            foreach ($fillable as $column) {
                if (array_key_exists($column, $rawData)) {
                    $filteredData[$column] = $rawData[$column];
                }
            }

            // Add appointment date if the table contains the column
            if (
                Schema::hasColumn($table, 'appointment_date') &&
                $appointmentDate !== null
            ) {
                $filteredData['appointment_date'] = $appointmentDate;
            }

            // Add appointment time if the table contains the column
            if (
                Schema::hasColumn($table, 'appointment_time') &&
                $appointmentTime !== null
            ) {
                $filteredData['appointment_time'] = $appointmentTime;
            }

            // Add user_id if the table contains the column
            if (
                Schema::hasColumn($table, 'user_id') &&
                isset($rawData['user_id'])
            ) {
                $filteredData['user_id'] = $rawData['user_id'];
            }

            // Add email if the table contains the column
            if (
                Schema::hasColumn($table, 'email') &&
                isset($rawData['email'])
            ) {
                $filteredData['email'] = $rawData['email'];
            }

            // 4. Add pending status
            if (
                in_array('status', $fillable) ||
                Schema::hasColumn($table, 'status')
            ) {
                if (!isset($filteredData['status'])) {
                    $filteredData['status'] = 'pending';
                }
            }

            // ========================================================
            // 4.5. Check appointment availability and slot capacity
            // ========================================================

            $avail = AppointmentAvailability::where(
                    'sacrament_type',
                    $type
                )
                ->where(
                    'available_date',
                    $appointmentDate
                )
                ->where(
                    'start_time',
                    'LIKE',
                    '%' . $appointmentTime . '%'
                )
                ->where('is_active', true)
                ->first();

            if ($avail) {
                $bookedCount = DB::table($table)
                    ->where(
                        'appointment_date',
                        $appointmentDate
                    )
                    ->where(
                        'appointment_time',
                        $appointmentTime
                    )
                    ->where(function ($query) {
                        $query->where('status', '!=', 'cancelled')
                              ->orWhereNull('status');
                    })
                    ->count();

                if ($bookedCount >= $avail->max_slots) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The selected date and time slot is already fully booked. Please choose another schedule.',
                    ], 422);
                }
            }

            Log::info("API_BOOKING_FINAL_DATA_{$type}", $filteredData);

            // 5. Create booking record
            $booking = $model->create($filteredData);

            Log::info("API_BOOKING_CREATED_{$type}", [
                'booking_id' => $booking->id ?? null,
                'appointment_date' => $booking->appointment_date ?? null,
                'appointment_time' => $booking->appointment_time ?? null,
                'status' => $booking->status ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' request submitted successfully! The admin will schedule your appointment.',
                'booking' => $booking,
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

    /**
     * Build strict and safe data array matching each table schema.
     */
    private function buildDataArray(
        string $type,
        string $userName,
        string $contactNumber,
        array $parsed,
        string $details
    ): array {
        $data = [];

        switch ($type) {

            case 'baptism':

                $data = [
                    'category'            => 'Baptism',
                    'book_number'         => 0,
                    'page_number'         => 0,
                    'line_number'         => 0,
                    'first_name'          => $parsed['child'] ?? $userName,
                    'last_name'           => '',
                    'legitimacy'          => 'Unknown',
                    'birth_date'          => '1900-01-01',
                    'birth_place'        => 'Unknown',
                    'father_name'         => $parsed['father'] ?? '',
                    'father_birthplace'   => '',
                    'mother_maiden_name'  => $parsed['mother'] ?? '',
                    'mother_birthplace'   => '',
                    'residence'           => $contactNumber,
                    'baptism_date'        => null,
                    'minister_name'       => 'TBD',
                    'godfather'           => '',
                    'godmother'           => '',
                    'remarks'             => $details,
                ];

                break;

            case 'communion':

                $data = [
                    'book_number'        => 0,
                    'page_number'        => 0,
                    'line_number'        => 0,
                    'first_name'         => $parsed['child'] ?? $userName,
                    'last_name'          => '',
                    'communion_date'     => null,
                    'residence'          => $contactNumber,
                    'minister_name'      => 'TBD',
                    'baptism_date'       => null,
                    'place_of_baptism'   => 'Unknown',
                ];

                break;

            case 'confirmation':

                $data = [
                    'book_number'       => 0,
                    'page_number'       => 0,
                    'line_number'       => 0,
                    'year'              => '',
                    'month_day'        => null,
                    'first_name'        => $parsed['child'] ?? $userName,
                    'last_name'         => '',
                    'age'               => $parsed['age'] ?? 0,
                    'birthplace'        => 'Unknown',
                    'father_name'       => $parsed['father'] ?? '',
                    'mother_name'       => $parsed['mother'] ?? '',
                    'parents_residence' => $contactNumber,
                    'sponsors'          => 'TBD',
                    'minister_name'     => 'TBD',
                ];

                break;

            case 'wedding':

                $data = [
                    'category'                  => 'Wedding',
                    'book_number'               => 0,
                    'page_number'               => 0,
                    'line_number'               => 0,
                    'year'                      => '',
                    'month_day'                => null,
                    'groom_name'               => $parsed['groom'] ?? $userName,
                    'groom_age'                 => 0,
                    'groom_status'              => 'Single',
                    'groom_residence'           => $contactNumber,
                    'groom_parents'             => '',
                    'groom_parents_residence'   => '',
                    'bride_name'                => $parsed['bride'] ?? '',
                    'bride_age'                 => 0,
                    'bride_status'              => 'Single',
                    'bride_residence'           => '',
                    'bride_parents'             => '',
                    'bride_parents_residence'   => '',
                ];

                break;

            case 'funeral':

                $data = [
                    'category'      => 'Funeral',
                    'book_number'   => 0,
                    'page_number'  => 0,
                    'line_number'   => 0,
                    'deceased_name' => $parsed['child'] ?? $userName,
                    'residence'     => $contactNumber,
                    'minister_name' => 'TBD',
                    'remarks'       => $details,
                ];

                break;
        }

        return $data;
    }

    /**
     * Parse the 'details' string to extract extra fields.
     */
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

                } elseif (
                    str_contains($key, 'child') ||
                    str_contains($key, 'name')
                ) {

                    $result['child'] = $value;
                }
            }
        }

        return $result;
    }
}