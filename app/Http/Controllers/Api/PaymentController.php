<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\RentPayment;
use App\Models\RentReceipt;
use App\Models\Contract;
use App\Models\WalletBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isTenant()) {
            $query = RentPayment::where('tenant_id', $user->id)
                ->with(['property', 'landlord', 'receipt']);
        } elseif ($user->isLandlord()) {
            $query = RentPayment::where('landlord_id', $user->id)
                ->with(['property', 'tenant', 'receipt']);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => PaymentResource::collection($payments),
            'pagination' => [
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isTenant()) {
            return response()->json([
                'success' => false,
                'message' => 'Only tenants can create payments'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'amount' => 'required|numeric|min:0',
            'payment_month' => 'required|date',
            'payment_method' => 'required|in:bank_transfer,hand_cash,mobile_banking,check,wallet',
            'transaction_id' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get contract for this property
        $contract = Contract::where('tenant_id', $user->id)
            ->where('property_id', $request->property_id)
            ->where('status', 'fully_signed')
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'No active contract found for this property'
            ], 404);
        }

        // Check if payment already exists for this month
        $existingPayment = RentPayment::where('tenant_id', $user->id)
            ->where('property_id', $request->property_id)
            ->where('payment_month', $request->payment_month)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already exists for this month'
            ], 400);
        }

        // Process wallet payment if selected
        if ($request->payment_method === 'wallet') {
            $wallet = WalletBalance::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->balance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient wallet balance'
                ], 400);
            }

            $wallet->update([
                'balance' => $wallet->balance - $request->amount
            ]);
        }

        $payment = RentPayment::create([
            'rental_id' => $contract->id,
            'tenant_id' => $user->id,
            'landlord_id' => $contract->landlord_id,
            'property_id' => $request->property_id,
            'amount' => $request->amount,
            'payment_month' => $request->payment_month,
            'payment_date' => now(),
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'note' => $request->note,
            'transaction_id' => $request->transaction_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment submitted successfully',
            'data' => new PaymentResource($payment->load(['property', 'tenant', 'landlord']))
        ], 201);
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        $user = Auth::user();
        $payment = RentPayment::with(['property', 'tenant', 'landlord', 'receipt'])->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        // Check authorization
        if ($user->isTenant() && $payment->tenant_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($user->isLandlord() && $payment->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new PaymentResource($payment)
        ]);
    }

    /**
     * Confirm payment (landlord only).
     */
    public function confirm(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isLandlord()) {
            return response()->json([
                'success' => false,
                'message' => 'Only landlords can confirm payments'
            ], 403);
        }

        $payment = RentPayment::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        if ($payment->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Payment can only be confirmed if pending'
            ], 400);
        }

        $payment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Generate receipt
        $receipt = RentReceipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => 'REC-' . strtoupper(uniqid()),
            'tenant_id' => $payment->tenant_id,
            'landlord_id' => $payment->landlord_id,
            'rent_amount' => $payment->amount,
            'late_fee' => 0,
            'total_amount' => $payment->amount,
            'payment_month' => $payment->payment_month,
            'generated_at' => now(),
            'email_sent' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed successfully',
            'data' => new PaymentResource($payment->load(['property', 'tenant', 'landlord', 'receipt']))
        ]);
    }

    /**
     * Reject payment (landlord only).
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->isLandlord()) {
            return response()->json([
                'success' => false,
                'message' => 'Only landlords can reject payments'
            ], 403);
        }

        $payment = RentPayment::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        if ($payment->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Can only reject pending payments'
            ], 400);
        }

        // Refund wallet payment if applicable
        if ($payment->payment_method === 'wallet') {
            $wallet = WalletBalance::where('user_id', $payment->tenant_id)->first();
            if ($wallet) {
                $wallet->update([
                    'balance' => $wallet->balance + $payment->amount
                ]);
            }
        }

        $payment->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Payment rejected successfully',
            'data' => new PaymentResource($payment->load(['property', 'tenant', 'landlord']))
        ]);
    }

    /**
     * Download receipt.
     */
    public function downloadReceipt($id)
    {
        $user = Auth::user();
        $payment = RentPayment::with('receipt')->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        // Check authorization
        if ($user->isTenant() && $payment->tenant_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($user->isLandlord() && $payment->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if (!$payment->receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payment->receipt
        ]);
    }
}
