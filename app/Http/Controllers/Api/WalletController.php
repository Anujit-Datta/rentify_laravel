<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletBalance;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    /**
     * Get wallet balance.
     */
    public function balance()
    {
        $user = Auth::user();

        $wallet = WalletBalance::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'monthly_added' => 0,
                'last_reset_date' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => (float) $wallet->balance,
                'monthly_added' => (float) $wallet->monthly_added,
                'last_reset_date' => $wallet->last_reset_date,
            ]
        ]);
    }

    /**
     * Add money to wallet.
     */
    public function addMoney(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100|max:100000',
            'payment_method' => 'required|in:bkash,nagad,rocket,bank_transfer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $wallet = WalletBalance::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'monthly_added' => 0,
                'last_reset_date' => now(),
            ]
        );

        $amount = $request->amount;
        $transactionId = 'TXN' . time() . Str::random(6);

        // Create transaction
        WalletTransaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'add_money',
            'amount' => $amount,
            'payment_method' => $request->payment_method,
            'balance_after' => $wallet->balance + $amount,
            'description' => "Money added via {$request->payment_method}",
            'transaction_id' => $transactionId,
        ]);

        // Update wallet balance
        $wallet->update([
            'balance' => $wallet->balance + $amount,
            'monthly_added' => $wallet->monthly_added + $amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Money added to wallet successfully',
            'data' => [
                'transaction_id' => $transactionId,
                'amount' => (float) $amount,
                'new_balance' => (float) $wallet->fresh()->balance,
            ]
        ]);
    }

    /**
     * Get wallet transactions.
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();

        $query = WalletTransaction::where('user_id', $user->id);

        // Filter by transaction type
        if ($request->has('type')) {
            $query->where('transaction_type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'pagination' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
            ]
        ]);
    }

    /**
     * Get transaction details.
     */
    public function transactionDetails($transactionId)
    {
        $user = Auth::user();

        $transaction = WalletTransaction::where('user_id', $user->id)
            ->where('transaction_id', $transactionId)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }
}
