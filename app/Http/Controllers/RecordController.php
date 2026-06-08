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

class RecordController extends Controller
{
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
        $category = $request->query('category', 'baptism');
        $book_number = $request->query('book_number', 1);
        $record = $this->resolveModel($category)->findOrFail($id);
        
        return view('records.edit', compact('record', 'category', 'book_number'));
    }

    public function update(Request $request, $id)
    {
        $category = strtolower($request->category ?? '');
        $request->validate(['category' => 'required|string', 'book_number' => 'required|integer']);
        
        $record = $this->resolveModel($category)->findOrFail($id);
        $record->update($request->except(['_token', '_method', 'category']));
        
        return redirect()->route('records.index', ['category' => $category, 'book_number' => $request->book_number])
                         ->with('success', 'Record successfully updated!');
    }

    public function destroy($id, Request $request)
    {
        $category = strtolower($request->query('category', 'baptism'));
        $this->resolveModel($category)->findOrFail($id)->delete();
        
        return redirect()->route('records.index', ['category' => $category, 'book_number' => $request->query('book_number', 1)])
                         ->with('success', 'Record deleted!');
    }

    /**
     * Store a newly created record.
     */
public function store(Request $request)
{
    $category = strtolower($request->category ?? '');
    
    // 1) Validation rules (match your Baptism create form inputs)
    $request->validate([
        'category' => 'required|string',
        'book_number' => 'required|integer',
        // NOTE: your Blade form uses: candidate_name + birth_date
        // The controller's duplicate logic will extract names from candidate_name.
        'candidate_name' => 'required|string',
        'birth_date' => 'required|date',
        // Ensure fields required by the baptisms table are present
        'baptism_date' => 'required|date',
        'birth_place' => 'required|string',
'father_name' => 'required|string',
        'mother_name' => 'required|string',
        // Your form field is named `residence`; however your submit may not always include it.
        // Default to null to prevent SQL errors.
        'residence' => 'nullable|string',
        'minister_name' => 'required|string',
        'legitimacy' => 'required|string',
    ]);

    // 2) Duplicate Check for Baptisms based on (first_name, last_name, birth_date)
    // We parse the first and last name from candidate_name ("First M.I. Surname" in your form).
    if ($category === 'baptism') {
        $candidateName = trim((string) $request->input('candidate_name'));
        $parts = preg_split('/\s+/', $candidateName);

        $firstName = $parts[0] ?? null;
        $lastName = count($parts) ? $parts[count($parts) - 1] : null;

        // If we can't parse names reliably, fail validation so the user sees an error.
        if (!$firstName || !$lastName) {
            $request->validate([
                'candidate_name' => [
                    function ($attribute, $value, $fail) {
                        $fail('Please provide a valid candidate name (First ... Surname).');
                    }
                ],
            ]);
        }

        $exists = \App\Models\Baptism::where('first_name', $firstName)
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

        // Map candidate_name into the DB columns expected by the Baptism model.
        // Keeps the existing resolveModel + fill($request->all()) approach working.
        $request->merge([
            'first_name' => $firstName,
            'last_name' => $lastName,
            // Your form uses mother_name, but the baptisms table column is mother_maiden_name
            'mother_maiden_name' => $request->input('mother_name'),
        ]);

        // Default optional fields to null to avoid SQL errors.
        $request->merge([
            'mother_birthplace' => $request->input('mother_birthplace'),
            'father_birthplace' => $request->input('father_birthplace'),
            'godfather' => $request->input('godfather'),
            'godmother' => $request->input('godmother'),
            // Column `residence` is NOT nullable in your migration, but your form may submit without it.
            // Only set it if present; otherwise provide a safe fallback.
            'residence' => $request->input('residence') ?: 'N/A',
        ]);
    }

    // 3. Save the record
    $model = $this->resolveModel($category);
    $model->fill($request->all())->save();

    return redirect()->route('records.index', ['category' => $category, 'book_number' => $request->book_number])
                     ->with('success', 'Record successfully saved!');
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