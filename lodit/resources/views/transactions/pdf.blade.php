<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #2c2c2c; color: white; }
        h2 { text-align: center; color: #333; }
    </style>
</head>
<body>
    <h2>Financial Report</h2>

    <p><strong>Total Income:</strong> ${{ number_format($totalIncome, 2) }}</p>
    <p><strong>Total Expense:</strong> ${{ number_format($totalExpense, 2) }}</p>
    <p><strong>Balance:</strong> ${{ number_format($balance, 2) }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Amount ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $t)
                <tr>
                    <td>{{ $t->date }}</td>
                    <td>{{ ucfirst($t->type) }}</td>
                    <td>{{ $t->category }}</td>
                    <td>{{ number_format($t->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
