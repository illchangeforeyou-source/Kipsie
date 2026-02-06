<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e3a8a; color: white; padding: 20px; border-radius: 5px 5px 0 0; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 5px 5px; }
        .order-details { background: white; padding: 15px; border-left: 4px solid #1e3a8a; margin-bottom: 20px; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th, .items-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .items-table th { background: #1e3a8a; color: white; font-weight: bold; }
        .total { font-size: 18px; font-weight: bold; color: #1e3a8a; text-align: right; margin-top: 20px; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
        .button { display: inline-block; padding: 10px 20px; background: #1e3a8a; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 LODIT Pharmacy</h1>
            <p>Your Receipt</p>
        </div>

        <div class="content">
            <div class="order-details">
                <h3>Order #{{ $order->id }}</h3>
                <p><strong>Date:</strong> {{ $order->created_at->format('F d, Y H:i A') }}</p>
                <p><strong>Customer:</strong> {{ $order->customer_name ?? 'Valued Customer' }}</p>
                <p><strong>Status:</strong> <span style="color: #28a745;">Confirmed</span></p>
            </div>

            <h3>Order Items</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items ?? [] as $item)
                    <tr>
                        <td>{{ $item->medicine->name ?? $item->medicine_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                Total Amount: ${{ number_format($order->total, 2) }}
            </div>

            <p style="margin-top: 30px; text-align: center;">
                <a href="{{ url('/orders/' . $order->id) }}" class="button">View Full Receipt</a>
            </p>

            <div class="footer">
                <p>Thank you for shopping at LODIT Pharmacy!</p>
                <p>For any questions, please contact our support team.</p>
                <p>&copy; {{ date('Y') }} LODIT Medicine Store. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
