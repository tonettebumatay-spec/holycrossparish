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
    // ... keep your existing create() and store() methods unchanged ...

    // ============================================================
    // API METHODS (Called by Android app)
    // ============================================================

    public function storeBaptism(Request $request)
    {
        return $this->storeSacrament($request, 'baptism');
    }

    public function storeWedding(Request $request)
    {
        return $this->storeSacrament($request, 'wedding');
    }

    public function storeCommunion(Request $request)
    {
        return $this->storeSacrament($request, 'communion');
    }

    public function storeConfirmation(Request $request)
    {
        return $this->storeSacrament($request, 'confirmation');
    }

    public function storeFuneral(Request $request)
    {
        return $this->storeSacrament($request, 'funeral');
    }

    /**
     * Generic API store method with flexible parsing of the 'details' field.
     */
    private function storeSacrament(Request $request, string $type)
    {
        try {
            Log::info("API_BOOKING_RAW_{$type}", $request->all());

            $modelMap = [
                'baptism'     => Baptism::class,
                'wedding'     => Wedding::class,
                'communion'   => Communion::class,
                'confirmation'=> Confirmation::class,
                'funeral'     => Funeral::class,
            ];
            if (!isset($modelMap[$type])) {
                return response()->json(['success' => false, 'message' => 'Invalid service type'], 400);
            }
            $modelClass = $modelMap[$type];

            // Extract fields from Android payload
            $appointmentDate = $request->input('appointment_date') ?? $request->input('preferred_date') ?? '';
            $contactNumber   = $request->input('contact_number') ?? $request->input('phone') ?? '';
            $details         = $request->input('details') ?? '';

            // ----- FLEXIBLE PARSING OF DETAILS -----
            $parsed = $this->parseDetails($details);

            // Merge parsed values with direct inputs (if any)
            $purpose   = $parsed['purpose'] ?? $request->input('purpose') ?? '';
            $childName = $parsed['name'] ?? $request->input('child_name') ?? $request->input('candidate_name') ?? '';
            $fatherName= $parsed['father'] ?? $request->input('father_name') ?? '';
            $email     = $parsed['email'] ?? $request->input('email') ?? '';

            // Log the extracted data for debugging
            Log::info("API_BOOKING_PARSED_{$type}", [
                'purpose' => $purpose,
                'childName' => $childName,
                'fatherName' => $fatherName,
                'email' => $email,
                'contactNumber' => $contactNumber,
                'appointmentDate' => $appointmentDate,
            ]);

            // ----- VALIDATION -----
            $validator = Validator::make(
                compact('purpose', 'appointmentDate', 'childName', 'fatherName', 'email', 'contactNumber'),
                [
                    'purpose'        => 'required|string|max:255',
                    'appointmentDate'=> 'required|date',
                    'childName'      => 'required|string|max:255',
                    'fatherName'     => 'required|string|max:255',
                    'email'          => 'required|email|max:255',
                    'contactNumber'  => 'nullable|string|max:20',
                ],
                [
                    'purpose.required'        => 'Purpose is required',
                    'appointmentDate.required'=> 'Appointment date is required',
                    'appointmentDate.date'    => 'Appointment date must be a valid date',
                    'childName.required'      => "Name is required (child/candidate/deceased)",
                    'fatherName.required'     => "Father/Sponsor name is required",
                    'email.required'          => 'Email address is required',
                    'email.email'             => 'Email must be a valid email address',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                    'received_data' => $request->all(),
                    'parsed_data' => compact('purpose', 'appointmentDate', 'childName', 'fatherName', 'email', 'contactNumber'),
                ], 422);
            }

            // ----- MAP TO MODEL COLUMNS -----
            $mappedData = $this->mapApiFields($type, $purpose, $appointmentDate, '', $childName, $fatherName, $email, $contactNumber);

            // Auto-fill any missing fillable columns with safe defaults
            $model = new $modelClass();
            $fillable = $model->getFillable();
            foreach ($fillable as $column) {
                if (!array_key_exists($column, $mappedData)) {
                    if (in_array($column, ['book_number', 'page_number', 'line_number', 'year', 'age', 'age_at_death', 'groom_age', 'bride_age'])) {
                        $mappedData[$column] = 0;
                    } elseif (strpos($column, 'date') !== false) {
                        $mappedData[$column] = '1900-01-01';
                    } elseif (in_array($column, ['legitimacy', 'marital_status', 'status'])) {
                        $mappedData[$column] = 'pending';
                    } else {
                        $mappedData[$column] = '';
                    }
                }
            }

            Log::info("API_BOOKING_FINAL_DATA_{$type}", $mappedData);

            // ----- CREATE RECORD -----
            try {
                $booking = $modelClass::create($mappedData);
            } catch (\Exception $e) {
                Log::error("API_BOOKING_CREATE_ERROR_{$type}", [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'mapped_data' => $mappedData
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage()
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' booking created successfully!',
                'booking' => $booking
            ], 201);

        } catch (\Exception $e) {
            Log::error("API_BOOKING_ERROR_{$type}", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse the 'details' string to extract key fields.
     * Handles multiple possible labels.
     */
    private function parseDetails(string $details): array
    {
        $result = [
            'purpose' => '',
            'name'    => '',
            'father'  => '',
            'email'   => '',
        ];

        if (empty($details)) {
            return $result;
        }

        // Split into lines
        $lines = explode("\n", $details);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Try to match "Purpose: ..."
            if (preg_match('/^Purpose:\s*(.+)/i', $line, $matches)) {
                $result['purpose'] = trim($matches[1]);
                continue;
            }

            // Try to match "Child:" or "Candidate:" or "Deceased:" or "Name:"
            if (preg_match('/^(Child|Candidate|Deceased|Name):\s*(.+)/i', $line, $matches)) {
                $result['name'] = trim($matches[2]);
                continue;
            }

            // Try to match "Father:" or "Sponsor:" or "Parent:"
            if (preg_match('/^(Father|Sponsor|Parent):\s*(.+)/i', $line, $matches)) {
                $result['father'] = trim($matches[2]);
                continue;
            }

            // Try to match "Email:"
            if (preg_match('/^Email:\s*(.+)/i', $line, $matches)) {
                $result['email'] = trim($matches[1]);
                continue;
            }
        }

        return $result;
    }

    /**
     * Map API fields to the correct database columns for each model.
     * Provides defaults for NOT NULL columns.
     */
    private function mapApiFields(string $type, string $purpose, string $date, string $time, string $childName, string $fatherName, string $email, string $contactNumber = '')
    {
        $base = [
            'category' => ucfirst($type),
            'remarks'  => "Purpose: $purpose | Time: $time | Email: $email | Contact: $contactNumber | Father: $fatherName",
            'book_number' => 0,
            'page_number' => 0,
            'line_number' => 0,
        ];

        switch ($type) {
            case 'baptism':
                return array_merge($base, [
                    'first_name'   => $childName,
                    'last_name'    => '',
                    'baptism_date' => $date,
                    'father_name'  => $fatherName,
                    'residence'    => $contactNumber,
                    'legitimacy'   => 'Unknown',
                    'birth_date'   => '1900-01-01',
                    'birth_place'  => '',
                    'father_birthplace' => '',
                    'mother_birthplace' => '',
                    'minister_name' => '',
                    'godfather'    => '',
                    'godmother'    => '',
                    'mother_name'  => '',
                    'mother_maiden_name'=> '',
                    'middle_name'  => '',
                    'suffix'       => '',
                    'candidate_name'=> $childName,
                ]);
            case 'communion':
                return array_merge($base, [
                    'candidate_name' => $childName,
                    'communion_date' => $date,
                    'residence'      => $contactNumber,
                    'baptism_date'   => '1900-01-01',
                    'place_of_baptism' => '',
                    'coordinator_name' => '',
                    'minister_name'  => '',
                    'first_name'     => '',
                    'last_name'      => '',
                ]);
            case 'confirmation':
                return array_merge($base, [
                    'candidate_name'    => $childName,
                    'confirmation_date' => $date,
                    'father_name'       => $fatherName,
                    'parents_residence' => $contactNumber,
                    'sponsor_name'      => '',
                    'age'               => 0,
                    'birthplace'        => '',
                    'minister_name'     => '',
                    'sponsors'          => '',
                ]);
            case 'funeral':
                return array_merge($base, [
                    'deceased_name' => $childName,
                    'burial_date'   => $date,
                    'residence'     => $contactNumber,
                    'marital_status' => '',
                    'spouse_name'   => '',
                    'death_date'    => '1900-01-01',
                    'age_at_death'  => 0,
                    'cause_of_death' => '',
                    'sacraments_received' => '',
                    'cemetery_name' => '',
                    'minister_name' => '',
                ]);
            case 'wedding':
                $dateObj = Carbon::parse($date);
                return array_merge($base, [
                    'year'      => $dateObj->year,
                    'month_day' => $dateObj->format('m-d'),
                    'groom_name'=> $fatherName,
                    'bride_name'=> $childName,
                    'groom_age' => 0,
                    'groom_status' => '',
                    'groom_father' => '',
                    'groom_mother' => '',
                    'groom_residence' => '',
                    'bride_age' => 0,
                    'bride_status' => '',
                    'bride_father' => '',
                    'bride_mother' => '',
                    'bride_residence' => '',
                    'wedding_date' => $date,
                    'minister_name' => '',
                    'witness_1' => '',
                    'witness_2' => '',
                ]);
            default:
                return [];
        }
    }
}