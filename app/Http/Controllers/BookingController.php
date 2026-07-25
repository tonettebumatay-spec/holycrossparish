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
                ], 422);
            }

            // Extract data
            $userName = $request->input('user_name');
            $contactNumber = $request->input('contact_number');
            $details = $request->input('details') ?: '';

            // Parse details for extra fields
            $parsed = $this->parseDetails($details);

            // Build safe universal data array
            $data = $this->buildDataArray($type, $userName, $contactNumber, $parsed, $details);

            // Create record
            $model = new $modelClass();
            $booking = $model->create($data);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' request submitted successfully!',
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
     * Universal safe data array to prevent database column errors
     */
    private function buildDataArray(string $type, string $userName, string $contactNumber, array $parsed, string $details): array
    {
        return [
            'first_name' => $parsed['child'] ?? $userName,
            'last_name'  => 'N/A',
            'residence'  => $contactNumber,
            'status'     => 'Pending',
            'remarks'    => "User Name: {$userName} | Contact: {$contactNumber} | " . $details,
        ];
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

                if (str_contains($key, 'child') || str_contains($key, 'name') || str_contains($key, 'communicant')) {
                    $result['child'] = $value;
                } elseif (str_contains($key, 'father')) {
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