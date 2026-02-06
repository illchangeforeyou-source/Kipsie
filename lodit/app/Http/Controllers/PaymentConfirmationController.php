<?php

namespace App\Http\Controllers;

use App\Models\PaymentConfirmation;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentConfirmationController extends Controller
{
    // Cashier view pending payments
    public function pending(Request $request)
    {
        // Check if user is cashier (level 5) or admin+
        if (session('level') < 3) {
            abort(403, 'Unauthorized');
        }

        $query = PaymentConfirmation::where('status', 'pending')
            ->with('user', 'order', 'cashier')
            ->orderBy('created_at', 'asc');

        // Filter by customer
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%$search%");
            });
        }

        // Filter by date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $confirmations = $query->paginate(15);

        return view('admin.payment-confirmations', [
            'confirmations' => $confirmations,
            'searchTerm' => $request->search ?? '',
            'dateFrom' => $request->date_from ?? '',
            'dateTo' => $request->date_to ?? '',
        ]);
    }

    // Create payment confirmation request (from order)
    public function createFromOrder($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        // Check if already has pending confirmation
        $existing = PaymentConfirmation::where('order_id', $orderId)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Payment already pending confirmation'], 400);
        }

        $confirmation = PaymentConfirmation::create([
            'order_id' => $orderId,
            'user_id' => session('id'),
            'amount' => $order->total,
            'payment_method' => 'cash',
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmation request created',
            'confirmation' => $confirmation
        ]);
    }

    // Confirm payment (admin/cashier action)
    public function confirm(Request $request, $id)
    {
        if (session('level') < 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $confirmation = PaymentConfirmation::find($id);
        if (!$confirmation) {
            return response()->json(['success' => false, 'message' => 'Confirmation not found'], 404);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $confirmation->update([
            'status' => 'confirmed',
            'cashier_id' => session('id'),
            'confirmed_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update order status
        $order = $confirmation->order;
        if ($order) {
            $order->update(['status' => 'paid']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed successfully',
        ]);
    }

    // Reject payment
    public function reject(Request $request, $id)
    {
        if (session('level') < 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $confirmation = PaymentConfirmation::find($id);
        if (!$confirmation) {
            return response()->json(['success' => false, 'message' => 'Confirmation not found'], 404);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $confirmation->update([
            'status' => 'rejected',
            'cashier_id' => session('id'),
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment rejected',
        ]);
    }

    // View user's payment confirmations
    public function userConfirmations()
    {
        if (!session('id')) {
            return redirect('/login');
        }

        $confirmations = PaymentConfirmation::where('user_id', session('id'))
            ->with('order', 'cashier')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payment.my-confirmations', [
            'confirmations' => $confirmations,
        ]);
    }
}
