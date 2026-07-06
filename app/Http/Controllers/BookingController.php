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
        // ... keep your existing create() method exactly as you have it ...
        $serviceDateMap = [
            'baptism'     => [Baptism::class, 'baptism_date'],
            'communion'   => [Communion::class, 'communion_date'],
            'confirmation'=> [Confirmation::class, 'confirmation_date'],
            'wedding'     => [Wedding::class, null],
            'funeral'     => [Funeral::class, 'burial_date'],
        ];

        $countsByDate = [];

        foreach (['baptism', 'communion', 'confirmation', 'funeral'] as $serviceKey) {
            [$modelClass, $dateColumn] = $serviceDateMap[$serviceKey];
            if (!is_string($dateColumn) || empty($dateColumn)) {
                continue;
            }
            $rows = $modelClass::query()
                ->selectRaw("DATE({$dateColumn}) as booking_date, COUNT(*) as total")
                ->groupBy('booking_date')
                ->get();
            foreach ($rows as $row) {
                $date = (string) $row->booking_date;
                if ($date === '') continue;
                $countsByDate[$date] = ($countsByDate[$date] ?? 0) + (int) $row->total;
            }
        }

        $rows = Wedding::query()
            ->selectRaw('year, month_day, COUNT(*) as total')
            ->groupBy('year', 'month_day')
            ->get();

        foreach ($rows as $row) {
            $year = trim((string) $row->year);
            $monthDay = trim((string) $row->month_day);
            if ($year === '' || $monthDay === '') continue;
            $date = $monthDay;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $monthDay)) {
                $date = $monthDay;
            } elseif (preg_match('/^(\d{1,2})-(\d{1,2})$/', $monthDay, $m)) {
                $mm = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                $dd = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                $date = $year . '-' . $mm . '-' . $dd;
            } else {
                continue;
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) continue;
            $countsByDate[$date] = ($countsByDate[$date] ?? 0) + (int) $row->total;
        }

        $fullDates = array_values(array_filter(
            array_keys($countsByDate),
            fn ($date) => ($countsByDate[$date] ?? 0) >= 3
        ));
        sort($fullDates);

        return view('booking.create', [
            'fullDates' => $fullDates,
        ]);
    }

    /**
     * Store the centralized booking request (web).
     */
    public function store(Request $request)
    {
        // ... keep your existing store() method exactly as you have it ...
        $validated = $request->validate([
            'service_type' => ['required', 'string', 'in:baptism,wedding,communion,confirmation,funeral'],
            'user_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'appointment_date' => ['required', 'date'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $serviceType = $validated['service_type'];
        $appointmentDate = $validated['appointment_date'];
        $details = $validated['details'] ?? null;

        switch ($serviceType) {
            case 'baptism':
                $model = new Baptism();
                $model->category = 'Baptism';
                $model->first_name = $validated['user_name'];
                $model->last_name = $validated['contact_number'];
                $model->baptism_date = $appointmentDate;
                $model->remarks = $details;
                $model->save();
                break;
            case 'communion':
                $model = new Communion();
                $model->category = 'Communion';
                $model->candidate_name = $validated['user_name'];
                $model->residence = $validated['contact_number'];
                $model->communion_date = $appointmentDate;
                $model->remarks = $details;
                $model->save();
                break;
            case 'confirmation':
                $model = new Confirmation();
                $model->category = 'Confirmation';
                $model->candidate_name = $validated['user_name'];
                $model->parents_residence = $validated['contact_number'];
                $model->confirmation_date = $appointmentDate;
                $model->sponsors = $details;
                $model->save();
                break;
            case 'wedding':
                $model = new Wedding();
                $model->category = 'Wedding';
                $date = date('Y-m-d', strtotime($appointmentDate));
                $model->year = date('Y', strtotime($date));
                $model->month_day = date('m-d', strtotime($date));
                $model->groom_name = $validated['user_name'];
                $model->bride_name = $validated['contact_number'];
                $model->remarks = $details;
                $model->save();
                break;
            case 'funeral':
                $model = new Funeral();
                $model->category = 'Funeral';
                $model->deceased_name = $validated['user_name'];
                $model->residence = $validated['contact_number'];
                $model->burial_date = $appointmentDate;
                $model->remarks = $details;
                $model->save();
                break;
            default:
                abort(422, 'Invalid service type.');
        }

        return redirect()->route('booking.create')->with('success', 'Booking request submitted successfully.');
    }

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
     * Generic API store method – with auto-fill for missing fillable columns.
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

            // ----- AUTO-FILL MISSING FILLABLE COLUMNS WITH SAFE DEFAULTS -----
            $model = new $modelClass();
            $fillable = $model->getFillable();

            foreach ($fillable as $column) {
                if (!array_key_exists($column, $mappedData)) {
                    // Default based on column name
                    if (in_array($column, ['book_number', 'page_number', 'line_number', 'year', 'age', 'age_at_death', 'groom_age', 'bride_age'])) {
                        $mappedData[$column] = 0;
                    } elseif (strpos($column, 'date') !== false) {
                        // For date columns, use a placeholder date if NOT NULL
                        $mappedData[$column] = '1900-01-01';
                    } elseif (in_array($column, ['legitimacy', 'marital_status'])) {
                        $mappedData[$column] = 'Unknown';
                    } else {
                        $mappedData[$column] = '';
                    }
                }
            }

            Log::info("API_BOOKING_FINAL_DATA_{$type}", $mappedData);

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
     * Map API fields to model columns – provides initial values.
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
                    'birth_date'   => '1900-01-01', // ✅ fixed – NOT NULL column
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
                    'baptism_date'   => null,
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