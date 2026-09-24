<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function apiStore(Request $request)
    {
        try {
            Log::info('API_PAYMENT_REQUEST', $request->all());

            $validator = Validator::make($request->all(), [
                'payment_method'   => 'required|string|in:cash,gcash',
                'amount'           => 'required|numeric|min:0',
                'reference_number' => 'nullable|string|max:100',
                'appointment_id'   => 'nullable|integer',
                'certificate_id'   => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $paymentMethod = $request->input('payment_method');

            $status = $paymentMethod === 'cash' ? 'pending' : 'paid';

            $record = Payment::create([
                'user_id'          => $user?->id,
                'appointment_id'   => $request->input('appointment_id'),
                'certificate_id'   => $request->input('certificate_id'),
                'payment_method'   => $paymentMethod,
                'amount'           => $request->input('amount'),
                'status'           => $status,
                'reference_number' => $request->input('reference_number'),
            ]);

            Log::info('API_PAYMENT_CREATED', [
                'id' => $record->id,
                'method' => $record->payment_method,
            ]);

            return response()->json([
                'success' => true,
                'message' => $paymentMethod === 'cash'
                    ? 'Payment recorded. Please pay at the parish office.'
                    : 'Payment successful!',
                'payment' => $record,
            ], 201);

        } catch (\Exception $e) {
            Log::error('API_PAYMENT_ERROR', [
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