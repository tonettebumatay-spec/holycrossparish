<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Baptism;
use App\Models\Communion;
use App\Models\Confirmation;
use App\Models\Wedding;
use App\Models\Funeral;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecordController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the list of record categories (Books) or dynamic listings.
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $book_number = $request->query('book_number');

        if ($category && $book_number !== null) {
            $title = strtoupper($category) . ' BOOK ' . $book_number;
            $records = collect();

            switch (strtolower($category)) {
                case 'baptism':     $records = Baptism::where('book_number', $book_number)->get(); break;
                case 'communion':   $records = Communion::where('book_number', $book_number)->get(); break;
                case 'confirmation': $records = Confirmation::where('book_number', $book_number)->get(); break;
                case 'wedding':     $records = Wedding::where('book_number', $book_number)->get(); break;
                case 'funeral':     $records = Funeral::where('book_number', $book_number)->get(); break;
            }

            $viewName = 'records.' . strtolower($category) . '_details';

            if (!view()->exists($viewName)) {
                abort(404, "The layout configuration [{$viewName}.blade.php] for this registry was not found.");
            }

            return view($viewName, [
                'category' => $category,
                'bookNumber' => $book_number,
                'title' => $title,
                'records' => $records,
            ]);
        }

        if ($category) {
            $title = strtoupper($category) . " BOOK";
            $volumes = range(1, 24);
            return view('records.volumes', compact('volumes', 'category', 'title'));
        }

        $books = [
            ['title' => 'BAPTISM', 'category' => 'baptism', 'file' => 'baprec.png'],
            ['title' => 'COMMUNION', 'category' => 'communion', 'file' => 'comrec.png'],
            ['title' => 'CONFIRMATION', 'category' => 'confirmation', 'file' => 'conrec.png'],
            ['title' => 'WEDDING', 'category' => 'wedding', 'file' => 'wedrec.png'],
            ['title' => 'FUNERAL', 'category' => 'funeral', 'file' => 'funrec.png'],
        ];

        return view('records.index', compact('books'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create(Request $request)
    {
        $this->authorize('manage-records');

        $category = $request->query('category', 'Baptism');
        $book_number = $request->query('book_number', 1);

        return view('records.create', compact('category', 'book_number'));
    }

    // --- Certificate View Methods ---
    public function showBaptism($id) { return view('records.baptism_certificate', ['record' => Baptism::findOrFail($id)]); }
    public function showCommunion($id) { return view('records.communion_certificate', ['record' => Communion::findOrFail($id)]); }
    public function showConfirmation($id) { return view('records.confirmation_certificate', ['record' => Confirmation::findOrFail($id)]); }
    public function showWedding($id) { return view('records.wedding_certificate', ['record' => Wedding::findOrFail($id)]); }
    public function showFuneral($id) { return view('records.funeral_certificate', ['record' => Funeral::findOrFail($id)]); }

    public function show($id, Request $request)
    {
        $category = $request->query('category', 'baptism');
        $model = $this->resolveModel($category);
        $record = $model->findOrFail($id);
        return view('records.show', compact('record', 'category'));
    }

    public function edit($id, Request $request)
    {
        $this->authorize('manage-records');

        $category = $request->query('category', 'baptism');
        $book_number = $request->query('book_number', 1);
        $record = $this->resolveModel($category)->findOrFail($id);

        return view('records.edit', compact('record', 'category', 'book_number'));
    }

    /**
     * Update a record – with column filtering to prevent SQL errors.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('manage-records');

        $category = strtolower($request->category ?? '');
        $request->validate([
            'category' => 'required|string',
            'book_number' => 'required|integer',
        ]);

        $record = $this->resolveModel($category)->findOrFail($id);
        $tableName = $record->getTable();

        // ✅ Filter only valid columns from the request
        $dbColumns = Schema::getColumnListing($tableName);
        $updateData = array_intersect_key(
            $request->except(['_token', '_method', 'category']),
            array_flip($dbColumns)
        );

        // Remove empty values (optional)
        $updateData = array_filter($updateData, function ($value) {
            return $value !== null && $value !== '';
        });

        $record->update($updateData);

        return redirect()->route('records.index', [
            'category' => $category,
            'book_number' => $request->book_number
        ])->with('success', 'Record successfully updated!');
    }

    /**
     * Delete a record.
     */
    public function destroy($id, Request $request)
    {
        $this->authorize('manage-records');

        $category = strtolower($request->query('category', 'baptism'));
        $book_number = $request->query('book_number', 1);

        try {
            $this->resolveModel($category)->findOrFail($id)->delete();
            return redirect()->route('records.index', [
                'category' => $category,
                'book_number' => $book_number
            ])->with('success', 'Record deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Record deletion failed: ' . $e->getMessage());
            return redirect()->route('records.index', [
                'category' => $category,
                'book_number' => $book_number
            ])->with('error', 'Failed to delete record: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created record.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-records');

        $category = strtolower($request->category ?? '');

        // 1) Define base validation rules required by EVERY single book
        $rules = [
            'category' => 'required|string',
            'book_number' => 'required|integer',
            'page_number' => 'required|integer',
            'line_number' => 'required|integer',
        ];

        // 2) Apply strict validation rules ONLY if the book category is Baptism
        if ($category === 'baptism') {
            $rules['candidate_name'] = 'required|string';
            $rules['birth_date'] = 'required|date';
            $rules['baptism_date'] = 'required|date';
            $rules['birth_place'] = 'required|string';
            $rules['father_name'] = 'required|string';
            $rules['mother_name'] = 'required|string';
            $rules['minister_name'] = 'required|string';
            $rules['legitimacy'] = 'required|string|in:Legitimate,Natural';
            $rules['residence'] = 'nullable|string';
        } else {
            $rules['legitimacy'] = 'nullable|string';
        }

        // Run the dynamic validation checklist
        $request->validate($rules);

        // Resolve the target model and fetch its distinct table name
        $model = $this->resolveModel($category);
        $tableName = $model->getTable();

        // 3) Process specific layout transformations for Baptisms
        if ($category === 'baptism') {
            $candidateName = trim((string) $request->input('candidate_name'));
            $parts = preg_split('/\s+/', $candidateName);

            // ✅ Improved: first is first name, rest is last name (handles middle names)
            $firstName = array_shift($parts);
            $lastName = implode(' ', $parts); // handles middle names

            if (!$firstName || !$lastName) {
                $request->validate([
                    'candidate_name' => [
                        function ($attribute, $value, $fail) {
                            $fail('Please provide a valid candidate name (First ... Surname).');
                        }
                    ],
                ]);
            }

            // Duplicate Check logic for Baptisms
            $exists = Baptism::where('first_name', $firstName)
                ->where('last_name', $lastName)
                ->whereDate('birth_date', $request->birth_date)
                ->exists();

            if ($exists) {
                $request->validate([
                    'candidate_name' => [
                        function ($attribute, $value, $fail) use ($firstName, $lastName, $request) {
                            $fail('A baptismal record for ' . $firstName . ' ' . $lastName . ' (born on ' . $request->birth_date . ') already exists.');
                        }
                    ],
                ]);
            }

            // Map and merge normalized keys for the Baptism table schema
            $request->merge([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'mother_maiden_name' => $request->input('mother_name'),
                'residence' => $request->input('residence') ?: 'N/A',
            ]);
        }

        // 4) Cross-book structural data normalization
        if ($category === 'confirmation' && $request->has('sponsor_name')) {
            $request->merge(['sponsors' => $request->input('sponsor_name')]);
        }

        if ($category === 'wedding' && $request->has('minister_name')) {
            $request->merge(['minister' => $request->input('minister_name')]);
        }

        // 5) DYNAMIC CLEANING FILTER: Match payload keys directly against database columns
        $dbColumns = Schema::getColumnListing($tableName);
        $saveData = array_intersect_key($request->all(), array_flip($dbColumns));

        // Save the cleaned dataset securely utilizing Eloquent model definitions
        $model->fill($saveData)->save();

        return redirect()->route('records.index', [
            'category' => $category,
            'book_number' => $request->book_number
        ])->with('success', 'Record successfully saved!');
    }

    /**
     * Helper to resolve model based on category
     */
    private function resolveModel($category)
    {
        return match (strtolower($category)) {
            'baptism' => new Baptism(),
            'communion' => new Communion(),
            'confirmation' => new Confirmation(),
            'wedding' => new Wedding(),
            'funeral' => new Funeral(),
            default => abort(400, 'Invalid category'),
        };
    }
}