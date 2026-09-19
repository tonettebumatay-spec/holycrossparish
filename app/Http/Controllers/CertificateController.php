<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            'full_name'        => 'required|string|max:255',
            'certificate_type' => 'required|in:Baptismal,Communion,Confirmation,Wedding',
            'request_date'     => 'required|date',
        ]);

        $validated['status'] = 'pending';

        Certificate::create($validated);

        return redirect()->route('certificates.index')->with('success', 'Certificate request submitted successfully!');
    }

    /**
     * API: Store certificate request from Android app.
     */
    public function apiStore(Request $request)
    {
        try {
            Log::info('API_CERTIFICATE_REQUEST', $request->all());

            $validator = Validator::make($request->all(), [
                'user_name'        => 'required|string|max:255',
                'service_type'     => 'required|string', // baptism, communion, etc.
                'contact_number'   => 'required|string|max:20',
                'details'          => 'nullable|string',
                'appointment_date' => 'nullable|date',
                'time'             => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $userName = $request->input('user_name');
            $contactNumber = $request->input('contact_number');
            $details = $request->input('details') ?: '';
            $serviceType = $request->input('service_type');
            $appointmentDate = $request->input('appointment_date');
            $appointmentTime = $request->input('time');

            // Normalize certificate_type para sa existing schema
            // (Baptismal, Communion, Confirmation, Wedding)
            $certificateType = match (strtolower($serviceType)) {
                'baptism'      => 'Baptismal',
                'communion'    => 'Communion',
                'confirmation' => 'Confirmation',
                'wedding'      => 'Wedding',
                default        => ucfirst($serviceType),
            };

            $record = Certificate::create([
                'full_name'        => $userName,
                'certificate_type' => $certificateType,
                'contact_number'   => $contactNumber,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'details'          => $details,
                'request_date'     => now()->toDateString(),
                'status'           => 'pending',
                'user_id'          => $user?->id,
                'email'            => $user?->email,
            ]);

            Log::info('API_CERTIFICATE_CREATED', [
                'certificate_id' => $record->id,
                'type'           => $certificateType,
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($serviceType) . ' certificate request submitted successfully! The admin will process your request.',
                'certificate' => $record,
            ], 201);

        } catch (\Exception $e) {
            Log::error('API_CERTIFICATE_ERROR', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
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