<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BibleVerse;
use Carbon\Carbon;

class BibleVerseApiController extends Controller
{
    public function getTodayVerse()
    {
        $today = Carbon::today()->toDateString();

        $verse = BibleVerse::where('date_to_display', $today)->first();

        // Kung walang naka-set para ngayong araw, kukunin ang pinakabago
        if (!$verse) {
            $verse = BibleVerse::latest('date_to_display')->first();
        }

        return response()->json([
            'success' => true,
            'verse' => $verse ? $verse->verse_text : 'Walang verse para ngayon.',
            'reference' => $verse ? $verse->reference : '',
        ]);
    }
}