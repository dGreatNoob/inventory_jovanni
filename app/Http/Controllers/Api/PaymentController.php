<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Finance;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments with filters
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with('finance');

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('payment_date', [$request->date_from, $request->date_to]);
        }

        // Filter by reference
        if ($request->has('reference')) {
            $query->where('payment_ref', 'like', "%{$request->reference}%");
        }

        // Filter by finance_id (linked payable/receivable)
        if ($request->has('finance_id')) {
            $query->where('finance_id', $request->finance_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_ref', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $payments = $query->orderByDesc('payment_date')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    /**
     * Store a newly created payment
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'finance_id' => 'required|exists:finances,id',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate payment amount against balance
        $finance = Finance::findOrFail($request->finance_id);
        
        if ($request->amount > $finance->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot exceed the balance due of ' . number_format($finance->balance, 2),
            ], 422);
        }

        // Generate payment reference
        $payment_ref = $this->generatePaymentRef();

        // Calculate status
        $status = $this->calculatePaymentStatus($finance, $request->amount);

        // Create payment
        $payment = Payment::create([
            'payment_ref' => $payment_ref,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'finance_id' => $request->finance_id,
            'status' => $status,
            'remarks' => $request->remarks,
        ]);

        // Update finance balance
        $finance->balance -= $request->amount;
        $finance->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'data' => $payment->load('finance'),
        ], 201);
    }

    /**
     * Display the specified payment
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $payment = Payment::with('finance')->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payment,
        ]);
    }

    /**
     * Update the specified payment
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'finance_id' => 'required|exists:finances,id',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $finance = Finance::findOrFail($request->finance_id);
        
        // Restore original balance
        $finance->balance += $payment->amount;
        
        // Validate new amount
        if ($request->amount > $finance->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot exceed the balance due of ' . number_format($finance->balance, 2),
            ], 422);
        }

        // Calculate new status
        $status = $this->calculatePaymentStatus($finance, $request->amount);

        // Update payment
        $payment->update([
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'finance_id' => $request->finance_id,
            'status' => $status,
            'remarks' => $request->remarks,
        ]);

        // Update finance balance
        $finance->balance -= $request->amount;
        $finance->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'data' => $payment->load('finance'),
        ]);
    }

    /**
     * Remove the specified payment
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        // Restore balance to finance record
        $finance = Finance::find($payment->finance_id);
        if ($finance) {
            $finance->balance += $payment->amount;
            $finance->save();
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully',
        ]);
    }

    /**
     * Get payment statistics
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_payments' => Payment::count(),
            'total_amount' => Payment::sum('amount'),
            'fully_paid_count' => Payment::where('status', PaymentStatus::FULLY_PAID->value)->count(),
            'partially_paid_count' => Payment::where('status', PaymentStatus::PARTIALLY_PAID->value)->count(),
            'overdue_count' => Payment::where('status', PaymentStatus::OVERDUE->value)->count(),
            'not_paid_count' => Payment::where('status', PaymentStatus::NOT_PAID->value)->count(),
            'by_method' => Payment::selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_method')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Generate payment reference
     *
     * @return string
     */
    private function generatePaymentRef(): string
    {
        $date = now()->format('ymd');
        $prefix = 'PAY' . $date;

        $latest = Payment::where('payment_ref', 'like', $prefix . '%')
                        ->orderByDesc('payment_ref')
                        ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->payment_ref, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return $prefix . $nextNumber;
    }

    /**
     * Calculate payment status
     *
     * @param Finance $finance
     * @param float $paymentAmount
     * @return string
     */
    private function calculatePaymentStatus(Finance $finance, float $paymentAmount): string
    {
        $remainingBalance = $finance->balance - $paymentAmount;
        
        if ($remainingBalance == 0) {
            return PaymentStatus::FULLY_PAID->value;
        } elseif ($remainingBalance > 0 && $remainingBalance < $finance->amount) {
            return PaymentStatus::PARTIALLY_PAID->value;
        } elseif ($finance->due_date && now()->isAfter($finance->due_date)) {
            return PaymentStatus::OVERDUE->value;
        } else {
            return PaymentStatus::NOT_PAID->value;
        }
    }
}
