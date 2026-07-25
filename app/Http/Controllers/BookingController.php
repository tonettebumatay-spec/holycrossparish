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
     * Core booking logic – no date/time required (admin will schedule)
     */
    private function handleBooking(Request $request, string $type, string $modelClass)
    {
        try {
            Log::info("API_BOOKING_REQUEST_{$type}", $request->all());

            // ✅ Only require user_name and contact_number
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
                    'received' => $request->all(),
                ], 422);
            }

            // Extract data
            $userName = $request->input('user_name');
            $contactNumber = $request->input('contact_number');
            $details = $request->input('details') ?: '';

            // Parse details for extra fields
            $parsed = $this->parseDetails($details);

            // Build data array - NO date/time required
            $data = $this->buildDataArray($type, $userName, $contactNumber, $parsed, $details);

            // Create record
            $model = new $modelClass();
            $booking = $model->create($data);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' request submitted! We will contact you soon.',
                'booking' => $booking,
            ], 201);

        } catch (\Exception $e) {
            Log::error("API_BOOKING_ERROR_{$type}", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build data array for the specific model - NO date/time required
     */
    private function buildDataArray(string $type, string $userName, string $contactNumber, array $parsed, string $details): array
    {
        $data = [];

        switch ($type) {
            case 'baptism':
                $data = [
                    'first_name'     => $userName,
                    'last_name'      => '',
                    'father_name'    => $parsed['father'] ?? '',
                    'mother_name'    => $parsed['mother'] ?? '',
                    'remarks'        => $details,
                    'category'       => 'Baptism',
                    'book_number'    => 0,
                    'page_number'    => 0,
                    'line_number'    => 0,
                ];
                break;

            case 'communion':
                $data = [
                    'first_name'     => $userName,
                    'residence'      => $contactNumber,
                    'remarks'        => $details,
                    'category'       => 'Communion',
                    'book_number'    => 0,
                    'page_number'    => 0,
                    'line_number'    => 0,
                ];
                break;

            case 'confirmation':
                $data = [
                    'candidate_name'    => $userName,
                    'father_name'       => $parsed['father'] ?? '',
                    'mother_name'       => $parsed['mother'] ?? '',
                    'parents_residence' => $contactNumber,
                    'remarks'           => $details,
                    'category'          => 'Confirmation',
                    'book_number'       => 0,
                    'page_number'       => 0,
                    'line_number'       => 0,
                ];
                break;

            case 'wedding':
                $data = [
                    'groom_name'    => $parsed['groom'] ?? $userName,
                    'bride_name'    => $parsed['bride'] ?? '',
                    'remarks'       => $details,
                    'category'      => 'Wedding',
                    'book_number'   => 0,
                    'page_number'   => 0,
                    'line_number'   => 0,
                    'year'          => '',
                    'month_day'     => '',
                ];
                break;

            case 'funeral':
                $data = [
                    'deceased_name' => $userName,
                    'residence'     => $contactNumber,
                    'remarks'       => $details,
                    'category'      => 'Funeral',
                    'book_number'   => 0,
                    'page_number'   => 0,
                    'line_number'   => 0,
                ];
                break;

            default:
                $data = [
                    'remarks'        => $details,
                    'category'       => ucfirst($type),
                    'book_number'    => 0,
                    'page_number'    => 0,
                    'line_number'    => 0,
                ];
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
                }
            }
        }

        return $result;
    }
}