<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PayMongoPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentRecordController extends Controller
{
    /**
     * Display a listing of all payments (GCash + Cash).
     */
    public function index(Request $request)
    {
        $method = $request->input('method', 'all');
        $status = $request->input('status', 'all');
        $search = $request->input('search', '');

        // ==================== PAYMONGO PAYMENTS (GCash) ====================
        $paymongoQuery = PayMongoPayment::query();

        if ($method === 'cash') {
            $paymongoQuery->whereRaw('1 = 0');
        }

        // ✅ Status filter logic
        if ($status === 'archived') {
            $paymongoQuery->where('status', 'archived');
        } elseif ($status === 'all') {
            // ✅ Default: huwag isama ang archived
            $paymongoQuery->where('status', '!=', 'archived');
        } else {
            $paymongoQuery->where('status', $status);
        }

        if ($search) {
            $paymongoQuery->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('paymongo_id', 'like', "%{$search}%");
            });
        }
        $paymongoPayments = $paymongoQuery->orderByDesc('created_at')->get();

        // ==================== REGULAR PAYMENTS (Cash) ====================
        $paymentQuery = Payment::query();

        if ($method === 'gcash') {
            $paymentQuery->whereRaw('1 = 0');
        }

        // ✅ Status filter logic
        if ($status === 'archived') {
            $paymentQuery->where('status', 'archived');
        } elseif ($status === 'all') {
            // ✅ Default: huwag isama ang archived
            $paymentQuery->where('status', '!=', 'archived');
        } else {
            $paymentQuery->where('status', $status);
        }

        if ($search) {
            $paymentQuery->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }
        $payments = $paymentQuery->orderByDesc('created_at')->get();

        // ==================== MERGE AND SORT ====================
        $allPayments = $paymongoPayments->map(function ($item) {
            return [
                'id' => $item->id,
                'source' => 'paymongo',
                'reference_number' => $item->reference_number,
                'amount' => $item->amount,
                'currency' => $item->currency ?? 'PHP',
                'payment_method' => 'gcash',
                'status' => $item->status,
                'qr_code_url' => $item->qr_code_url,
                'created_at' => $item->created_at,
                'user_id' => $item->user_id,
            ];
        })->merge($payments->map(function ($item) {
            return [
                'id' => $item->id,
                'source' => 'payments',
                'reference_number' => $item->reference_number ?? 'N/A',
                'amount' => $item->amount,
                'currency' => 'PHP',
                'payment_method' => $item->payment_method,
                'status' => $item->status,
                'qr_code_url' => null,
                'created_at' => $item->created_at,
                'user_id' => $item->user_id,
            ];
        }))->sortByDesc('created_at')->values();

        // ==================== STATS ====================
        $stats = [
            'total' => $allPayments->count(),
            'paid' => $allPayments->where('status', 'paid')->count(),
            'pending' => $allPayments->where('status', 'pending')->count(),
            'failed' => $allPayments->where('status', 'failed')->count(),
            'archived' => $allPayments->where('status', 'archived')->count(),
            'gcash' => $allPayments->where('payment_method', 'gcash')->count(),
            'cash' => $allPayments->where('payment_method', 'cash')->count(),
            'total_amount' => $allPayments->where('status', 'paid')->sum('amount'),
        ];

        return view('payments.index', compact('allPayments', 'stats', 'method', 'status', 'search'));
    }

    /**
     * Display the specified payment.
     */
    public function show($id, $source = 'paymongo')
    {
        if ($source === 'paymongo') {
            $payment = PayMongoPayment::findOrFail($id);
        } else {
            $payment = Payment::findOrFail($id);
        }

        return view('payments.show', compact('payment', 'source'));
    }

    /**
     * Mark a cash payment as paid.
     */
    public function markAsPaid($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => 'paid']);

        return redirect()->route('payments.index')
            ->with('success', 'Payment marked as paid successfully!');
    }

    /**
     * ✅ Archive a payment (soft delete).
     */
    public function archive($id, $source = 'paymongo')
    {
        try {
            if ($source === 'paymongo') {
                $payment = PayMongoPayment::findOrFail($id);
            } else {
                $payment = Payment::findOrFail($id);
            }

            $payment->update(['status' => 'archived']);

            return redirect()->route('payments.index')
                ->with('success', 'Payment archived successfully!');
        } catch (\Exception $e) {
            return redirect()->route('payments.index')
                ->with('error', 'Failed to archive payment: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Restore an archived payment.
     */
    public function restore($id, $source = 'paymongo')
    {
        try {
            if ($source === 'paymongo') {
                $payment = PayMongoPayment::findOrFail($id);
            } else {
                $payment = Payment::findOrFail($id);
            }

            $payment->update(['status' => 'pending']);

            return redirect()->route('payments.index')
                ->with('success', 'Payment restored successfully!');
        } catch (\Exception $e) {
            return redirect()->route('payments.index')
                ->with('error', 'Failed to restore payment: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Delete a payment.
     */
    public function destroy($id, $source = 'paymongo')
    {
        try {
            if ($source === 'paymongo') {
                $payment = PayMongoPayment::findOrFail($id);
            } else {
                $payment = Payment::findOrFail($id);
            }

            $payment->delete();

            return redirect()->route('payments.index')
                ->with('success', 'Payment deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('payments.index')
                ->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }

    /**
     * Export payments to CSV.
     */
    public function export(Request $request)
    {
        $method = $request->input('method', 'all');
        $status = $request->input('status', 'all');

        $filename = 'payments_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($method, $status) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Date',
                'Reference Number',
                'Amount',
                'Currency',
                'Payment Method',
                'Status',
                'User ID',
            ]);

            $paymongoQuery = PayMongoPayment::query();
            if ($method === 'cash') $paymongoQuery->whereRaw('1 = 0');
            if ($status === 'archived') {
                $paymongoQuery->where('status', 'archived');
            } elseif ($status === 'all') {
                $paymongoQuery->where('status', '!=', 'archived');
            } else {
                $paymongoQuery->where('status', $status);
            }
            $paymongoPayments = $paymongoQuery->orderByDesc('created_at')->get();

            $paymentQuery = Payment::query();
            if ($method === 'gcash') $paymentQuery->whereRaw('1 = 0');
            if ($status === 'archived') {
                $paymentQuery->where('status', 'archived');
            } elseif ($status === 'all') {
                $paymentQuery->where('status', '!=', 'archived');
            } else {
                $paymentQuery->where('status', $status);
            }
            $payments = $paymentQuery->orderByDesc('created_at')->get();

            foreach ($paymongoPayments as $payment) {
                fputcsv($file, [
                    $payment->created_at,
                    $payment->reference_number,
                    $payment->amount,
                    $payment->currency ?? 'PHP',
                    'gcash',
                    $payment->status,
                    $payment->user_id,
                ]);
            }

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->created_at,
                    $payment->reference_number ?? 'N/A',
                    $payment->amount,
                    'PHP',
                    $payment->payment_method,
                    $payment->status,
                    $payment->user_id,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}