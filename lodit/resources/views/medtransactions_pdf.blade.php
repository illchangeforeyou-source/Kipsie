<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medicine Transactions Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #eee;
        }

        ul {
            margin: 0;
            padding-left: 15px;
        }
    </style>
</head>
<body>

<h2>Medicine Transactions</h2>

<p><strong>Total Sales:</strong> ${{ number_format($totalSales, 2) }}</p>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Total ($)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            @php
                $items = json_decode($order->items, true);
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>
                    <ul>
                        @foreach($items as $item)
                            <li>
                                {{ $item['name'] ?? 'Unknown' }}
                                × {{ $item['quantity'] ?? 1 }}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td>${{ number_format($order->total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
