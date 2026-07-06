<?php

namespace App\Http\Controllers;

use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Show the centralized booking form.
     *
     * - Counts how many existing bookings exist per specific date across:
     *   baptisms, weddings, communions, confirmations, funerals.
     * - If the total for a date is >= 3, that date is marked as FULL.
     * - Also disables ALL Mondays in the UI (Flatpickr) via JS.
     */
    public function create()
    {
        // Service => [ModelClass, date column mapping]
        $serviceDateMap = [
            'baptism' => [Baptism::class, 'baptism_date'],
            'communion' => [Communion::class, 'communion_date'],
            'confirmation' => [Confirmation::class, 'confirmation_date'],
            // Your weddings table migration uses year + month_day (no wedding_date)
            'wedding' => [Wedding::class, null],
            // Your funerals table uses burial_date as the booking-relevant date
            'funeral' => [Funeral::class, 'burial_date'],
        ];

        $countsByDate = [];

        // Baptism/Communion/Confirmation/Funeral: single date column
        foreach (['baptism', 'communion', 'confirmation', 'funeral'] as $serviceKey) {
            [$modelClass, $dateColumn] = $serviceDateMap[$serviceKey];

            // Guard in case date column doesn't exist (prevents runtime errors)
            if (!is_string($dateColumn) || empty($dateColumn)) {
                continue;
            }

            $rows = $modelClass::query()
                ->selectRaw("DATE({$dateColumn}) as booking_date, COUNT(*) as total")
                ->groupBy('booking_date')
                ->get();

            foreach ($rows as $row) {
                $date = (string) $row->booking_date;
                if ($date === '') {
                    continue;
                }
                $countsByDate[$date] = ($countsByDate[$date] ?? 0) + (int) $row->total;
            }
        }

        // Wedding: combine year + month_day into a YYYY-MM-DD string
        // wedding table migration shown uses: year (string) + month_day (string like "05-21")
        $rows = Wedding::query()
            ->selectRaw('year, month_day, COUNT(*) as total')
            ->groupBy('year', 'month_day')
            ->get();

        foreach ($rows as $row) {
            $year = trim((string) $row->year);
            $monthDay = trim((string) $row->month_day);

            if ($year === '' || $monthDay === '') {
                continue;
            }

            // Normalize common formats:
            // - month_day might be "MM-DD"; build "YYYY-MM-DD"
            // - if it's already "YYYY-MM-DD" just trust it
            $date = $monthDay;

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $monthDay)) {
                $date = $monthDay;
            } elseif (preg_match('/^(\d{1,2})-(\d{1,2})$/', $monthDay, $m)) {
                $mm = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                $dd = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                $date = $year . '-' . $mm . '-' . $dd;
            } else {
                // Unknown format; skip
                continue;
            }

            // Basic sanity check: must look like YYYY-MM-DD
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                continue;
            }


            $countsByDate[$date] = ($countsByDate[$date] ?? 0) + (int) $row->total;
        }

        // Full dates: total >= 3
        $fullDates = array_values(array_filter(
            array_keys($countsByDate),
            fn ($date) => ($countsByDate[$date] ?? 0) >= 3
        ));

        // Sort for consistent UX
        sort($fullDates);

        return view('booking.create', [
            'fullDates' => $fullDates,
        ]);
    }

    /**
     * Store the centralized booking request.
     * Saves into the appropriate table based on service_type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type' => ['required', 'string', 'in:baptism,wedding,communion,confirmation,funeral'],
            'user_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'appointment_date' => ['required', 'date'],
            'details' => ['nullable', 'string', 'max:2000'],
        ], [
            'appointment_date.date' => 'Please provide a valid date.',
        ]);

        $serviceType = $validated['service_type'];
        $appointmentDate = $validated['appointment_date'];
        $details = $validated['details'] ?? null;

        // We don’t have a dedicated centralized bookings table yet.
        // Since the service tables do not share the same column names for request fields,
        // we store user_name/contact_number/details into commonly-available columns.
        // - Baptism/Communion/Confirmation/Funeral: we use minister_name (or remarks) for admin notes.
        // - Wedding: uses groom_name/bride_name/remarks in a best-effort way.
        // If you later add a dedicated bookings table, this controller can be simplified.

        switch ($serviceType) {
            case 'baptism':
                $model = new Baptism();
                $model->category = 'Baptism';
                // Put request info into available columns
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

                // Best-effort mapping into available columns
                $model->groom_name = $validated['user_name'];
                $model->bride_name = $validated['contact_number'];
                $model->remarks = $details;

                // These columns exist in model fillable list; DB migration may not require them,
                // but to avoid SQL errors on NOT NULL constraints, we keep minimal assignments.
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
}

