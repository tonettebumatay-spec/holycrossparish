<?php

namespace App\Http\Controllers;

use App\Models\PayMongoPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Paymongo\PaymongoClient;

class PayMongoController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new PaymongoClient(
            config('services.paymongo.secret_key')
        );
    }

    /**
     * Create QR Ph payment
     */
    public function createQrPhPayment(Request $request)
    {
        try {
            Log::info('PAYMONGO_QRPH_REQUEST', $request->all());

            $validator = Validator::make($request->all(), [
                'amount'      => 'required|numeric|min:1',
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $amount = $request->input('amount');
            $description = $request->input('description') ?? 'Parish Service Fee';
            $referenceNumber = 'HCP-' . strtoupper(uniqid());

            // 1. Create Payment Intent
            $paymentIntent = $this->client->paymentIntents->create([
                'amount' => intval($amount * 100), // Convert to centavos
                'currency' => 'PHP',
                'payment_method_allowed' => ['qrph'],
                'description' => $description,
                'statement_descriptor' => 'Holy Cross Parish',
            ]);

            // 2. Create Payment Method (QR Ph)
            $paymentMethod = $this->client->paymentMethods->create([
                'type' => 'qrph',
            ]);

            // 3. Attach Payment Method to Payment Intent
            $attachedIntent = $this->client->paymentIntents->attach(
                $paymentIntent->id,
                [
                    'payment_method' => $paymentMethod->id,
                ]
            );

            // 4. Save to database
            $record = PayMongoPayment::create([
                'user_id'            => $user?->id,
                'paymongo_id'        => $paymentIntent->id,
                'reference_number'   => $referenceNumber,
                'amount'             => $amount,
                'currency'           => 'PHP',
                'payment_method'     => 'qrph',
                'status'             => 'pending',
                'qr_code_url'        => $attachedIntent->next_action->code->image_url ?? null,
                'payment_intent_status' => $attachedIntent->status,
                'raw_response'       => json_encode($attachedIntent),
            ]);

            Log::info('PAYMONGO_QRPH_CREATED', [
                'id' => $record->id,
                'reference' => $referenceNumber,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'QR Ph payment created successfully!',
                'payment' => $record,
                'qr_code_url' => $record->qr_code_url,
                'next_action' => $attachedIntent->next_action ?? null,
            ], 201);

        } catch (\Exception $e) {
            Log::error('PAYMONGO_QRPH_ERROR', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook handler
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('PAYMONGO_WEBHOOK_RECEIVED', $request->all());

            $payload = $request->all();
            $eventType = $payload['data']['attributes']['type'] ?? null;
            $paymentData = $payload['data']['attributes']['data'] ?? null;

            if (!$eventType || !$paymentData) {
                return response()->json(['success' => false, 'message' => 'Invalid payload'], 400);
            }

            $paymentIntentId = $paymentData['id'] ?? null;

            if ($eventType === 'payment.paid' || $eventType === 'checkout_session.payment.paid') {
                // Update payment status
                PayMongoPayment::where('paymongo_id', $paymentIntentId)
                    ->update(['status' => 'paid']);
            } elseif ($eventType === 'payment.failed') {
                PayMongoPayment::where('paymongo_id', $paymentIntentId)
                    ->update(['status' => 'failed']);
            } elseif ($eventType === 'qrph.expired') {
                PayMongoPayment::where('paymongo_id', $paymentIntentId)
                    ->update(['status' => 'expired']);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('PAYMONGO_WEBHOOK_ERROR', [
                'message' => $e->getMessage(),
            ]);

            return response()->json(['success' => false], 500);
        }
    }
}