<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .receipt {
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1e3a8a;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        .order-info {
            font-size: 12px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px dashed #999;
        }
        .order-info div {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .label {
            font-weight: bold;
            color: #333;
        }
        .items-section {
            margin-bottom: 20px;
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1px dashed #999;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 8px 0;
            padding: 0;
        }
        .item-name {
            flex: 1;
        }
        .item-qty {
            width: 40px;
            text-align: center;
        }
        .item-price {
            width: 60px;
            text-align: right;
        }
        .total-section {
            border-top: 2px dashed #333;
            padding-top: 15px;
            margin-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0;
        }
        .subtotal-row {
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #999;
            font-size: 11px;
            color: #666;
        }
        .thank-you {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
            font-size: 12px;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            .receipt {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>🏥 LODIT</h1>
            <p>Pharmacy & Medicine Store</p>
            <p>Receipt for Order #{{ $order->id }}</p>
        </div>

        <!-- Order Info -->
        <div class="order-info">
            <div>
                <span class="label">Date:</span>
                <span>{{ $order->created_at->format('M d, Y H:i') }}</span>
            </div>
            <div>
                <span class="label">Customer:</span>
                <span>{{ $order->customer_name ?? 'Guest' }}</span>
            </div>
            <div>
                <span class="label">Order ID:</span>
                <span>#{{ $order->id }}</span>
            </div>
        </div>

        <!-- Items -->
        <div class="items-section">
            <div class="item-header">
                <span class="item-name">Item</span>
                <span class="item-qty">Qty</span>
                <span class="item-price">Price</span>
            </div>

            @foreach($order->items ?? [] as $item)
            <div class="item-row">
                <span class="item-name">{{ $item->medicine->name ?? $item->medicine_name }}</span>
                <span class="item-qty">{{ $item->quantity }}</span>
                <span class="item-price">${{ number_format($item->price * $item->quantity, 2) }}</span>
            </div>
            @endforeach
        </div>

        <!-- Total Section -->
        <div class="total-section">
            <div class="subtotal-row">
                <span>Subtotal:</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
            <div class="subtotal-row">
                <span>Tax (0%):</span>
                <span>$0.00</span>
            </div>
            <div class="total-row">
                <span>TOTAL:</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="thank-you">
            Thank you for your purchase! ❤️
        </div>
        <div class="footer">
            <p>LODIT Medicine Store</p>
            <p style="margin: 5px 0;">For any queries, contact us</p>
            <p style="margin: 5px 0;">This is a system-generated receipt</p>
        </div>
    </div>
</body>
</html>
