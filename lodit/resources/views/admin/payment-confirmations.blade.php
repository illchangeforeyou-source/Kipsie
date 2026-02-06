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

    .card-body {
        background-color: #2a2a2a;
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

    .btn-success {
        background-color: #5a9;
        border: none;
        color: white;
    }

    .btn-success:hover {
        background-color: #6ba;
    }

    .btn-danger {
        background-color: #a54;
        border: none;
        color: white;
    }

    .btn-danger:hover {
        background-color: #b65;
    }

    .btn-secondary {
        background-color: #555;
        border: none;
        color: #f5f5f5;
    }

    .btn-secondary:hover {
        background-color: #666;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
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

    .status-pending {
        background-color: #5a5a2a;
        color: #ffff99;
    }

    .status-confirmed {
        background-color: #2a5a2a;
        color: #99ff99;
    }

    .status-rejected {
        background-color: #5a2a2a;
        color: #ff9999;
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

    .alert {
        background-color: #2a2a2a;
        border-color: #444;
        color: #f5f5f5;
    }

    .alert-success {
        background-color: #1a3a2a;
        border-color: #3a6a5a;
    }

    .alert-danger {
        background-color: #3a1a1a;
        border-color: #6a3a3a;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>💳 Payment Confirmations</h1>
            <p class="text-muted">Review and process pending payment confirmations</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.payment-confirmations') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search customer..." 
                    value="{{ $searchTerm }}">
            </div>

            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" placeholder="From" value="{{ $dateFrom }}">
            </div>

            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" placeholder="To" value="{{ $dateTo }}">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>

            <div class="col-md-12">
                <a href="{{ route('admin.payment-confirmations') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Confirmations Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Pending Payment Confirmations</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="8%">ID</th>
                        <th width="12%">Order</th>
                        <th width="15%">Customer</th>
                        <th width="12%">Amount</th>
                        <th width="12%">Method</th>
                        <th width="10%">Status</th>
                        <th width="20%">Date</th>
                        <th width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($confirmations as $confirmation)
                        <tr>
                            <td>#{{ $confirmation->id }}</td>
                            <td>
                                <a href="#" class="text-light text-decoration-none">
                                    #{{ $confirmation->order->id ?? 'N/A' }}
                                </a>
                            </td>
                            <td>{{ $confirmation->user->username ?? 'N/A' }}</td>
                            <td><strong>Rp{{ number_format($confirmation->amount, 0, ',', '.') }}</strong></td>
                            <td>{{ ucfirst($confirmation->payment_method) }}</td>
                            <td>
                                <span class="badge status-{{ strtolower($confirmation->status) }}">
                                    {{ ucfirst($confirmation->status) }}
                                </span>
                            </td>
                            <td>{{ $confirmation->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($confirmation->status == 'pending')
                                    <button class="btn btn-success btn-sm me-2" onclick="confirmPayment({{ $confirmation->id }})">
                                        ✓ Confirm
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="rejectPayment({{ $confirmation->id }})">
                                        ✗ Reject
                                    </button>
                                @else
                                    <small class="text-muted">Processed</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No pending confirmations</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $confirmations->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="confirmNotes" rows="3" placeholder="Add any notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitConfirm()">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Reason for Rejection</label>
                    <textarea class="form-control" id="rejectNotes" rows="3" placeholder="Explain why..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitReject()">Reject</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentConfirmationId = null;

    function confirmPayment(id) {
        currentConfirmationId = id;
        const modal = new bootstrap.Modal(document.getElementById('confirmPaymentModal'));
        modal.show();
    }

    function rejectPayment(id) {
        currentConfirmationId = id;
        const modal = new bootstrap.Modal(document.getElementById('rejectPaymentModal'));
        modal.show();
    }

    async function submitConfirm() {
        const notes = document.getElementById('confirmNotes').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/admin/payment-confirmation/${currentConfirmationId}/confirm`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ notes: notes })
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
            alert('Error confirming payment');
        }
    }

    async function submitReject() {
        const notes = document.getElementById('rejectNotes').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/admin/payment-confirmation/${currentConfirmationId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ notes: notes })
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
            alert('Error rejecting payment');
        }
    }
</script>

@php
    echo view('footer');
@endphp
