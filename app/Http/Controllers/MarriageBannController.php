<?php

namespace App\Http\Controllers;

use App\Models\MarriageBann;
use App\Mail\MarriageBannCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MarriageBannController extends Controller
{
    /**
     * Display a listing of banns (Admin).
     */
    public function index(Request $request)
    {
        MarriageBann::updateExpiredStatus();

        $statusFilter = $request->input('status');
        $query = MarriageBann::query();

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $banns = $query->orderByDesc('banns_date')->get();

        $stats = [
            'total' => MarriageBann::count(),
            'active' => MarriageBann::where('status', 'active')->count(),
            'expired' => MarriageBann::where('status', 'expired')->count(),
            'completed' => MarriageBann::where('status', 'completed')->count(),
        ];

        return view('banns.index', compact('banns', 'stats', 'statusFilter'));
    }

    public function create()
    {
        return view('banns.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'wedding_date' => 'required|date',
            'banns_date' => 'required|date',
            'notes' => 'nullable|string',
            // Validation para sa pictures
            'groom_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bride_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Validation para sa dates
            'publication_start' => 'nullable|date',
            'publication_end' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $expiresAt = \Carbon\Carbon::parse($request->banns_date)->addWeeks(3)->toDateString();

        // Kunin lahat ng fields maliban sa pictures
        $data = $request->only([
            'groom_name', 'bride_name', 'wedding_date', 'banns_date', 'notes',
            'groom_dob', 'groom_pob', 'groom_father', 'groom_mother', 'groom_address',
            'bride_dob', 'bride_pob', 'bride_father', 'bride_mother', 'bride_address',
            'publication_start', 'publication_end'
        ]);

        $data['status'] = 'active';
        $data['expires_at'] = $expiresAt;
        $data['created_by'] = Auth::id();

        // ✅ Upload Pictures
        if ($request->hasFile('groom_picture')) {
            $data['groom_picture'] = $request->file('groom_picture')->store('banns', 'public');
        }
        if ($request->hasFile('bride_picture')) {
            $data['bride_picture'] = $request->file('bride_picture')->store('banns', 'public');
        }

        $bann = MarriageBann::create($data);

        try {
            $adminEmail = config('mail.from.address');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new MarriageBannCreatedMail($bann));
            }
        } catch (\Exception $e) {
            Log::error('Bann notification failed: ' . $e->getMessage());
        }

        return redirect()->route('banns.index')
            ->with('success', 'Marriage banns added successfully!');
    }

    public function edit($id)
    {
        $bann = MarriageBann::findOrFail($id);
        return view('banns.edit', compact('bann'));
    }

    public function update(Request $request, $id)
    {
        $bann = MarriageBann::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'wedding_date' => 'required|date',
            'banns_date' => 'required|date',
            'status' => 'required|in:active,expired,completed',
            'notes' => 'nullable|string',
            'groom_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bride_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $expiresAt = \Carbon\Carbon::parse($request->banns_date)->addWeeks(3)->toDateString();

        $data = $request->only([
            'groom_name', 'bride_name', 'wedding_date', 'banns_date', 'status', 'notes',
            'groom_dob', 'groom_pob', 'groom_father', 'groom_mother', 'groom_address',
            'bride_dob', 'bride_pob', 'bride_father', 'bride_mother', 'bride_address',
            'publication_start', 'publication_end'
        ]);

        $data['expires_at'] = $expiresAt;

        // ✅ Upload Pictures (Kung may bago)
        if ($request->hasFile('groom_picture')) {
            if ($bann->groom_picture) Storage::disk('public')->delete($bann->groom_picture);
            $data['groom_picture'] = $request->file('groom_picture')->store('banns', 'public');
        }
        if ($request->hasFile('bride_picture')) {
            if ($bann->bride_picture) Storage::disk('public')->delete($bann->bride_picture);
            $data['bride_picture'] = $request->file('bride_picture')->store('banns', 'public');
        }

        $bann->update($data);

        return redirect()->route('banns.index')
            ->with('success', 'Marriage banns updated successfully!');
    }

    public function destroy($id)
    {
        $bann = MarriageBann::findOrFail($id);
        
        // ✅ I-delete din ang mga pictures sa storage
        if ($bann->groom_picture) Storage::disk('public')->delete($bann->groom_picture);
        if ($bann->bride_picture) Storage::disk('public')->delete($bann->bride_picture);
        
        $bann->delete();

        return redirect()->route('banns.index')
            ->with('success', 'Marriage banns deleted successfully!');
    }

    public function apiIndex()
    {
        try {
            MarriageBann::updateExpiredStatus();

            $banns = MarriageBann::active()
                ->orderByDesc('banns_date')
                ->get()
                ->map(function ($bann) {
                    return [
                        'id'            => $bann->id,
                        'groom_name'    => $bann->groom_name,
                        'bride_name'    => $bann->bride_name,
                        'wedding_date'  => $bann->wedding_date?->format('Y-m-d'),
                        'banns_date'    => $bann->banns_date?->format('Y-m-d'),
                        'expires_at'    => $bann->expires_at?->format('Y-m-d'),
                        'notes'         => $bann->notes,
                        'status'        => $bann->status,
                        // ✅ Idinagdag para sa API at Android
                        'groom_picture' => $bann->groom_picture ? asset('storage/' . $bann->groom_picture) : null,
                        'bride_picture' => $bann->bride_picture ? asset('storage/' . $bann->bride_picture) : null,
                        'publication_start' => $bann->publication_start?->format('Y-m-d'),
                        'publication_end'   => $bann->publication_end?->format('Y-m-d'),
                    ];
                });

            return response()->json([
                'success' => true,
                'count'   => $banns->count(),
                'banns'   => $banns,
            ]);

        } catch (\Exception $e) {
            Log::error('API_BANNS_ERROR', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}