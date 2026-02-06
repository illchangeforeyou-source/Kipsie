@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">Cashier Dashboard</h2>
            <p class="text-muted">Manage payments and transactions</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex gap-2 justify-content-end">
                <small class="text-muted">Last Updated: <span id="lastUpdate">Now</span></small>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-dark text-light">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Pending Payments</h6>
                    <h2 class="mb-0">{{ $pendingPayments->count() }}</h2>
                    <small class="text-muted">Amount: Rp {{ number_format($pendingPayments->sum('amount'), 0, ',', '.') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-dark text-light">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Today's Income</h6>
                    <h2 class="mb-0">Rp {{ number_format($totals['today_income'], 0, ',', '.') }}</h2>
                    <small class="text-muted">From confirmed orders</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-dark text-light">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Total Users</h6>
                    <h2 class="mb-0">{{ $users->count() }}</h2>
                    <small class="text-muted">Active customers</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs border-bottom mb-4" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="payments-tab" data-bs-toggle="tab" href="#payments" role="tab">
                        <i class="fas fa-credit-card me-2"></i>Pending Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="users-tab" data-bs-toggle="tab" href="#users" role="tab">
                        <i class="fas fa-users me-2"></i>Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="transactions-tab" data-bs-toggle="tab" href="#transactions" role="tab">
                        <i class="fas fa-exchange-alt me-2"></i>Daily Transactions
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Pending Payments Tab -->
                <div class="tab-pane fade show active" id="payments" role="tabpanel">
                    @if($pendingPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Submitted</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingPayments as $payment)
                                        <tr class="align-middle">
                                            <td class="fw-bold">{{ $payment->order_id ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($payment->user && (isset($payment->user->profile_picture) && $payment->user->profile_picture))
                                                        <img src="{{ asset('storage/' . $payment->user->profile_picture) }}" alt="Profile" class="rounded-circle" width="32" height="32">
                                                    @else
                                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                    @endif
                                                    <span>{{ $payment->user ? ($payment->user->username ?? 'Unknown') : 'Unknown' }}</span>
                                                </div>
                                            </td>
                                            <td><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                                            <td><small class="text-muted">{{ $payment->created_at->format('d M H:i') }}</small></td>
                                            <td>
                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal{{ $payment->id }}">
                                                    <i class="fas fa-check me-1"></i>Confirm
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $payment->id }}">
                                                    <i class="fas fa-times me-1"></i>Reject
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Confirm Modal -->
                                        <div class="modal fade" id="confirmModal{{ $payment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content bg-dark text-light border-secondary">
                                                    <div class="modal-header border-secondary">
                                                        <h5 class="modal-title">Confirm Payment</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to confirm this payment?</p>
                                                        <div class="bg-secondary p-3 rounded">
                                                            <small class="d-block text-muted">Order ID: {{ $payment->order_id }}</small>
                                                            <small class="d-block text-muted">Customer: {{ $payment->user->username ?? 'Unknown' }}</small>
                                                            <small class="d-block text-muted">Amount: Rp {{ number_format($payment->amount, 0, ',', '.') }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-secondary">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form method="POST" action="{{ route('cashier.confirm-payment', $payment->id) }}" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success">Confirm Payment</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $payment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content bg-dark text-light border-secondary">
                                                    <div class="modal-header border-secondary">
                                                        <h5 class="modal-title">Reject Payment</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="{{ route('cashier.reject-payment', $payment->id) }}">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Reject this payment. Provide a reason:</p>
                                                            <div class="bg-secondary p-3 rounded mb-3">
                                                                <small class="d-block text-muted">Order ID: {{ $payment->order_id }}</small>
                                                                <small class="d-block text-muted">Customer: {{ $payment->user->username ?? 'Unknown' }}</small>
                                                                <small class="d-block text-muted">Amount: Rp {{ number_format($payment->amount, 0, ',', '.') }}</small>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="reject_reason{{ $payment->id }}" class="form-label">Reason:</label>
                                                                <textarea class="form-control bg-secondary border-secondary text-light" id="reject_reason{{ $payment->id }}" name="reason" rows="3" placeholder="Why are you rejecting this payment?" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-secondary">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Reject Payment</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>No pending payments at the moment.
                        </div>
                    @endif
                </div>

                <!-- Users Tab -->
                <div class="tab-pane fade" id="users" role="tabpanel">
                    <div class="alert alert-info">
                        <h5>Total Users: {{ $users->count() }}</h5>
                        <p>Active customers in the system</p>
                    </div>
                </div>

                <!-- Daily Transactions Tab -->
                <div class="tab-pane fade" id="transactions" role="tabpanel">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Reference</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr class="align-middle">
                                            <td>
                                                @if($transaction->type === 'income')
                                                    <span class="badge bg-success">Income</span>
                                                @elseif($transaction->type === 'expense')
                                                    <span class="badge bg-danger">Expense</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $transaction->type }}</span>
                                                @endif
                                            </td>
                                            <td><small>{{ $transaction->category ?? '-' }}</small></td>
                                            <td class="fw-bold">
                                                @if($transaction->type === 'income')
                                                    <span class="text-success">+ Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-danger">- Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td><small class="text-muted">{{ $transaction->reference_id ?? '-' }}</small></td>
                                            <td><small class="text-muted">{{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d M H:i') : '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>No transactions found for today.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        color: #888;
        border-bottom: 3px solid transparent;
    }

    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border-color: transparent;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-color: #0d6efd;
        background: transparent;
    }

    .table {
        color: #f0f0f0;
    }

    .table-hover tbody tr:hover {
        background-color: #3a3a3a;
    }

    .btn-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
    }
</style>

<script>
    // Update last updated time
    function updateLastUpdate() {
        const now = new Date();
        document.getElementById('lastUpdate').textContent = now.toLocaleTimeString();
    }

    // Handle confirm payment form submission
    document.addEventListener('submit', function(e) {
        const form = e.target;
        
        // Check if it's a confirm payment form
        if (form.action.includes('confirm')) {
            e.preventDefault();
            
            const submitBtn = e.submitter;
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';

            const csrfToken = document.querySelector('input[name="_token"]')?.value || 
                            form.querySelector('input[name="_token"]')?.value ||
                            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                    if (modal) modal.hide();
                    
                    // Reload table after 1 second
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('danger', data.message || 'Failed to confirm payment');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        }
        
        // Check if it's a reject payment form
        else if (form.action.includes('reject')) {
            e.preventDefault();
            
            const submitBtn = e.submitter;
            const originalText = submitBtn.textContent;
            const reasonInput = form.querySelector('textarea[name="reason"]');
            const reason = reasonInput ? reasonInput.value : '';
            
            if (!reason.trim()) {
                showAlert('warning', 'Please provide a reason for rejection');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';

            const csrfToken = document.querySelector('input[name="_token"]')?.value || 
                            form.querySelector('input[name="_token"]')?.value ||
                            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                    if (modal) modal.hide();
                    
                    // Reload table after 1 second
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('danger', data.message || 'Failed to reject payment');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        }
    });

    // Show alert message
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert at top of page
        document.body.insertAdjacentElement('afterbegin', alertDiv);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Auto-refresh every 30 seconds
    setInterval(() => {
        location.reload();
    }, 30000);
</script>
@endsection
