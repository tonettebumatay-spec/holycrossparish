<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

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
     * Core booking logic – safely inserts data, filtering out non-existent columns.
     */
    private function handleBooking(Request $request, string $type, string $modelClass)
    {
        try {
            Log::info("API_BOOKING_REQUEST_{$type}", $request->all());

            // 1. Validate
            $validator = Validator::make($request->all(), [
                'user_name'      => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'details'        => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $userName = $request->input('user_name');
            $contactNumber = $request->input('contact_number');
            $details = $request->input('details') ?: '';

            $parsed = $this->parseDetails($details);

            // 2. Build raw data array
            $rawData = $this->buildDataArray($type, $userName, $contactNumber, $parsed, $details);

            // 3. Filter: keep only columns that exist in the target table
            $model = new $modelClass();
            $fillable = $model->getFillable();

            $filteredData = [];
            foreach ($fillable as $column) {
                if (array_key_exists($column, $rawData)) {
                    $filteredData[$column] = $rawData[$column];
                }
            }

            // 4. Add status if the table has that column
            if (in_array('status', $fillable)) {
                $filteredData['status'] = 'pending';
            }

            Log::info("API_BOOKING_FINAL_DATA_{$type}", $filteredData);

            // 5. Create record
            $booking = $model->create($filteredData);

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
    private function buildDataArray(string $type, string $userName, string $contactNumber, array $parsed, string $details): array
    {
        $data = [];

        switch ($type) {
            case 'baptism':
                $data = [
                    'category'             => 'Baptism',
                    'book_number'          => 0,
                    'page_number'          => 0,
                    'line_number'          => 0,
                    'first_name'           => $userName,
                    'last_name'            => '',
                    'legitimacy'           => 'Unknown',
                    'birth_date'           => '1900-01-01',
                    'birth_place'          => 'Unknown',
                    'father_name'          => $parsed['father'] ?? '',
                    'father_birthplace'    => '',
                    'mother_maiden_name'   => $parsed['mother'] ?? '',
                    'mother_birthplace'    => '',
                    'residence'            => $contactNumber,
                    'baptism_date'         => now()->toDateString(),
                    'minister_name'        => 'TBD',
                    'godfather'            => '',
                    'godmother'            => '',
                    'remarks'              => $details,
                ];
                break;

            case 'communion':
                $data = [
                    'category'           => 'Communion',
                    'first_name'         => $userName,
                    'last_name'          => '',
                    'age'                => $parsed['age'] ?? 0,
                    'father_name'        => $parsed['father'] ?? '',
                    'residence'          => $contactNumber,
                    'communion_date'     => now()->toDateString(),
                    'minister_name'      => 'TBD',
                    'baptism_date'       => now()->toDateString(),
                    'place_of_baptism'   => 'TBD',
                    'book_number'        => 0,
                    'page_number'        => 0,
                    'line_number'        => 0,
                ];
                break;

            case 'confirmation':
                $data = [
                    'category'          => 'Confirmation',
                    'first_name'        => $userName,
                    'last_name'         => '',
                    'age'               => $parsed['age'] ?? 0,
                    'birthplace'        => 'TBD',
                    'father_name'       => $parsed['father'] ?? '',
                    'parents_residence' => $contactNumber,
                    'sponsors'          => 'TBD',
                    'minister_name'     => 'TBD',
                    'year'              => '',
                    'month_day'         => '',
                    'book_number'       => 0,
                    'page_number'       => 0,
                    'line_number'       => 0,
                ];
                break;

            case 'wedding':
                $data = [
                    'category'     => 'Wedding',
                    'groom_name'   => $parsed['groom'] ?? $userName,
                    'bride_name'   => $parsed['bride'] ?? '',
                    'remarks'      => $details,
                    'book_number'  => 0,
                    'page_number'  => 0,
                    'line_number'  => 0,
                    'year'         => '',
                    'month_day'    => '',
                ];
                break;

            case 'funeral':
                $data = [
                    'category'      => 'Funeral',
                    'deceased_name' => $userName,
                    'residence'     => $contactNumber,
                    'remarks'       => $details,
                    'book_number'   => 0,
                    'page_number'   => 0,
                    'line_number'   => 0,
                ];
                break;
        }

        return $data;
    }

    /**
     * Parse the 'details' string to extract extra fields
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
            if (empty($line)) continue;

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