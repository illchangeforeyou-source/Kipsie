<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Notification;
use App\Models\User;
use App\Mail\DeliveryStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;

class DeliveryController extends Controller
{
    // Customer view their delivery status
    public function trackOrder($orderId)
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        // Check authorization - only order owner can view
        if (session('id') != $order->user_id && session('level') < 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'total' => $order->total,
                'status' => $order->status,
                'delivery_status' => $order->delivery_status,
                'created_at' => $order->created_at->format('d M Y H:i'),
                'shipped_at' => $order->shipped_at ? $order->shipped_at->format('d M Y H:i') : null,
                'delivered_at' => $order->delivered_at ? $order->delivered_at->format('d M Y H:i') : null,
                'delivery_notes' => $order->delivery_notes,
            ]
        ]);
    }

    // Admin view all deliveries
    public function allDeliveries(Request $request)
    {
        if (session('level') < 3) {
            abort(403, 'Unauthorized');
        }

        $query = Order::query();

        if ($request->has('filter_status') && $request->filter_status) {
            $query->where('delivery_status', $request->filter_status);
        }

        if ($request->has('search') && $request->search) {
            $query->where('customer_name', 'like', "%{$request->search}%");
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.delivery-tracking', [
            'orders' => $orders,
            'filterStatus' => $request->filter_status ?? '',
            'searchTerm' => $request->search ?? '',
        ]);
    }

    // Update delivery status (admin)
    public function updateDeliveryStatus(Request $request, $orderId)
    {
        if (session('level') < 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $validated = $request->validate([
            'delivery_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'delivery_notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->delivery_status;

        $order->delivery_status = $validated['delivery_status'];
        $order->delivery_notes = $validated['delivery_notes'] ?? $order->delivery_notes;

        if ($validated['delivery_status'] == 'shipped') {
            $order->shipped_at = now();
        } elseif ($validated['delivery_status'] == 'delivered') {
            $order->delivered_at = now();
        }

        $order->save();

        // Send in-app notification to customer when delivery is delivered or cancelled
        if ($oldStatus !== $order->delivery_status && in_array($order->delivery_status, ['delivered', 'cancelled'])) {
            $title = $order->delivery_status === 'delivered' ? 'Order Delivered' : 'Order Cancelled';
            $message = $order->delivery_status === 'delivered'
                ? "Your order #{$order->id} has been delivered. Thank you for shopping with us!"
                : "Your order #{$order->id} has been cancelled. {$order->delivery_notes}";

            Notification::create([
                'user_id' => $order->user_id,
                'title' => $title,
                'message' => $message,
                'type' => 'delivery',
                'order_id' => $order->id,
                'read' => false,
                'read_at' => null,
            ]);

            // Send email
            $user = User::find($order->user_id);
            if ($user && !empty($user->email)) {
                try {
                    Mail::to($user->email)->send(new DeliveryStatusChanged($order, $order->delivery_status, $order->delivery_notes));
                } catch (\Exception $e) {
                    // Log but don't break the response
                    // logging not included to keep changes minimal
                }
            }

            // Send WhatsApp via Twilio if configured and user has phone
            if ($user && !empty($user->phone) && env('TWILIO_ACCOUNT_SID') && env('TWILIO_AUTH_TOKEN') && env('TWILIO_WHATSAPP_FROM')) {
                try {
                    $client = new Client();
                    $sid = env('TWILIO_ACCOUNT_SID');
                    $token = env('TWILIO_AUTH_TOKEN');
                    $from = env('TWILIO_WHATSAPP_FROM');
                    $to = 'whatsapp:' . preg_replace('/[^0-9+]/', '', $user->phone);
                    $body = $message;

                    $client->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                        'auth' => [$sid, $token],
                        'form_params' => [
                            'To' => $to,
                            'From' => $from,
                            'Body' => $body,
                        ],
                    ]);
                } catch (\Exception $e) {
                    // ignore failures to avoid blocking admin
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Delivery status updated successfully!',
        ]);
    }

    // Customer view their orders with delivery status
    public function myDeliveries(Request $request)
    {
        if (!session('id')) {
            return redirect('/login');
        }

        $orders = Order::where('user_id', session('id'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('delivery.my-orders', [
            'orders' => $orders,
        ]);
    }
}
