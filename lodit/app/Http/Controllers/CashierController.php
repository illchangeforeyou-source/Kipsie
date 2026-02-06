<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentConfirmation;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    // Cashier Dashboard
    public function dashboard(Request $request)
    {
        // Check if user is cashier (level 7) or admin (level 3+)
        if (session('level') != 7 && session('level') < 3) {
            abort(403, 'Unauthorized');
        }

        // Get pending payment confirmations
        $pendingPayments = PaymentConfirmation::where('status', 'pending')
            ->with('user', 'order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get recent transactions for today
        $todayTransactions = DB::table('transactions')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user list - using raw query to avoid model issues
        $users = DB::table('login')
            ->where('hidden', '!=', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate totals
        $todayIncome = DB::table('transactions')
            ->whereDate('created_at', today())
            ->where('type', 'income')
            ->sum('amount') ?? 0;

        $totalPendingAmount = PaymentConfirmation::where('status', 'pending')
            ->sum(DB::raw('amount')) ?? 0;

        return view('cashier.dashboard', [
            'pendingPayments' => $pendingPayments,
            'transactions' => $todayTransactions,
            'users' => $users,
            'totals' => [
                'today_income' => $todayIncome,
                'total_pending_amount' => $totalPendingAmount,
            ]
        ]);
    }

    // Confirm payment
    public function confirmPayment(Request $request, $paymentId)
    {
        if (session('level') != 7 && session('level') < 3) {
            abort(403, 'Unauthorized');
        }

        $payment = PaymentConfirmation::find($paymentId);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        try {
            $payment->update([
                'status' => 'confirmed',
                'confirmed_by' => session('id'),
                'confirmed_at' => now(),
            ]);

            // Update order status to paid
            if ($payment->order) {
                $payment->order->update(['status' => 'confirmed']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reject payment
    public function rejectPayment(Request $request, $paymentId)
    {
        if (session('level') != 7 && session('level') < 3) {
            abort(403, 'Unauthorized');
        }

        $payment = PaymentConfirmation::find($paymentId);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $payment->update([
                'status' => 'rejected',
                'rejected_by' => session('id'),
                'rejected_reason' => $validated['reason'],
                'rejected_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
