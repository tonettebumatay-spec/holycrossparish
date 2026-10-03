<?php

namespace App\Http\Controllers;

use App\Models\MarriageBann;
use App\Mail\MarriageBannCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MarriageBannController extends Controller
{
    /**
     * Display a listing of banns (Admin).
     */
    public function index(Request $request)
    {
        // ✅ Auto-update expired status
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

    /**
     * Show the form for creating a new bann.
     */
    public function create()
    {
        return view('banns.create');
    }

    /**
     * Store a newly created bann.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'wedding_date' => 'required|date',
            'banns_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // ✅ Auto-compute expiration (3 weeks after banns_date)
        $expiresAt = \Carbon\Carbon::parse($request->banns_date)->addWeeks(3)->toDateString();

        $bann = MarriageBann::create([
            'groom_name' => $request->groom_name,
            'bride_name' => $request->bride_name,
            'wedding_date' => $request->wedding_date,
            'banns_date' => $request->banns_date,
            'status' => 'active',
            'expires_at' => $expiresAt,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // ✅ Send notification sa admin
        try {
            $adminEmail = config('mail.from.address');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new MarriageBannCreatedMail($bann));
            }
        } catch (\Exception $e) {
            \Log::error('Bann notification failed: ' . $e->getMessage());
        }

        return redirect()->route('banns.index')
            ->with('success', 'Marriage banns added successfully!');
    }

    /**
     * Show the form for editing the specified bann.
     */
    public function edit($id)
    {
        $bann = MarriageBann::findOrFail($id);
        return view('banns.edit', compact('bann'));
    }

    /**
     * Update the specified bann.
     */
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
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $expiresAt = \Carbon\Carbon::parse($request->banns_date)->addWeeks(3)->toDateString();

        $bann->update([
            'groom_name' => $request->groom_name,
            'bride_name' => $request->bride_name,
            'wedding_date' => $request->wedding_date,
            'banns_date' => $request->banns_date,
            'status' => $request->status,
            'expires_at' => $expiresAt,
            'notes' => $request->notes,
        ]);

        return redirect()->route('banns.index')
            ->with('success', 'Marriage banns updated successfully!');
    }

    /**
     * Delete the specified bann.
     */
    public function destroy($id)
    {
        $bann = MarriageBann::findOrFail($id);
        $bann->delete();

        return redirect()->route('banns.index')
            ->with('success', 'Marriage banns deleted successfully!');
    }
}