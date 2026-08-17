<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    // ============================================================
    // API METHODS – Called by Android App
    // ============================================================

    public function storeBaptism(Request $request)
    {
        return $this->handleBooking($request, 'Baptism');
    }

    public function storeCommunion(Request $request)
    {
        return $this->handleBooking($request, 'Communion');
    }

    public function storeConfirmation(Request $request)
    {
        return $this->handleBooking($request, 'Confirmation');
    }

    public function storeWedding(Request $request)
    {
        return $this->handleBooking($request, 'Wedding');
    }

    public function storeFuneral(Request $request)
    {
        return $this->handleBooking($request, 'Funeral');
    }

    // ============================================================
    // COMMON BOOKING HANDLER
    // ============================================================

    private function handleBooking(Request $request, string $serviceType)
    {
        try {

            Log::info('BOOKING REQUEST RECEIVED', [
                'service_type' => $serviceType,
                'data' => $request->all(),
            ]);

            // ----------------------------------------------------
            // VALIDATION
            // ----------------------------------------------------

            $validator = Validator::make($request->all(), [
                'user_name' => 'required|string|max:255',
                'contact_number' => 'required|string|max:255',
                'details' => 'nullable|string',
            ]);

            if ($validator->fails()) {

                Log::warning('BOOKING VALIDATION FAILED', [
                    'service_type' => $serviceType,
                    'errors' => $validator->errors()->toArray(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // ----------------------------------------------------
            // AUTHENTICATED USER
            // ----------------------------------------------------

            $user = $request->user();

            // ----------------------------------------------------
            // GET DATA FROM ANDROID
            // ----------------------------------------------------

            $userName = trim($request->input('user_name'));
            $contactNumber = trim($request->input('contact_number'));
            $details = $request->input('details');

            // ----------------------------------------------------
            // INSERT INTO appointments TABLE
            // ----------------------------------------------------

            $appointmentData = [
                'user_name' => $userName,
                'service_type' => $serviceType,
                'appointment_date' => null,
                'contact_number' => $contactNumber,
                'details' => $details,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Save user_id if column exists
            if (
                $user &&
                \Illuminate\Support\Facades\Schema::hasColumn(
                    'appointments',
                    'user_id'
                )
            ) {
                $appointmentData['user_id'] = $user->id;
            }

            // Save email if column exists
            if (
                $user &&
                \Illuminate\Support\Facades\Schema::hasColumn(
                    'appointments',
                    'email'
                )
            ) {
                $appointmentData['email'] = $user->email;
            }

            Log::info('INSERTING APPOINTMENT', [
                'data' => $appointmentData,
            ]);

            // ----------------------------------------------------
            // INSERT
            // ----------------------------------------------------

            $appointmentId = DB::table('appointments')->insertGetId(
                $appointmentData
            );

            // ----------------------------------------------------
            // GET CREATED APPOINTMENT
            // ----------------------------------------------------

            $appointment = DB::table('appointments')
                ->where('id', $appointmentId)
                ->first();

            Log::info('APPOINTMENT CREATED SUCCESSFULLY', [
                'id' => $appointmentId,
                'service_type' => $serviceType,
            ]);

            // ----------------------------------------------------
            // RESPONSE
            // ----------------------------------------------------

            return response()->json([
                'success' => true,
                'message' => $serviceType .
                    ' request submitted successfully! ' .
                    'The admin will schedule your appointment.',
                'booking' => $appointment,
            ], 201);

        } catch (\Exception $e) {

            Log::error('BOOKING ERROR', [
                'service_type' => $serviceType,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}