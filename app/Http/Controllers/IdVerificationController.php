<?php

namespace App\Http\Controllers;

use App\Models\IdVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IdVerificationController extends Controller
{
    public function apiStore(Request $request)
    {
        try {
            Log::info('API_ID_VERIFICATION_REQUEST', $request->all());

            $validator = Validator::make($request->all(), [
                'id_type'        => 'required|string|in:driver_license,national_id,tin_id,voters_id,philhealth_id',
                'id_number'      => 'nullable|string|max:50',
                'full_name'      => 'nullable|string|max:255',
                'birthdate'      => 'nullable|date',
                'address'        => 'nullable|string|max:500',
                'id_picture'     => 'nullable|image|max:5120',
                'appointment_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $picturePath = null;

            if ($request->hasFile('id_picture')) {
                $picturePath = $request->file('id_picture')
                    ->store('id_verifications', 'public');
            }

            $record = IdVerification::create([
                'user_id'          => $user?->id,
                'appointment_id'   => $request->input('appointment_id'),
                'id_type'          => $request->input('id_type'),
                'id_number'        => $request->input('id_number'),
                'full_name'        => $request->input('full_name'),
                'birthdate'        => $request->input('birthdate'),
                'address'          => $request->input('address'),
                'id_picture_path'  => $picturePath,
                'status'           => 'verified',
            ]);

            Log::info('API_ID_VERIFICATION_CREATED', [
                'id' => $record->id,
                'type' => $record->id_type,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ID verified successfully!',
                'id_verification' => $record,
            ], 201);

        } catch (\Exception $e) {
            Log::error('API_ID_VERIFICATION_ERROR', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}