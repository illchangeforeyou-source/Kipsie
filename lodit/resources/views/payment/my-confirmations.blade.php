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

    .card-header {
        background-color: #3d3d3d !important;
        border-bottom: 1px solid #555 !important;
    }

    .card-body {
        background-color: #2a2a2a;
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

    .confirmation-item {
        background-color: #3a3a3a;
        border-left: 4px solid #b0b0b0;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .confirmation-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 10px;
    }

    .confirmation-title {
        color: #f0f0f0;
        font-size: 1rem;
    }

    .confirmation-meta {
        color: #999;
        font-size: 0.9rem;
    }

    .amount-display {
        color: #b0b0b0;
        font-weight: bold;
        font-size: 1.2rem;
        margin: 10px 0;
    }

    .status-display {
        background-color: #2a2a2a;
        padding: 10px;
        border-radius: 5px;
        margin: 10px 0;
    }

    .alert {
        background-color: #2a2a2a;
        border-color: #444;
        color: #f5f5f5;
    }

    .alert-info {
        background-color: #1a3a4a;
        border-color: #3a6a7a;
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

<div class="container">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>💳 My Payment Confirmations</h1>
            <p class="text-muted">Track the status of your payment confirmations</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($confirmations->count() == 0)
        <div class="alert alert-info">
            <strong>No payment confirmations yet.</strong> When you make a purchase, your payment confirmation will appear here.
        </div>
    @else
        <!-- Confirmations List -->
        @foreach($confirmations as $confirmation)
            <div class="confirmation-item">
                <div class="confirmation-header">
                    <div class="flex-grow-1">
                        <h5 class="confirmation-title mb-1">
                            Order #{{ $confirmation->order->id ?? 'N/A' }}
                        </h5>
                        <div class="confirmation-meta">
                            {{ $confirmation->created_at->format('d M Y H:i') }}
                            @if($confirmation->confirmed_at)
                                • Processed {{ $confirmation->confirmed_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                    <span class="badge status-{{ strtolower($confirmation->status) }}">
                        {{ ucfirst($confirmation->status) }}
                    </span>
                </div>

                <div class="amount-display">
                    💰 Rp{{ number_format($confirmation->amount, 0, ',', '.') }}
                </div>

                <div class="status-display">
                    <div class="mb-2">
                        <strong>Payment Method:</strong> {{ ucfirst($confirmation->payment_method) }}
                    </div>
                    
                    @if($confirmation->status == 'pending')
                        <div style="color: #ffff99;">
                            ⏳ <strong>Status:</strong> Waiting for payment confirmation from admin
                        </div>
                    @elseif($confirmation->status == 'confirmed')
                        <div style="color: #99ff99;">
                            ✓ <strong>Status:</strong> Payment confirmed!
                        </div>
                    @elseif($confirmation->status == 'rejected')
                        <div style="color: #ff9999;">
                            ✗ <strong>Status:</strong> Payment rejected
                        </div>
                        @if($confirmation->notes)
                            <div style="color: #ffaaaa; margin-top: 8px;">
                                <strong>Reason:</strong> {{ $confirmation->notes }}
                            </div>
                        @endif
                    @endif
                </div>

                @if($confirmation->cashier)
                    <div class="confirmation-meta">
                        Handled by: <strong>{{ $confirmation->cashier->username }}</strong>
                    </div>
                @endif

                @if($confirmation->notes && $confirmation->status == 'confirmed')
                    <div class="mt-2 p-2" style="background-color: #2a4a2a; border-radius: 5px;">
                        <strong style="color: #99ff99;">Notes:</strong>
                        <div style="color: #c0e0c0;">{{ $confirmation->notes }}</div>
                    </div>
                @endif
            </div>
        @endforeach

        <!-- Pagination -->
        @if($confirmations->hasPages())
            <div class="mt-4">
                {{ $confirmations->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

@php
    echo view('footer');
@endphp
