@php
// This view will be wrapped with header, menu, and footer
@endphp

<style>
    .report-container {
        background-color: #2b2b2b;
        border: 1px solid #444;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        padding: 30px;
        margin-top: 20px;
        margin-bottom: 30px;
    }

    .report-container h1 {
        border-bottom: 2px solid #555;
        padding-bottom: 10px;
        margin-bottom: 30px;
        color: #f0f0f0;
    }

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background-color: #2a2a2a;
        border-left: 4px solid #444;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .summary-card h3 {
        color: #999;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    .summary-card .amount {
        font-size: 28px;
        font-weight: bold;
        color: #f0f0f0;
    }

    .card-income {
        border-left-color: #5cb85c;
    }

    .card-income .amount {
        color: #90ee90;
    }

    .card-expense {
        border-left-color: #d9534f;
    }

    .card-expense .amount {
        color: #ff6b6b;
    }

    .card-balance {
        border-left-color: #5bc0de;
    }

    .card-balance .amount {
        color: #87ceeb;
    }

    .transactions-section {
        margin-top: 40px;
    }

    .transactions-section h2 {
        color: #f0f0f0;
        border-bottom: 1px solid #555;
        padding-bottom: 15px;
        margin-bottom: 20px;
        font-size: 18px;
    }

    .transaction-filters {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .transaction-filters select {
        background-color: #1e1e1e;
        border: 1px solid #444;
        border-radius: 6px;
        padding: 10px 12px;
        color: #fff;
        font-size: 13px;
    }

    .transaction-filters select:focus {
        border-color: #666;
        outline: none;
    }

    .transactions-table {
        width: 100%;
        border-collapse: collapse;
        overflow-x: auto;
    }

    .transactions-table thead {
        background-color: #1e1e1e;
    }

    .transactions-table th {
        padding: 15px;
        text-align: left;
        color: #ccc;
        font-weight: 600;
        border-bottom: 2px solid #444;
        font-size: 12px;
        text-transform: uppercase;
    }

    .transactions-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #3a3a3a;
        color: #ddd;
        font-size: 13px;
    }

    .transactions-table tbody tr:hover {
        background-color: #3a3a3a;
    }

    .transaction-type-sale {
        color: #90ee90;
        font-weight: 600;
    }

    .transaction-type-stock {
        color: #ffb347;
        font-weight: 600;
    }

    .amount-positive {
        color: #90ee90;
        font-weight: 600;
    }

    .amount-negative {
        color: #ff6b6b;
        font-weight: 600;
    }

    .no-transactions {
        text-align: center;
        padding: 40px;
        color: #888;
    }

    .receipt-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
    }

    .receipt-content {
        background-color: #2b2b2b;
        margin: 5% auto;
        padding: 30px;
        border: 1px solid #444;
        border-radius: 15px;
        width: 90%;
        max-width: 600px;
        color: #ddd;
    }

    .receipt-header {
        text-align: center;
        border-bottom: 2px solid #555;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }

    .receipt-header h2 {
        color: #f0f0f0;
        margin: 0;
    }

    .receipt-body {
        line-height: 1.8;
    }

    .receipt-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .receipt-total {
        border-top: 2px solid #555;
        padding-top: 15px;
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        font-size: 16px;
        font-weight: bold;
        color: #90ee90;
    }

    .close-receipt {
        color: #999;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-receipt:hover {
        color: #f0f0f0;
    }

    .print-btn {
        background-color: #5bc0de;
        color: #1e1e1e;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 20px;
        font-weight: 600;
    }

    .print-btn:hover {
        background-color: #46b8da;
    }

    .export-section {
        margin-top: 30px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .export-btn {
        background-color: #5cb85c;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }

    .export-btn:hover {
        background-color: #4cae4c;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .receipt-content, .receipt-content * {
            visibility: visible;
        }
        .print-btn, .close-receipt {
            display: none;
        }
    }
</style>

<div class="container" style="margin-top: 100px;">
    <div class="report-container">
        <h1>Medicine Financial Report</h1>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card card-income">
                <h3>Total Sales Income</h3>
                <div class="amount">${{ number_format($totalIncome, 2) }}</div>
                <p style="color: #666; margin-top: 10px;">{{ $salesCount }} orders</p>
            </div>

            <div class="summary-card card-expense">
                <h3>Total Stock Expenses</h3>
                <div class="amount">${{ number_format($totalExpenses, 2) }}</div>
                <p style="color: #666; margin-top: 10px;">{{ $stockAdditions }} stock additions</p>
            </div>

            <div class="summary-card card-balance">
                <h3>Net Balance</h3>
                <div class="amount" style="color: {{ $netBalance >= 0 ? '#90ee90' : '#ff6b6b' }}">
                    ${{ number_format(abs($netBalance), 2) }}
                </div>
                <p style="color: #666; margin-top: 10px;">{{ $netBalance >= 0 ? 'Profit' : 'Loss' }}</p>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="transactions-section">
            <h2>Transaction Details</h2>

            <!-- Filters -->
            <div class="transaction-filters">
                <input type="date" id="filterDateFrom" placeholder="From Date" style="background-color: #1e1e1e; border: 1px solid #444; border-radius: 6px; padding: 10px 12px; color: #fff; font-size: 13px;">
                <input type="date" id="filterDateTo" placeholder="To Date" style="background-color: #1e1e1e; border: 1px solid #444; border-radius: 6px; padding: 10px 12px; color: #fff; font-size: 13px;">
                <select id="filterType" style="background-color: #1e1e1e; border: 1px solid #444; border-radius: 6px; padding: 10px 12px; color: #fff; font-size: 13px;">
                    <option value="">All Types</option>
                    <option value="sale">Sales Only</option>
                    <option value="stock_addition">Stock Only</option>
                </select>
                <input type="text" id="filterDescription" placeholder="Search description..." style="background-color: #1e1e1e; border: 1px solid #444; border-radius: 6px; padding: 10px 12px; color: #fff; font-size: 13px;">
                <input type="number" id="filterAmount" placeholder="Amount" style="background-color: #1e1e1e; border: 1px solid #444; border-radius: 6px; padding: 10px 12px; color: #fff; font-size: 13px;">
                <button onclick="resetFilters()" style="background-color: #5bc0de; color: #1e1e1e; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Reset</button>
            </div>

            @if($transactions->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="transactionBody">
                            @foreach($transactions as $transaction)
                                <tr class="transaction-row" data-id="{{ $transaction->id }}" data-date="{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d') }}" data-type="{{ $transaction->type }}" data-description="{{ $transaction->description }}" data-amount="{{ $transaction->amount }}">
                                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($transaction->type === 'sale')
                                            <span class="transaction-type-sale">SALE</span>
                                        @else
                                            <span class="transaction-type-stock">STOCK</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>
                                        <span class="{{ $transaction->amount > 0 ? 'amount-positive' : 'amount-negative' }}">
                                            {{ $transaction->amount > 0 ? '+' : '' }}${{ number_format(abs($transaction->amount), 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="color: {{ $transaction->balance >= 0 ? '#90ee90' : '#ff6b6b' }}">
                                            ${{ number_format($transaction->balance, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(session('level') >= 3)
                                            <button onclick="deleteTransaction({{ $transaction->id }})" style="background-color: #d9534f; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">Delete</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="no-transactions">
                    <p>No transactions recorded yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div id="receiptModal" class="receipt-modal">
    <div class="receipt-content">
        <span class="close-receipt" onclick="closeReceipt()">&times;</span>
        <div class="receipt-header">
            <h2>Receipt</h2>
            <p style="color: #999; margin: 10px 0 0 0;">Order Summary</p>
        </div>
        <div class="receipt-body" id="receiptBody">
            <!-- Receipt content will be populated here -->
        </div>
        <button class="print-btn" onclick="window.print()">Print Receipt</button>
    </div>
</div>

<script>
function viewReceipt(orderId) {
    // Placeholder for receipt viewing - would need order details endpoint
    console.log('View receipt for order', orderId);
}

function closeReceipt() {
    document.getElementById('receiptModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('receiptModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Filter functionality
function applyFilters() {
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;
    const type = document.getElementById('filterType').value;
    const description = document.getElementById('filterDescription').value.toLowerCase();
    const amount = document.getElementById('filterAmount').value;

    const rows = document.querySelectorAll('.transaction-row');
    rows.forEach(row => {
        let show = true;
        
        // Date range filter
        if (dateFrom || dateTo) {
            const rowDate = row.getAttribute('data-date');
            if (dateFrom && rowDate < dateFrom) show = false;
            if (dateTo && rowDate > dateTo) show = false;
        }
        
        // Type filter
        if (type && row.getAttribute('data-type') !== type) show = false;
        
        // Description filter
        if (description && !row.getAttribute('data-description').toLowerCase().includes(description)) show = false;
        
        // Amount filter
        if (amount && Math.abs(parseFloat(row.getAttribute('data-amount'))) !== Math.abs(parseFloat(amount))) show = false;
        
        row.style.display = show ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filterDateFrom')?.value = '';
    document.getElementById('filterDateTo')?.value = '';
    document.getElementById('filterType')?.value = '';
    document.getElementById('filterDescription')?.value = '';
    document.getElementById('filterAmount')?.value = '';
    applyFilters();
}

// Add event listeners to filters
document.getElementById('filterDateFrom')?.addEventListener('change', applyFilters);
document.getElementById('filterDateTo')?.addEventListener('change', applyFilters);
document.getElementById('filterType')?.addEventListener('change', applyFilters);
document.getElementById('filterDescription')?.addEventListener('input', applyFilters);
document.getElementById('filterAmount')?.addEventListener('input', applyFilters);

// Delete transaction
async function deleteTransaction(transactionId) {
    if (!confirm('Are you sure you want to delete this transaction? Super admins can restore it from the Deleted Transactions log.')) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch(`/transaction/${transactionId}/delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            credentials: 'same-origin'
        });

        const data = await response.json();
        if (data.success) {
            alert('Transaction deleted successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        alert('Error deleting transaction: ' + error.message);
    }
}
</script>

