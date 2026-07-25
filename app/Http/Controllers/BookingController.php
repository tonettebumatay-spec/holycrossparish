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
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show the centralized booking form (web).
     */
    public function create()
    {
        // ... keep your existing web create method ...
        // (I'll keep it as is, but for brevity, I'll omit full code here)
    }

    /**
     * Store the centralized booking request (web).
     */
    public function store(Request $request)
    {
        // ... keep your existing web store method ...
    }

    // ============================================================
    // API METHODS – Called by Android app
    // ============================================================

    /**
     * Unified booking handler for all sacraments.
     * The Android app sends a POST request with a BookingRequest JSON.
     * Expected fields: user_name, service_type, appointment_date, contact_number, details
     */
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
     * Core booking logic – validates, maps fields, and stores.
     */
    private function handleBooking(Request $request, string $type, string $modelClass)
    {
        try {
            // 1. Log incoming data for debugging
            Log::info("API_BOOKING_REQUEST_{$type}", $request->all());

            // 2. Validate required fields (Android sends these)
            $validator = Validator::make($request->all(), [
                'user_name'       => 'required|string|max:255',
                'service_type'    => 'required|string|in:baptism,communion,confirmation,wedding,funeral',
                'appointment_date'=> 'nullable|date',
                'contact_number'  => 'required|string|max:20',
                'details'         => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // 3. Extract data
            $userName = $request->input('user_name');
            $appointmentDate = $request->input('appointment_date') ?: now()->toDateString();
            $contactNumber = $request->input('contact_number');
            $details = $request->input('details') ?: '';

            // 4. Parse details for extra fields (name, email, time, etc.)
            $parsed = $this->parseDetails($details);

            // 5. Build the data array for the specific model
            $data = $this->buildDataArray($type, $userName, $appointmentDate, $contactNumber, $parsed, $details);

            // 6. Create the record
            $model = new $modelClass();
            $booking = $model->create($data);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' booking created successfully!',
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
     * Build the data array for the specific model, mapping fields.
     */
    private function buildDataArray(string $type, string $userName, string $appointmentDate, string $contactNumber, array $parsed, string $details): array
    {
        $base = [
            'book_number'     => 0,
            'page_number'     => 0,
            'line_number'     => 0,
            'remarks'         => $details,
            'minister_name'   => $parsed['minister'] ?? '',
            'residence'       => $parsed['residence'] ?? $contactNumber,
            'parents_residence' => $parsed['residence'] ?? $contactNumber,
            'sponsor_name'    => $parsed['sponsor'] ?? '',
            'sponsors'        => $parsed['sponsor'] ?? '',
            'godfather'       => $parsed['godfather'] ?? '',
            'godmother'       => $parsed['godmother'] ?? '',
            'mother_name'     => $parsed['mother'] ?? '',
            'mother_maiden_name' => $parsed['mother'] ?? '',
            'middle_name'     => '',
            'suffix'          => '',
            'birthplace'      => $parsed['birthplace'] ?? '',
            'birth_place'     => $parsed['birthplace'] ?? '',
            'father_birthplace' => '',
            'mother_birthplace' => '',
            'place_of_baptism' => $parsed['baptism_place'] ?? '',
            'coordinator_name' => '',
            'cemetery_name'   => '',
            'cause_of_death'  => '',
            'sacraments_received' => '',
            'spouse_name'     => $parsed['spouse'] ?? '',
            'groom_status'    => '',
            'bride_status'    => '',
            'groom_father'    => '',
            'groom_mother'    => '',
            'groom_parents'   => '',
            'groom_parents_residence' => '',
            'groom_residence' => '',
            'bride_father'    => '',
            'bride_mother'    => '',
            'bride_parents'   => '',
            'bride_parents_residence' => '',
            'bride_residence' => '',
            'witness_1'       => '',
            'witness_2'       => '',
            'age'             => $parsed['age'] ?? 0,
            'age_at_death'    => $parsed['age'] ?? 0,
            'groom_age'       => $parsed['groom_age'] ?? 0,
            'bride_age'       => $parsed['bride_age'] ?? 0,
            'legitimacy'      => $parsed['legitimacy'] ?? 'Unknown',
            'marital_status'  => $parsed['marital_status'] ?? 'Unknown',
            'birth_date'      => $parsed['birth_date'] ?? null,
            'death_date'      => $parsed['death_date'] ?? null,
        ];

        // Type-specific mappings
        switch ($type) {
            case 'baptism':
                $base['candidate_name'] = $userName;
                $base['first_name'] = $userName;
                $base['father_name'] = $parsed['father'] ?? '';
                $base['baptism_date'] = $appointmentDate;
                $base['category'] = 'Baptism';
                break;

            case 'communion':
                $base['candidate_name'] = $userName;
                $base['communion_date'] = $appointmentDate;
                $base['baptism_date'] = $parsed['baptism_date'] ?? null;
                $base['category'] = 'Communion';
                break;

            case 'confirmation':
                $base['candidate_name'] = $userName;
                $base['confirmation_date'] = $appointmentDate;
                $base['father_name'] = $parsed['father'] ?? '';
                $base['category'] = 'Confirmation';
                break;

            case 'wedding':
                $dateObj = Carbon::parse($appointmentDate);
                $base['groom_name'] = $parsed['groom'] ?? $userName;
                $base['bride_name'] = $parsed['bride'] ?? '';
                $base['year'] = $dateObj->year;
                $base['month_day'] = $dateObj->format('m-d');
                $base['category'] = 'Wedding';
                $base['wedding_date'] = $appointmentDate;
                break;

            case 'funeral':
                $base['deceased_name'] = $userName;
                $base['burial_date'] = $appointmentDate;
                $base['category'] = 'Funeral';
                break;

            default:
                // fallback
                $base['category'] = ucfirst($type);
        }

        // Clean up: remove null values to avoid DB errors
        return array_filter($base, function ($value) {
            return !is_null($value);
        });
    }

    /**
     * Parse the 'details' string to extract extra fields.
     * Expects lines like "Father: John Doe" or plain text.
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

                switch ($key) {
                    case 'father':
                    case 'fathers name':
                        $result['father'] = $value;
                        break;
                    case 'mother':
                    case 'mothers name':
                        $result['mother'] = $value;
                        break;
                    case 'groom':
                    case 'groom name':
                        $result['groom'] = $value;
                        break;
                    case 'bride':
                    case 'bride name':
                        $result['bride'] = $value;
                        break;
                    case 'sponsor':
                    case 'sponsor name':
                        $result['sponsor'] = $value;
                        break;
                    case 'godfather':
                        $result['godfather'] = $value;
                        break;
                    case 'godmother':
                        $result['godmother'] = $value;
                        break;
                    case 'minister':
                    case 'minister name':
                        $result['minister'] = $value;
                        break;
                    case 'age':
                        $result['age'] = intval($value);
                        break;
                    case 'email':
                        $result['email'] = $value;
                        break;
                    case 'contact':
                    case 'phone':
                    case 'contact number':
                        $result['contact'] = $value;
                        break;
                    case 'birthplace':
                    case 'place of birth':
                        $result['birthplace'] = $value;
                        break;
                    case 'baptism place':
                    case 'place of baptism':
                        $result['baptism_place'] = $value;
                        break;
                    case 'baptism date':
                        $result['baptism_date'] = $value;
                        break;
                    case 'death date':
                        $result['death_date'] = $value;
                        break;
                    case 'residence':
                        $result['residence'] = $value;
                        break;
                    case 'legitimacy':
                        $result['legitimacy'] = $value;
                        break;
                    case 'marital status':
                        $result['marital_status'] = $value;
                        break;
                    default:
                        // Store any other key-value pairs
                        $result[$key] = $value;
                        break;
                }
            } else {
                // Plain text – try to interpret
                // If no other name is found, assume it's the candidate name
                if (empty($result['candidate']) && empty($result['name'])) {
                    $result['name'] = $line;
                }
            }
        }

        return $result;
    }
}