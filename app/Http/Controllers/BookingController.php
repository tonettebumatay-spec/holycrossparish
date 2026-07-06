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
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Show the centralized booking form (web).
     */
    public function create()
    {
        // ... keep your existing web `create` method exactly as you have it ...
        // (I'll include it below unchanged)
        $serviceDateMap = [
            'baptism' => [Baptism::class, 'baptism_date'],
            'communion' => [Communion::class, 'communion_date'],
            'confirmation' => [Confirmation::class, 'confirmation_date'],
            'wedding' => [Wedding::class, null],
            'funeral' => [Funeral::class, 'burial_date'],
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
        // ... keep your existing web `store` method exactly as you have it ...
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

    /**
     * API: Store Baptism booking.
     * Expects JSON: purpose, preferred_date, preferred_time, child_name, father_name, email
     */
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
     * Generic API store method for all sacraments.
     * Maps the Android payload to the correct model columns.
     */
    private function storeSacrament(Request $request, string $type)
    {
        try {
            Log::info("API_BOOKING_DEBUG_{$type}", $request->all());

            // Define the model class
            $modelMap = [
                'baptism'     => Baptism::class,
                'wedding'     => Wedding::class,
                'communion'   => Communion::class,
                'confirmation'=> Confirmation::class,
                'funeral'     => Funeral::class,
            ];

            if (!isset($modelMap[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid service type'
                ], 400);
            }

            $modelClass = $modelMap[$type];

            // Validation rules that match the Android payload
            $rules = [
                'purpose'          => 'required|string|max:255',
                'preferred_date'   => 'required|date',
                'preferred_time'   => 'required|string|max:20',
                'child_name'       => 'required|string|max:255',
                'father_name'      => 'required|string|max:255',
                'email'            => 'required|email|max:255',
            ];

            // For wedding, we may have bride_name, groom_name, etc. We'll keep generic.
            // The Android payload for all sacraments uses these fields.
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Map the incoming fields to the actual column names used in the database.
            // Adjust these mappings to match your table schema.
            $data = [
                // Baptism columns: first_name, last_name, baptism_date, remarks, etc.
                // We'll store:
                'purpose'          => $request->input('purpose'),
                'appointment_date' => $request->input('preferred_date'),  // we have appointment_date
                'appointment_time' => $request->input('preferred_time'),
                'child_name'       => $request->input('child_name'),
                'father_name'      => $request->input('father_name'),
                'email'            => $request->input('email'),
                'status'           => 'pending',
                // If your table has separate columns, adjust accordingly.
                // For weddings, we'll handle separately.
            ];

            // For wedding, we need to split date into year and month_day
            if ($type === 'wedding') {
                $date = date('Y-m-d', strtotime($request->input('preferred_date')));
                $data['year'] = date('Y', strtotime($date));
                $data['month_day'] = date('m-d', strtotime($date));
                // Also map groom/bride
                $data['groom_name'] = $request->input('father_name'); // if you have groom
                $data['bride_name'] = $request->input('child_name');   // placeholder
                // Remove unused keys
                unset($data['child_name'], $data['father_name'], $data['appointment_date'], $data['appointment_time']);
            }

            // For other sacraments, we need to map fields based on the actual table columns.
            // Since each table has different column names, we'll handle per type.
            // We'll create a mapping function for each.

            $mappedData = $this->mapFieldsForModel($type, $data, $request);

            // Create the record
            $booking = $modelClass::create($mappedData);

            return response()->json([
                'success'  => true,
                'message'  => ucfirst($type) . ' booking created successfully!',
                'booking'  => $booking
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
     * Map incoming data to the correct model columns for each type.
     */
    private function mapFieldsForModel(string $type, array $data, Request $request)
    {
        $mapped = [];

        switch ($type) {
            case 'baptism':
                $mapped = [
                    'first_name'     => $request->input('child_name'),
                    'last_name'      => $request->input('father_name'), // or you can split
                    'baptism_date'   => $request->input('preferred_date'),
                    'remarks'        => $request->input('purpose') . ' | ' . $request->input('preferred_time'),
                    // adjust as needed
                ];
                break;
            case 'communion':
                $mapped = [
                    'candidate_name' => $request->input('child_name'),
                    'residence'      => $request->input('email'), // placeholder
                    'communion_date' => $request->input('preferred_date'),
                    'remarks'        => $request->input('purpose') . ' | ' . $request->input('preferred_time'),
                ];
                break;
            case 'confirmation':
                $mapped = [
                    'candidate_name'   => $request->input('child_name'),
                    'parents_residence'=> $request->input('email'),
                    'confirmation_date'=> $request->input('preferred_date'),
                    'sponsors'         => $request->input('purpose') . ' | ' . $request->input('preferred_time'),
                ];
                break;
            case 'funeral':
                $mapped = [
                    'deceased_name' => $request->input('child_name'),
                    'residence'     => $request->input('email'),
                    'burial_date'   => $request->input('preferred_date'),
                    'remarks'       => $request->input('purpose') . ' | ' . $request->input('preferred_time'),
                ];
                break;
            case 'wedding':
                $date = date('Y-m-d', strtotime($request->input('preferred_date')));
                $mapped = [
                    'year'          => date('Y', strtotime($date)),
                    'month_day'     => date('m-d', strtotime($date)),
                    'groom_name'    => $request->input('father_name'),
                    'bride_name'    => $request->input('child_name'),
                    'remarks'       => $request->input('purpose') . ' | ' . $request->input('preferred_time'),
                ];
                break;
        }

        // Add common fields
        $mapped['status'] = 'pending';
        return $mapped;
    }
}