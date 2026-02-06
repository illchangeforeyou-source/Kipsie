@extends('layouts.app')

@section('content')
<style>
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
        margin-bottom: 20px;
    }

    .prescription-card {
        background-color: #3a3a3a;
        border-left: 4px solid #b0b0b0;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .prescription-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }

    .prescription-title {
        color: #f0f0f0;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .prescription-meta {
        color: #999;
        font-size: 0.9rem;
    }

    .medicine-name {
        color: #b0b0b0;
        font-weight: bold;
        margin: 10px 0;
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

    .alert-info {
        background-color: #1a3a4a;
        border-color: #3a6a7a;
    }

    .prescription-file {
        background-color: #2a2a2a;
        padding: 10px;
        border-radius: 5px;
        margin: 10px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .file-icon {
        font-size: 1.5rem;
        margin-right: 10px;
    }
</style>

<div class="container-lg mt-5">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>💊 Pending Prescription Validations</h1>
            <p class="text-muted">Review and validate customer prescriptions for restricted medicines</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-section">
        <form method="GET" action="{{ route('prescription.pending') }}" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search by customer or medicine..." 
                value="{{ $searchTerm }}" style="max-width: 300px;">
            <button type="submit" class="btn btn-primary">🔍 Search</button>
            <a href="{{ route('prescription.pending') }}" class="btn btn-secondary">Clear</a>
        </form>
    </div>

    @if($prescriptions->count() == 0)
        <div class="alert alert-info">
            ✅ No pending prescriptions! All prescriptions have been validated.
        </div>
    @else
        <p class="mb-4"><strong>{{ $prescriptions->total() }}</strong> pending prescription(s) to validate</p>
    @endif

    @forelse($prescriptions as $prescription)
        <div class="prescription-card">
            <div class="prescription-header">
                <div>
                    <div class="prescription-title">
                        👤 {{ $prescription->user->username ?? 'Unknown' }}
                    </div>
                    <div class="prescription-meta">
                        Submitted {{ $prescription->created_at->diffForHumans() }}
                    </div>
                    <div class="medicine-name">
                        💊 {{ $prescription->medicine->name ?? 'Unknown Medicine' }}
                    </div>
                </div>
                <span class="badge" style="background-color: #5a5a2a; color: #ffff99;">⏳ Pending</span>
            </div>

            <!-- Prescription File -->
            <div class="prescription-file">
                <div>
                    <span class="file-icon">📄</span>
                    <strong>Prescription Document</strong>
                </div>
                <button class="btn btn-sm btn-primary" onclick="viewPrescription('{{ $prescription->file_path }}')">
                    View File
                </button>
            </div>

            <!-- Order Info -->
            <div style="background-color: #2a2a2a; padding: 10px; border-radius: 5px; margin: 10px 0; font-size: 0.9rem;">
                <strong>Order #{{ $prescription->order->id }}</strong> | 
                Amount: Rp{{ number_format($prescription->order->total, 0, ',', '.') }}
            </div>

            <!-- Actions -->
            <div class="mt-3">
                <button class="btn btn-success btn-sm me-2" onclick="approvePrescription({{ $prescription->id }})">
                    ✓ Approve
                </button>
                <button class="btn btn-danger btn-sm" onclick="rejectPrescription({{ $prescription->id }})">
                    ✗ Reject
                </button>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            No pending prescriptions at the moment.
        </div>
    @endforelse

    @if($prescriptions->hasPages())
        <div class="mt-4">
            {{ $prescriptions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approvePrescriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Prescription</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="approveNotes" rows="3" placeholder="Add any notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitApprove()">Approve</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectPrescriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Prescription</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Reason for Rejection</label>
                    <textarea class="form-control" id="rejectNotes" rows="3" placeholder="Explain why you're rejecting this prescription..."></textarea>
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
    let currentPrescriptionId = null;

    function viewPrescription(filePath) {
        window.open(`/storage/${filePath}`, '_blank');
    }

    function approvePrescription(id) {
        currentPrescriptionId = id;
        const modal = new bootstrap.Modal(document.getElementById('approvePrescriptionModal'));
        modal.show();
    }

    function rejectPrescription(id) {
        currentPrescriptionId = id;
        const modal = new bootstrap.Modal(document.getElementById('rejectPrescriptionModal'));
        modal.show();
    }

    async function submitApprove() {
        const notes = document.getElementById('approveNotes').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/prescription/${currentPrescriptionId}/approve`, {
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
            alert('Error approving prescription');
        }
    }

    async function submitReject() {
        const notes = document.getElementById('rejectNotes').value;
        
        if (!notes.trim()) {
            alert('Please provide a reason for rejection');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/prescription/${currentPrescriptionId}/reject`, {
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
            alert('Error rejecting prescription');
        }
    }
</script>
@endsection
