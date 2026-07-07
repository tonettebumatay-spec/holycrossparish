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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show the centralized booking form (web).
     */
    public function create()
    {
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
                $model->first_name = $validated['user_name'];
                $model->last_name = $validated['contact_number'];
                $model->baptism_date = $appointmentDate;
                $model->remarks = $details;
                $model->save();
                break;
            case 'communion':
                $model = new Communion();
                $model->candidate_name = $validated['user_name'];
                $model->residence = $validated['contact_number'];
                $model->communion_date = $appointmentDate;
                $model->remarks = $details;
                $model->save();
                break;
            case 'confirmation':
                $model = new Confirmation();
                $model->candidate_name = $validated['user_name'];
                $model->parents_residence = $validated['contact_number'];
                $model->confirmation_date = $appointmentDate;
                $model->remarks = $details;
                $model->save();
                break;
            case 'wedding':
                $model = new Wedding();
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
     * Generic API store method – with explicit column filtering.
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

            // ----- Extract fields -----
            $appointmentDate = $request->input('appointment_date') 
                             ?? $request->input('preferred_date') 
                             ?? $request->input('date') 
                             ?? '';

            $contactNumber   = $request->input('contact_number') 
                             ?? $request->input('phone') 
                             ?? $request->input('phone_number') 
                             ?? '';

            $details         = $request->input('details') ?? '';

            $parsed = $this->parseDetails($details);

            $name = $request->input('confirmand_name') 
                  ?? $request->input('candidate_name') 
                  ?? $request->input('child_name') 
                  ?? $request->input('name') 
                  ?? $request->input('deceased_name') 
                  ?? $parsed['name'] 
                  ?? '';

            $second = $request->input('father_name') 
                    ?? $request->input('sponsor_name') 
                    ?? $request->input('parent_name') 
                    ?? $parsed['second'] 
                    ?? '';

            if (empty($second) && $type === 'confirmation') {
                $second = $contactNumber ?: 'N/A';
            }

            $email = $request->input('email') 
                   ?? $request->input('email_address') 
                   ?? $parsed['email'] 
                   ?? '';

            $purpose = $request->input('purpose') 
                     ?? $parsed['purpose'] 
                     ?? 'Book ' . ucfirst($type);

            Log::info("API_BOOKING_PARSED_{$type}", compact('purpose', 'name', 'second', 'email', 'contactNumber', 'appointmentDate'));

            // ----- Validation -----
            $rules = [
                'purpose'        => 'required|string|max:255',
                'appointmentDate'=> 'required|date',
                'name'           => 'required|string|max:255',
                'email'          => 'required|email|max:255',
                'contactNumber'  => 'nullable|string|max:20',
            ];
            if ($type !== 'confirmation') {
                $rules['second'] = 'required|string|max:255';
            }

            $validator = Validator::make(
                compact('purpose', 'appointmentDate', 'name', 'second', 'email', 'contactNumber'),
                $rules,
                [
                    'purpose.required'        => 'Purpose is required',
                    'appointmentDate.required'=> 'Appointment date is required',
                    'appointmentDate.date'    => 'Appointment date must be a valid date',
                    'name.required'           => 'Name is required',
                    'second.required'         => 'Father/Sponsor name is required',
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
                    'parsed_data' => compact('purpose', 'appointmentDate', 'name', 'second', 'email', 'contactNumber'),
                ], 422);
            }

            // ----- Build data from fillable columns -----
            $model = new $modelClass();
            $fillable = $model->getFillable();

            $mappedData = [];
            foreach ($fillable as $column) {
                $mappedData[$column] = $this->getDefaultForColumn($type, $column, $purpose, $appointmentDate, $name, $second, $email, $contactNumber);
            }

            // ----- FILTER OUT COLUMNS THAT DON'T EXIST IN THE ACTUAL TABLE -----
            $table = $model->getTable();
            $actualColumns = DB::getSchemaBuilder()->getColumnListing($table);
            foreach ($mappedData as $column => $value) {
                if (!in_array($column, $actualColumns)) {
                    unset($mappedData[$column]);
                    Log::info("API_BOOKING_REMOVED_COLUMN_{$type}", ['column' => $column]);
                }
            }

            Log::info("API_BOOKING_FINAL_DATA_{$type}", $mappedData);

            // ----- Create record -----
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
     * Parse the 'details' string – handles both key:value and plain text.
     */
    private function parseDetails(string $details): array
    {
        $result = [
            'purpose' => '',
            'name'    => '',
            'second'  => '',
            'email'   => '',
        ];

        if (empty($details)) {
            return $result;
        }

        $lines = explode("\n", $details);
        $values = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (strpos($line, ':') !== false) {
                $parts = explode(':', $line, 2);
                $key = trim($parts[0]);
                $value = trim($parts[1] ?? '');

                $lowerKey = strtolower($key);

                if (str_contains($lowerKey, 'purpose') || str_contains($lowerKey, 'service')) {
                    $result['purpose'] = $value;
                } elseif (str_contains($lowerKey, 'name') || str_contains($lowerKey, 'confirmand') || 
                          str_contains($lowerKey, 'candidate') || str_contains($lowerKey, 'child') || 
                          str_contains($lowerKey, 'deceased')) {
                    $result['name'] = $value;
                } elseif (str_contains($lowerKey, 'father') || str_contains($lowerKey, 'sponsor') || 
                          str_contains($lowerKey, 'parent')) {
                    $result['second'] = $value;
                } elseif (str_contains($lowerKey, 'email') || str_contains($lowerKey, 'mail')) {
                    $result['email'] = $value;
                } else {
                    $values[] = $value;
                }
            } else {
                $values[] = $line;
            }
        }

        // Fill missing fields from plain values
        if (empty($result['name']) && !empty($values)) {
            $result['name'] = array_shift($values);
        }
        if (empty($result['second']) && !empty($values)) {
            $result['second'] = array_shift($values);
        }
        if (empty($result['email']) && !empty($values)) {
            foreach ($values as $val) {
                if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $result['email'] = $val;
                    break;
                }
            }
        }
        if (empty($result['purpose'])) {
            $result['purpose'] = 'Book Service';
        }

        return $result;
    }

    /**
     * Get a safe default value for each fillable column.
     */
    private function getDefaultForColumn(string $type, string $column, string $purpose, string $date, string $name, string $second, string $email, string $contactNumber)
    {
        // Common defaults for all tables
        $common = [
            'remarks' => "Purpose: $purpose | Email: $email | Contact: $contactNumber | Second: $second",
            'book_number' => 0,
            'page_number' => 0,
            'line_number' => 0,
            'status' => 'pending',
            'minister_name' => '',
            'residence' => $contactNumber,
            'parents_residence' => $contactNumber,
            'sponsor_name' => '',
            'sponsors' => '',
            'godfather' => '',
            'godmother' => '',
            'mother_name' => '',
            'mother_maiden_name' => '',
            'middle_name' => '',
            'suffix' => '',
            'birthplace' => '',
            'birth_place' => '',
            'father_birthplace' => '',
            'mother_birthplace' => '',
            'place_of_baptism' => '',
            'coordinator_name' => '',
            'cemetery_name' => '',
            'cause_of_death' => '',
            'sacraments_received' => '',
            'spouse_name' => '',
            'groom_status' => '',
            'bride_status' => '',
            'groom_father' => '',
            'groom_mother' => '',
            'groom_parents' => '',
            'groom_parents_residence' => '',
            'groom_residence' => '',
            'bride_father' => '',
            'bride_mother' => '',
            'bride_parents' => '',
            'bride_parents_residence' => '',
            'bride_residence' => '',
            'witness_1' => '',
            'witness_2' => '',
            'first_name' => $name,
            'last_name' => '',
            'candidate_name' => $name,
            'deceased_name' => $name,
            'father_name' => $second,
            'age' => 0,
            'age_at_death' => 0,
            'groom_age' => 0,
            'bride_age' => 0,
            'legitimacy' => 'Unknown',
            'marital_status' => 'Unknown',
            'birth_date' => '1900-01-01',
            'death_date' => '1900-01-01',
            'baptism_date' => $date,
            'communion_date' => $date,
            'confirmation_date' => $date,
            'burial_date' => $date,
            'wedding_date' => $date,
        ];

        // Type-specific overrides
        $overrides = [];
        switch ($type) {
            case 'wedding':
                $dateObj = Carbon::parse($date);
                $overrides = [
                    'groom_name' => $second,
                    'bride_name' => $name,
                    'year' => $dateObj->year,
                    'month_day' => $dateObj->format('m-d'),
                    'category' => 'Wedding',
                ];
                break;
            case 'baptism':
                $overrides = [
                    'first_name' => $name,
                    'father_name' => $second,
                    'baptism_date' => $date,
                    'legitimacy' => 'Unknown',
                    'birth_date' => '1900-01-01',
                    'candidate_name' => $name,
                    'category' => 'Baptism',
                ];
                break;
            case 'communion':
                $overrides = [
                    'candidate_name' => $name,
                    'communion_date' => $date,
                    'baptism_date' => '1900-01-01',
                    'category' => 'Communion',
                ];
                break;
            case 'confirmation':
                $overrides = [
                    'candidate_name' => $name,
                    'confirmation_date' => $date,
                    'father_name' => $second,
                    'parents_residence' => $contactNumber,
                    'category' => 'Confirmation',
                ];
                break;
            case 'funeral':
                $overrides = [
                    'deceased_name' => $name,
                    'burial_date' => $date,
                    'marital_status' => 'Unknown',
                    'death_date' => '1900-01-01',
                    'category' => 'Funeral',
                ];
                break;
            default:
                break;
        }

        return array_merge($common, $overrides)[$column] ?? '';
    }
}