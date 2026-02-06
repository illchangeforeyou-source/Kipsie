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

    .stat-card {
        background-color: #3a3a3a;
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid #b0b0b0;
        margin-bottom: 15px;
        text-align: center;
    }

    .stat-label {
        color: #999;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .stat-value {
        color: #b0b0b0;
        font-size: 2rem;
        font-weight: bold;
    }

    .stat-card.revenue {
        border-left-color: #5a9;
    }

    .stat-card.orders {
        border-left-color: #9a7;
    }

    .stat-card.stock {
        border-left-color: #a95;
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

    .card-body {
        background-color: #2a2a2a;
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

    .btn-info {
        background-color: #789;
        border: none;
        color: white;
    }

    .btn-info:hover {
        background-color: #89a;
    }

    .table {
        color: #f5f5f5;
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
    }

    .table tbody tr:hover {
        background-color: #2f2f2f;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>📊 Manager Dashboard</h1>
            <p class="text-muted">Financial and stock reports overview</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manager.financial-report') }}" class="btn btn-info me-2">
                💰 Financial Report
            </a>
            <a href="{{ route('manager.stock-report') }}" class="btn btn-info">
                📦 Stock Report
            </a>
        </div>
    </div>

    <!-- Financial Statistics -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 style="color: #b0b0b0; margin-bottom: 15px;">💰 Financial Overview</h4>
        </div>
        <div class="col-md-3">
            <div class="stat-card revenue">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card revenue">
                <div class="stat-label">Completed Orders Revenue</div>
                <div class="stat-value">Rp{{ number_format($completedRevenue ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card orders">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ $totalOrders }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card orders">
                <div class="stat-label">Completed Orders</div>
                <div class="stat-value">{{ $completedOrders }}</div>
            </div>
        </div>
    </div>

    <!-- Stock Statistics -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 style="color: #b0b0b0; margin-bottom: 15px;">📦 Stock Overview</h4>
        </div>
        <div class="col-md-3">
            <div class="stat-card stock">
                <div class="stat-label">Total Medicines</div>
                <div class="stat-value">{{ $totalMedicines }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stock">
                <div class="stat-label">Low Stock</div>
                <div class="stat-value" style="color: #ffc99a;">{{ $lowStockMedicines }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stock">
                <div class="stat-label">Out of Stock</div>
                <div class="stat-value" style="color: #ff9999;">{{ $outOfStock }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stock">
                <div class="stat-label">Stock Status</div>
                <div class="stat-value" style="color: #99ff99;">✓ OK</div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">📋 Recent Orders</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="10%">Order ID</th>
                        <th width="20%">Customer</th>
                        <th width="20%">Amount</th>
                        <th width="15%">Status</th>
                        <th width="15%">Delivery</th>
                        <th width="20%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->customer_name ?? 'N/A' }}</td>
                            <td><strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong></td>
                            <td>
                                <span class="badge" style="background-color: {{ $order->status == 'completed' ? '#2a5a2a' : '#5a5a2a' }}; color: {{ $order->status == 'completed' ? '#99ff99' : '#ffff99' }};">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ in_array($order->delivery_status, ['delivered']) ? '#2a5a2a' : '#4a5a5a' }}; color: {{ in_array($order->delivery_status, ['delivered']) ? '#99ff99' : '#99daff' }};">
                                    {{ ucfirst($order->delivery_status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No orders yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <p style="text-align: center; color: #777; font-size: 0.9rem;">
            Use the reports button above to view detailed financial and stock information
        </p>
    </div>
</div>

@php
    echo view('footer');
@endphp
