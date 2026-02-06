@php
    echo view('header');
@endphp

<style>
    body {
        background-color: #1e1e1e;
        color: #f5f5f5;
    }

    .container-fluid {
        margin-top: 100px;
        background-color: #2b2b2b;
        border: 1px solid #444;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
    }

    .page-header h1 {
        color: #f0f0f0;
        border-bottom: 2px solid #555;
        padding-bottom: 15px;
        margin-bottom: 10px;
    }

    .stat-box {
        background-color: #3a3a3a;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #b0b0b0;
        margin-bottom: 10px;
    }

    .stat-label {
        color: #999;
        font-size: 0.85rem;
    }

    .stat-value {
        color: #b0b0b0;
        font-size: 1.5rem;
        font-weight: bold;
        margin-top: 5px;
    }

    .card {
        background-color: #2a2a2a;
        border: 1px solid #444;
        color: #f5f5f5;
    }

    .card-header {
        background-color: #3d3d3d !important;
        border-bottom: 1px solid #555 !important;
    }

    .table {
        color: #f5f5f5;
        background-color: #2a2a2a;
    }

    .table thead {
        background-color: #3d3d3d;
    }

    .table thead th {
        color: #f0f0f0;
        border-color: #555;
    }

    .table tbody td {
        border-color: #444;
        color: #e0e0e0;
        background-color: #2a2a2a;
    }

    .table tbody tr:hover {
        background-color: #2f2f2f;
    }

    .btn-primary {
        background-color: #b0b0b0;
        border: none;
        color: #1e1e1e;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #d0d0d0;
        color: #000;
    }

    .btn-secondary {
        background-color: #555;
        border: none;
        color: #f5f5f5;
    }

    .btn-secondary:hover {
        background-color: #666;
    }

    .filter-section {
        background-color: #2a2a2a;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 1px solid #444;
    }

    .form-control, .form-select {
        background-color: #2f2f2f;
        border-color: #555;
        color: #f5f5f5;
    }

    .form-control:focus, .form-select:focus {
        background-color: #333;
        border-color: #777;
        color: #f5f5f5;
    }

    .form-label {
        color: #e0e0e0;
    }

    .badge {
        padding: 0.4em 0.6em;
        font-size: 0.85em;
    }

    .pagination {
        background-color: #2a2a2a;
    }

    .page-link {
        background-color: #2f2f2f;
        color: #f5f5f5;
        border-color: #555;
    }

    .page-link:hover {
        background-color: #3d3d3d;
        color: #f5f5f5;
        border-color: #666;
    }

    .page-link.active {
        background-color: #b0b0b0;
        border-color: #b0b0b0;
        color: #1e1e1e;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>💰 Financial Report</h1>
            <p class="text-muted">Detailed revenue and order analysis</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manager.dashboard') }}" class="btn btn-secondary me-2">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-label">Completed Revenue</div>
                <div class="stat-value" style="color: #99ff99;">Rp{{ number_format($completedRevenue, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-label">Pending Revenue</div>
                <div class="stat-value" style="color: #ffff99;">Rp{{ number_format($pendingRevenue, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ count($orders) }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('manager.financial-report') }}" class="row g-2">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="filter_status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ $filterStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $filterStatus == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $filterStatus == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-md-3" style="display: flex; gap: 10px; align-items: flex-end;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Filter</button>
                <a href="{{ route('manager.financial-report') }}" class="btn btn-secondary" style="flex: 1;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Order Details</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="8%">Order ID</th>
                        <th width="15%">Customer</th>
                        <th width="25%">Items</th>
                        <th width="12%">Amount</th>
                        <th width="10%">Status</th>
                        <th width="15%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>{{ $order->customer_name ?? 'N/A' }}</td>
                            <td>
                                @if($order->items)
                                    <small>
                                        @foreach($order->items as $item)
                                            <div>{{ $item['name'] ?? 'Unknown' }} x{{ $item['quantity'] ?? 1 }}</div>
                                        @endforeach
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td><strong style="color: #b0b0b0;">Rp{{ number_format($order->total, 0, ',', '.') }}</strong></td>
                            <td>
                                @php
                                    $statusColor = match($order->status) {
                                        'completed' => 'background-color: #2a5a2a; color: #99ff99;',
                                        'pending' => 'background-color: #5a5a2a; color: #ffff99;',
                                        'cancelled' => 'background-color: #5a2a2a; color: #ff9999;',
                                        default => 'background-color: #444; color: #999;'
                                    };
                                @endphp
                                <span class="badge" style="{{ $statusColor }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
</div>

@php
    echo view('footer');
@endphp
