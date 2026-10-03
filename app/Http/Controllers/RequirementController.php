<?php

namespace App\Http\Controllers;

use App\Models\BookingRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequirementController extends Controller
{
    /**
     * Display a listing of requirements.
     */
    public function index(Request $request)
    {
        $sacramentFilter = $request->input('sacrament');

        $query = BookingRequirement::query();

        if ($sacramentFilter) {
            $query->where('sacrament_type', $sacramentFilter);
        }

        $requirements = $query->ordered()->get()->groupBy('sacrament_type');

        return view('requirements.index', compact('requirements', 'sacramentFilter'));
    }

    /**
     * Show the form for creating a new requirement.
     */
    public function create()
    {
        return view('requirements.create');
    }

    /**
     * Store a newly created requirement.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sacrament_type' => 'required|string|in:baptism,communion,confirmation,wedding,funeral',
            'requirement_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        BookingRequirement::create([
            'sacrament_type' => $request->sacrament_type,
            'requirement_name' => $request->requirement_name,
            'description' => $request->description,
            'is_required' => $request->has('is_required'),
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('requirements.index')
            ->with('success', 'Requirement added successfully!');
    }

    /**
     * Show the form for editing the specified requirement.
     */
    public function edit($id)
    {
        $requirement = BookingRequirement::findOrFail($id);
        return view('requirements.edit', compact('requirement'));
    }

    /**
     * Update the specified requirement.
     */
    public function update(Request $request, $id)
    {
        $requirement = BookingRequirement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'sacrament_type' => 'required|string|in:baptism,communion,confirmation,wedding,funeral',
            'requirement_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $requirement->update([
            'sacrament_type' => $request->sacrament_type,
            'requirement_name' => $request->requirement_name,
            'description' => $request->description,
            'is_required' => $request->has('is_required'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('requirements.index')
            ->with('success', 'Requirement updated successfully!');
    }

    /**
     * Remove the specified requirement.
     */
    public function destroy($id)
    {
        $requirement = BookingRequirement::findOrFail($id);
        $requirement->delete();

        return redirect()->route('requirements.index')
            ->with('success', 'Requirement deleted successfully!');
    }

    /**
     * Toggle active status.
     */
    public function toggle($id)
    {
        $requirement = BookingRequirement::findOrFail($id);
        $requirement->update(['is_active' => !$requirement->is_active]);

        return back()->with('success', 'Requirement status updated!');
    }

    /**
     * API: Get requirements by sacrament.
     */
    public function apiGetRequirements($sacrament)
    {
        try {
            $requirements = BookingRequirement::forSacrament($sacrament)
                ->active()
                ->ordered()
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'requirement_name' => $item->requirement_name,
                        'description' => $item->description,
                        'is_required' => $item->is_required,
                    ];
                });

            return response()->json([
                'success' => true,
                'sacrament' => $sacrament,
                'requirements' => $requirements,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}