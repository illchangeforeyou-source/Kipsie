@php
    echo view('header');
@endphp

<style>
    body {
        background-color: #1e1e1e;
        color: #f5f5f5;
    }

    .container {
        margin-top: 100px;
    }

    .page-header h1 {
        color: #f0f0f0;
        margin-bottom: 10px;
    }

    .card {
        background-color: #2a2a2a;
        border: 1px solid #444;
        color: #f5f5f5;
        margin-bottom: 20px;
    }

    .order-card {
        background-color: #3a3a3a;
        border-left: 4px solid #b0b0b0;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }

    .order-id {
        color: #f0f0f0;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .order-date {
        color: #999;
        font-size: 0.9rem;
    }

    .timeline {
        margin: 20px 0;
        position: relative;
    }

    .timeline-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        position: relative;
    }

    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 24px;
        top: 40px;
        width: 2px;
        height: 20px;
        background-color: #555;
    }

    .timeline-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 15px;
        position: relative;
        z-index: 1;
    }

    .timeline-icon.pending {
        background-color: #5a5a2a;
        color: #ffff99;
        border: 2px solid #5a5a2a;
    }

    .timeline-icon.processing {
        background-color: #4a5a5a;
        color: #99daff;
        border: 2px solid #4a5a5a;
    }

    .timeline-icon.shipped {
        background-color: #5a4a2a;
        color: #ffc99a;
        border: 2px solid #5a4a2a;
    }

    .timeline-icon.delivered {
        background-color: #2a5a2a;
        color: #99ff99;
        border: 2px solid #2a5a2a;
    }

    .timeline-icon.cancelled {
        background-color: #5a2a2a;
        color: #ff9999;
        border: 2px solid #5a2a2a;
    }

    .timeline-icon.inactive {
        background-color: #2f2f2f;
        color: #777;
        border: 2px solid #444;
    }

    .timeline-content h6 {
        color: #f0f0f0;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .timeline-content p {
        color: #bbb;
        margin: 0;
        font-size: 0.9rem;
    }

    .badge {
        padding: 0.5em 1em;
        font-size: 0.9em;
    }

    .status-pending {
        background-color: #5a5a2a;
        color: #ffff99;
    }

    .status-processing {
        background-color: #4a5a5a;
        color: #99daff;
    }

    .status-shipped {
        background-color: #5a4a2a;
        color: #ffc99a;
    }

    .status-delivered {
        background-color: #2a5a2a;
        color: #99ff99;
    }

    .status-cancelled {
        background-color: #5a2a2a;
        color: #ff9999;
    }

    .order-info {
        background-color: #2a2a2a;
        padding: 15px;
        border-radius: 5px;
        margin-top: 15px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .info-label {
        color: #999;
    }

    .info-value {
        color: #f0f0f0;
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
</style>

<div class="container">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>📦 My Deliveries</h1>
            <p class="text-muted">Track the status of your medicine orders</p>
        </div>
    </div>

    @if($orders->count() == 0)
        <div class="alert alert-info" style="background-color: #1a3a4a; border-color: #3a6a7a; color: #f5f5f5;">
            <strong>No orders yet.</strong> Your delivery status will appear here once you place an order.
        </div>
    @else
        @foreach($orders as $order)
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">Order #{{ $order->id }}</div>
                        <div class="order-date">{{ $order->created_at->format('d F Y H:i') }}</div>
                    </div>
                    <span class="badge status-{{ strtolower($order->delivery_status) }}">
                        {{ ucfirst($order->delivery_status) }}
                    </span>
                </div>

                <!-- Timeline -->
                <div class="timeline">
                    <!-- Pending -->
                    <div class="timeline-item">
                        <div class="timeline-icon {{ in_array($order->delivery_status, ['pending', 'processing', 'shipped', 'delivered']) ? 'pending' : 'inactive' }}">
                            ✓
                        </div>
                        <div class="timeline-content">
                            <h6>Order Placed</h6>
                            <p>{{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Processing -->
                    <div class="timeline-item">
                        <div class="timeline-icon {{ in_array($order->delivery_status, ['processing', 'shipped', 'delivered']) ? 'processing' : 'inactive' }}">
                            ⏳
                        </div>
                        <div class="timeline-content">
                            <h6>Processing</h6>
                            <p>Preparing your medicine for shipment</p>
                        </div>
                    </div>

                    <!-- Shipped -->
                    <div class="timeline-item">
                        <div class="timeline-icon {{ in_array($order->delivery_status, ['shipped', 'delivered']) ? 'shipped' : 'inactive' }}">
                            🚚
                        </div>
                        <div class="timeline-content">
                            <h6>Shipped</h6>
                            <p>{{ $order->shipped_at ? $order->shipped_at->format('d M Y H:i') : 'Awaiting shipment' }}</p>
                        </div>
                    </div>

                    <!-- Delivered -->
                    <div class="timeline-item">
                        <div class="timeline-icon {{ $order->delivery_status == 'delivered' ? 'delivered' : 'inactive' }}">
                            ✓
                        </div>
                        <div class="timeline-content">
                            <h6>Delivered</h6>
                            <p>{{ $order->delivered_at ? $order->delivered_at->format('d M Y H:i') : 'Awaiting delivery' }}</p>
                        </div>
                    </div>

                    <!-- Cancelled -->
                    @if($order->delivery_status == 'cancelled')
                        <div class="timeline-item">
                            <div class="timeline-icon cancelled">
                                ✗
                            </div>
                            <div class="timeline-content">
                                <h6>Cancelled</h6>
                                <p>{{ $order->delivery_notes ?? 'Order has been cancelled' }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Order Details -->
                <div class="order-info">
                    <div class="info-row">
                        <span class="info-label">Total Amount:</span>
                        <span class="info-value">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Status:</span>
                        <span class="info-value">{{ ucfirst($order->status) }}</span>
                    </div>
                    @if($order->delivery_notes)
                        <div class="info-row">
                            <span class="info-label">Notes:</span>
                            <span class="info-value">{{ $order->delivery_notes }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

@php
    echo view('footer');
@endphp
