<?php

namespace App\Http\Controllers;

use App\Models\FaceRecognition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FaceRecognitionController extends Controller
{
    public function apiStore(Request $request)
    {
        try {
            Log::info('API_FACE_RECOGNITION_REQUEST', $request->all());

            $validator = Validator::make($request->all(), [
                'face_picture' => 'nullable|image|max:5120',
                'appointment_id' => 'nullable|integer',
                'certificate_id' => 'nullable|integer',
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

            if ($request->hasFile('face_picture')) {
                $picturePath = $request->file('face_picture')
                    ->store('face_recognitions', 'public');
            }

            $record = FaceRecognition::create([
                'user_id'           => $user?->id,
                'appointment_id'    => $request->input('appointment_id'),
                'certificate_id'    => $request->input('certificate_id'),
                'face_picture_path' => $picturePath,
                'status'            => 'verified',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Face verified successfully!',
                'face_recognition' => $record,
            ], 201);

        } catch (\Exception $e) {
            Log::error('API_FACE_RECOGNITION_ERROR', [
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