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
    // ... keep your existing create() and store() methods exactly as they are ...

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
     * Generic API store method.
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

            $appointmentDate = $request->input('appointment_date') ?? $request->input('preferred_date') ?? '';
            $contactNumber   = $request->input('contact_number') ?? $request->input('phone') ?? '';
            $details         = $request->input('details') ?? '';

            $purpose = $childName = $fatherName = $email = '';

            if (!empty($details)) {
                preg_match('/Purpose:\s*([^\n]+)/i', $details, $purposeMatch);
                if (!empty($purposeMatch[1])) $purpose = trim($purposeMatch[1]);

                preg_match('/Child(?:ren)?:\s*([^\n]+)/i', $details, $childMatch);
                if (!empty($childMatch[1])) $childName = trim($childMatch[1]);

                preg_match('/Father:\s*([^\n]+)/i', $details, $fatherMatch);
                if (!empty($fatherMatch[1])) $fatherName = trim($fatherMatch[1]);

                preg_match('/Email:\s*([^\n]+)/i', $details, $emailMatch);
                if (!empty($emailMatch[1])) $email = trim($emailMatch[1]);
            }

            $purpose    = $purpose ?: $request->input('purpose') ?? '';
            $childName  = $childName ?: $request->input('child_name') ?? $request->input('candidate_name') ?? '';
            $fatherName = $fatherName ?: $request->input('father_name') ?? '';
            $email      = $email ?: $request->input('email') ?? '';

            $validator = Validator::make(
                compact('purpose', 'appointmentDate', 'childName', 'fatherName', 'email', 'contactNumber'),
                [
                    'purpose'        => 'required|string|max:255',
                    'appointmentDate'=> 'required|date',
                    'childName'      => 'required|string|max:255',
                    'fatherName'     => 'required|string|max:255',
                    'email'          => 'required|email|max:255',
                    'contactNumber'  => 'nullable|string|max:20',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                    'received_data' => $request->all()
                ], 422);
            }

            $mappedData = $this->mapApiFields($type, $purpose, $appointmentDate, '', $childName, $fatherName, $email, $contactNumber);
            Log::info("API_BOOKING_MAPPED_{$type}", $mappedData);

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
     * Map API fields to model columns – with defaults for NOT NULL columns.
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
                    // NOT NULL columns -> provide a default
                    'legitimacy'        => 'Unknown',
                    'birth_date'        => null,   // if nullable, ok
                    'birth_place'       => '',
                    'father_birthplace' => '',
                    'mother_birthplace' => '',
                    'minister_name'     => '',
                    'godfather'         => '',
                    'godmother'         => '',
                    'mother_name'       => '',
                    'mother_maiden_name'=> '',
                    'middle_name'       => '',
                    'suffix'            => '',
                    'candidate_name'    => $childName,
                ]);
            case 'communion':
                return array_merge($base, [
                    'candidate_name' => $childName,
                    'communion_date' => $date,
                    'residence'      => $contactNumber,
                    'baptism_date'   => null,
                    'place_of_baptism' => '',
                    'coordinator_name' => '',
                    'minister_name'  => '',
                    'first_name'     => '',   // if they exist
                    'last_name'      => '',
                ]);
            case 'confirmation':
                return array_merge($base, [
                    'candidate_name'    => $childName,
                    'confirmation_date' => $date,
                    'father_name'       => $fatherName,
                    'parents_residence' => $contactNumber,
                    'sponsor_name'      => '',
                    'age'               => null,
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
                    'death_date'    => null,
                    'age_at_death'  => null,
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
                    'groom_age' => null,
                    'groom_status' => '',
                    'groom_father' => '',
                    'groom_mother' => '',
                    'groom_residence' => '',
                    'bride_age' => null,
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