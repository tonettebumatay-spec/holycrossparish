<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::query()
            ->orderByDesc('request_date')
            ->orderByDesc('id')
            ->get();

        return view('certificates.index', compact('certificates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'certificate_type' => 'required|in:Baptismal,Communion,Confirmation,Wedding',
            'request_date' => 'required|date',
        ]);

        $validated['status'] = 'pending';

        Certificate::create($validated);

        return redirect()->route('certificates.index')->with('success', 'Certificate request submitted successfully!');
    }

    public function complete($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->update(['status' => 'completed']);

        return redirect()->route('certificates.index')->with('success', 'Certificate marked as completed.');
    }

    public function cancel($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->update(['status' => 'cancelled']);

        return redirect()->route('certificates.index')->with('success', 'Certificate request cancelled.');
    }
}

