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

    .btn-info {
        background-color: #789;
        border: none;
        color: white;
    }

    .btn-info:hover {
        background-color: #89a;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .badge {
        padding: 0.4em 0.6em;
        font-size: 0.85em;
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

    .modal-content {
        background-color: #2a2a2a;
        color: #f5f5f5;
        border: 1px solid #444;
    }

    .modal-header {
        background-color: #3d3d3d;
        border-bottom: 1px solid #555;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
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
</style>

<div class="container-fluid">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>🚚 Delivery Tracking</h1>
            <p class="text-muted">Manage and track all medicine deliveries</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('delivery.all') }}" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search customer..." 
                    value="{{ $searchTerm }}">
            </div>

            <div class="col-md-4">
                <select name="filter_status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ $filterStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $filterStatus == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ $filterStatus == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ $filterStatus == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $filterStatus == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>

            <div class="col-md-12">
                <a href="{{ route('delivery.all') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Delivery Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Deliveries</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="8%">Order ID</th>
                        <th width="15%">Customer</th>
                        <th width="12%">Amount</th>
                        <th width="12%">Status</th>
                        <th width="13%">Delivery Status</th>
                        <th width="18%">Date</th>
                        <th width="22%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>{{ $order->customer_name ?? 'N/A' }}</td>
                            <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $order->status == 'completed' ? '#2a5a2a' : '#5a5a2a' }}; color: {{ $order->status == 'completed' ? '#99ff99' : '#ffff99' }};">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge status-{{ strtolower($order->delivery_status) }}">
                                    {{ ucfirst($order->delivery_status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="updateDelivery({{ $order->id }}, '{{ $order->delivery_status }}')">
                                    📦 Update
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No deliveries found</td>
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

<!-- Update Delivery Modal -->
<div class="modal fade" id="updateDeliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Delivery Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Delivery Status</label>
                    <select class="form-select" id="deliveryStatus">
                        <option value="pending">⏳ Pending</option>
                        <option value="processing">⚙️ Processing</option>
                        <option value="shipped">🚚 Shipped</option>
                        <option value="delivered">✓ Delivered</option>
                        <option value="cancelled">✗ Cancelled</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="deliveryNotes" rows="3" placeholder="Add delivery notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitDeliveryUpdate()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentOrderId = null;

    function updateDelivery(orderId, currentStatus) {
        currentOrderId = orderId;
        document.getElementById('deliveryStatus').value = currentStatus;
        document.getElementById('deliveryNotes').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('updateDeliveryModal'));
        modal.show();
    }

    async function submitDeliveryUpdate() {
        const status = document.getElementById('deliveryStatus').value;
        const notes = document.getElementById('deliveryNotes').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/delivery/${currentOrderId}/update-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    delivery_status: status,
                    delivery_notes: notes
                })
            });

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error updating delivery status');
        }
    }
</script>

@php
    echo view('footer');
@endphp
