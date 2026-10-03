<?php

namespace App\Http\Controllers;

use App\Models\MarriageBann;

class PublicBannController extends Controller
{
    /**
     * Display public list of marriage banns.
     */
    public function index()
    {
        // ✅ Auto-update expired status
        MarriageBann::updateExpiredStatus();

        $banns = MarriageBann::active()
            ->orderByDesc('banns_date')
            ->get();

        return view('banns.public', compact('banns'));
    }
}