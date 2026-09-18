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
     * Search records by name across one or all sacrament tables.
     * Optional ?category=baptism limits the search to a specific sacrament.
     */
    public function search(Request $request)
    {
        $query = trim((string) $request->input('q'));
        $category = $request->input('category'); // optional filter

        $results = collect();

        // If no query, return empty results page
        if ($query === '') {
            return view('records.search_results', [
                'query' => '',
                'category' => $category,
                'results' => $results,
            ]);
        }

        // Normalize category
        $normalized = strtolower(trim((string) $category));
        $allowedCategories = ['baptism', 'communion', 'confirmation', 'wedding', 'funeral'];

        $searchIn = function ($modelClass, $type, $nameFields) use ($query) {
            $q = $modelClass::query();
            $q->where(function ($w) use ($query, $nameFields) {
                foreach ($nameFields as $field) {
                    $w->orWhere($field, 'LIKE', "%{$query}%");
                }
            });
            return $q->orderByDesc('id')->limit(50)->get()->map(function ($item) use ($type) {
                // Build display name per sacrament
                $name = match (strtolower($type)) {
                    'baptism', 'communion', 'confirmation' =>
                        trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'N/A',
                    'wedding' =>
                        trim(($item->groom_name ?? '') . ' & ' . ($item->bride_name ?? '')) ?: 'N/A',
                    'funeral' =>
                        $item->deceased_name ?? 'N/A',
                    default => 'N/A',
                };

                return [
                    'id'            => $item->id,
                    'type'          => ucfirst($type),
                    'category_slug' => strtolower($type),
                    'name'          => $name,
                    'book_number'   => $item->book_number ?? null,
                    'page_number'   => $item->page_number ?? null,
                    'line_number'   => $item->line_number ?? null,
                ];
            });
        };

        // Decide which tables to search
        if ($normalized && in_array($normalized, $allowedCategories)) {
            // Per-sacrament search
            $results = match ($normalized) {
                'baptism' => $searchIn(Baptism::class, 'baptism', [
                    'first_name', 'last_name', 'father_name', 'mother_name', 'mother_maiden_name',
                ]),
                'communion' => $searchIn(Communion::class, 'communion', [
                    'first_name', 'last_name',
                ]),
                'confirmation' => $searchIn(Confirmation::class, 'confirmation', [
                    'first_name', 'last_name', 'father_name', 'mother_name',
                ]),
                'wedding' => $searchIn(Wedding::class, 'wedding', [
                    'groom_name', 'bride_name', 'groom_parents', 'bride_parents',
                ]),
                'funeral' => $searchIn(Funeral::class, 'funeral', [
                    'deceased_name', 'spouse_name',
                ]),
            };
        } else {
            // All sacraments — search everything
            $results = collect()
                ->merge($searchIn(Baptism::class, 'baptism', [
                    'first_name', 'last_name', 'father_name', 'mother_name', 'mother_maiden_name',
                ]))
                ->merge($searchIn(Communion::class, 'communion', [
                    'first_name', 'last_name',
                ]))
                ->merge($searchIn(Confirmation::class, 'confirmation', [
                    'first_name', 'last_name', 'father_name', 'mother_name',
                ]))
                ->merge($searchIn(Wedding::class, 'wedding', [
                    'groom_name', 'bride_name',
                ]))
                ->merge($searchIn(Funeral::class, 'funeral', [
                    'deceased_name', 'spouse_name',
                ]));
        }

        return view('records.search_results', [
            'query'    => $query,
            'category' => $normalized ?: null,
            'results'  => $results,
        ]);
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

    public function verify($type, $id)
    {
        try {
            $model = $this->resolveModel($type);
            $record = $model->findOrFail($id);

            $name = match (strtolower($type)) {
                'baptism', 'communion', 'confirmation' =>
                    trim(($record->first_name ?? '') . ' ' . ($record->last_name ?? '')) ?: 'N/A',
                'wedding' =>
                    trim(($record->groom_name ?? '') . ' & ' . ($record->bride_name ?? '')) ?: 'N/A',
                'funeral' =>
                    $record->deceased_name ?? 'N/A',
                default => 'N/A',
            };

            $issuedAt = $record->updated_at ?? $record->created_at ?? now();

            return view('records.verify', [
                'type'        => ucfirst(strtolower($type)),
                'name'        => $name,
                'bookNumber'  => $record->book_number  ?? null,
                'pageNumber'  => $record->page_number  ?? null,
                'lineNumber'  => $record->line_number  ?? null,
                'issuedAt'    => $issuedAt,
                'verifiedAt'  => now(),
            ]);

        } catch (\Exception $e) {
            return view('records.verify', [
                'type'        => null,
                'name'        => null,
                'bookNumber'  => null,
                'pageNumber'  => null,
                'lineNumber'  => null,
                'issuedAt'    => null,
                'verifiedAt'  => now(),
                'notFound'    => true,
            ]);
        }
    }

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

    public function store(Request $request)
    {
        $category = strtolower($request->category ?? '');
        
        $rules = [
            'category' => 'required|string',
            'book_number' => 'required|integer',
            'page_number' => 'required|integer',
            'line_number' => 'required|integer',
        ];

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

        $request->validate($rules);

        $model = $this->resolveModel($category);
        $tableName = $model->getTable();

        if ($category === 'baptism') {
            $candidateName = trim((string) $request->input('candidate_name'));
            $parts = preg_split('/\s+/', $candidateName);

            $firstName = $parts[0] ?? null;
            $lastName = count($parts) ? $parts[count($parts) - 1] : null;

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

            $request->merge([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'mother_maiden_name' => $request->input('mother_name'),
                'residence' => $request->input('residence') ?: 'N/A',
            ]);
        }

        if ($category === 'funeral') {
            $firstName  = trim((string) $request->input('first_name'));
            $middleName = trim((string) $request->input('middle_name'));
            $lastName   = trim((string) $request->input('last_name'));

            $deceasedName = trim(preg_replace('/\s+/', ' ', "{$firstName} {$middleName} {$lastName}"));
            if (empty($deceasedName)) {
                $deceasedName = 'N/A';
            }

            $request->merge([
                'deceased_name'  => $deceasedName,
                'age_at_death'   => $request->input('age'),
                'marital_status' => $request->input('civil_status'),
                'cemetery_name'  => $request->input('burial_place'),
                'residence'      => $request->input('residence') ?: 'N/A',
            ]);
        }

        if ($category === 'confirmation' && $request->has('sponsor_name')) {
            $request->merge(['sponsors' => $request->input('sponsor_name')]);
        }

        if ($category === 'wedding' && $request->has('minister_name')) {
            $request->merge(['minister' => $request->input('minister_name')]);
        }

        $dbColumns = Schema::getColumnListing($tableName);
        $saveData = array_intersect_key($request->all(), array_flip($dbColumns));

        $model->fill($saveData)->save();

        return redirect()->route('records.index', ['category' => $category, 'book_number' => $request->book_number])
                         ->with('success', 'Record successfully saved!');
    }

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