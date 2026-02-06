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

    .card {
        background-color: #2a2a2a;
        border: 1px solid #444;
        color: #f5f5f5;
    }

    .card-header {
        background-color: #3d3d3d !important;
        border-bottom: 1px solid #555 !important;
    }

    .card-header h5 {
        color: #f0f0f0;
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

    .stat-card {
        background-color: #3a3a3a;
        padding: 15px;
        border-radius: 10px;
        border-left: 4px solid #b0b0b0;
        margin-bottom: 15px;
    }

    .stat-card h6 {
        color: #999;
        font-size: 0.85rem;
        margin-bottom: 5px;
    }

    .stat-card .number {
        color: #b0b0b0;
        font-size: 1.8rem;
        font-weight: bold;
    }

    .pagination {
        background-color: #2a2a2a;
        border: 1px solid #444;
        border-radius: 10px;
        padding: 10px;
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

    .status-pending {
        background-color: #5a5a2a;
        color: #ffff99;
    }

    .status-completed {
        background-color: #2a5a2a;
        color: #99ff99;
    }

    .status-cancelled {
        background-color: #5a2a2a;
        color: #ff9999;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>📊 Purchase History</h1>
            <p class="text-muted">View and manage all customer orders and sales</p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <h6>Total Sales</h6>
                <div class="number">Rp{{ number_format($totalSales, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6>Total Orders</h6>
                <div class="number">{{ $totalOrders }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6>Pending Orders</h6>
                <div class="number">{{ $pendingOrders }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6>Completed Orders</h6>
                <div class="number">{{ $completedOrders }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.purchase-history') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Customer or Order ID..." 
                    value="{{ $searchTerm }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="filter_status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ $filterStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $filterStatus == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $filterStatus == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Min Price</label>
                <input type="number" name="price_from" class="form-control" step="0.01" value="{{ $priceFrom }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Max Price</label>
                <input type="number" name="price_to" class="form-control" step="0.01" value="{{ $priceTo }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Sort By</label>
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Date</option>
                    <option value="customer_name" {{ $sortBy == 'customer_name' ? 'selected' : '' }}>Name</option>
                    <option value="total" {{ $sortBy == 'total' ? 'selected' : '' }}>Total</option>
                    <option value="status" {{ $sortBy == 'status' ? 'selected' : '' }}>Status</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Order</label>
                <select name="sort_order" class="form-select">
                    <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Newest</option>
                    <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Oldest</option>
                </select>
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">🔍 Filter</button>
                <a href="{{ route('admin.purchase-history') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Order Records</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="8%">Order ID</th>
                        <th width="15%">Customer</th>
                        <th width="35%">Items</th>
                        <th width="12%">Total Amount</th>
                        <th width="10%">Status</th>
                        <th width="15%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->customer_name ?? 'N/A' }}</td>
                            <td>
                                @if($order->items)
                                    <small>
                                        @foreach($order->items as $item)
                                            <div>{{ $item['name'] ?? 'Unknown' }} x{{ $item['quantity'] ?? 1 }}</div>
                                        @endforeach
                                    </small>
                                @else
                                    <small class="text-muted">No items</small>
                                @endif
                            </td>
                            <td><strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong></td>
                            <td>
                                @php
                                    $statusClass = match($order->status) {
                                        'pending' => 'status-pending',
                                        'completed' => 'status-completed',
                                        'cancelled' => 'status-cancelled',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
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
