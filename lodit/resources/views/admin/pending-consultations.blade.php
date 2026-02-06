@extends('layouts.app')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')

<style>
    .container-fluid {
        margin-top: 120px;
        padding-bottom: 40px;
    }

    .page-header {
        background-color: #2a2a2a;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid #444;
    }

    .page-header h1 {
        color: #f0f0f0;
        border-bottom: 2px solid #555;
        padding-bottom: 15px;
        margin-bottom: 10px;
    }

    .page-header p {
        color: #b0b0b0;
        margin: 0;
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

    .card-title {
        color: #f0f0f0;
    }

    .card-text {
        color: #d0d0d0;
    }

    .btn-primary {
        background-color: #1e3a8a;
        border: none;
        color: #ffffff;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #1e40af;
        color: #ffffff;
    }

    .btn-success {
        background-color: #16a34a;
        border: none;
        color: #ffffff;
        font-weight: 600;
    }

    .btn-success:hover {
        background-color: #15803d;
        color: #ffffff;
    }

    .btn-secondary {
        background-color: #555;
        border: none;
        color: #f5f5f5;
    }

    .btn-secondary:hover {
        background-color: #666;
        color: #f5f5f5;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .badge {
        padding: 0.4em 0.6em;
        font-size: 0.85em;
    }

    .status-pending {
        background-color: #5a5a2a;
        color: #ffff99;
    }

    .status-answered {
        background-color: #2a5a2a;
        color: #99ff99;
    }

    .question-card {
        background-color: #3a3a3a;
        border-left: 4px solid #b0b0b0;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .question-text {
        color: #f0f0f0;
        font-size: 1rem;
        margin-bottom: 10px;
    }

    .user-info {
        color: #999;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .form-label {
        color: #e0e0e0;
        font-weight: 500;
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
        box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.25);
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

    .modal-title {
        color: #f0f0f0;
        font-weight: 600;
    }

    .alert {
        background-color: #2a2a2a;
        border-color: #444;
        color: #f5f5f5;
    }

    .alert-success {
        background-color: #1a3a2a;
        border-color: #3a6a5a;
        color: #99ff99;
    }

    .alert-info {
        background-color: #1a3a4a;
        border-color: #3a6a7a;
        color: #99ddff;
    }

    .alert-dismissible .btn-close-white {
        filter: brightness(0) invert(1);
    }

    .text-muted {
        color: #b0b0b0 !important;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>❓ Pending Consultations</h1>
            <p class="text-muted">Review and respond to customer questions</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($consultations->count() == 0)
        <div class="alert alert-info">
            ✅ No pending consultations! All questions have been answered.
        </div>
    @else
        <p class="mb-4"><strong>{{ $consultations->total() }}</strong> pending question(s) to answer</p>
    @endif

    <!-- Consultations List -->
    @forelse($consultations as $consultation)
        <div class="question-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h5 class="card-title mb-1">
                        @if($consultation->medicine)
                            💊 {{ $consultation->medicine->name }}
                        @else
                            ❓ General Question
                        @endif
                    </h5>
                    <div class="user-info">
                        👤 <strong>{{ $consultation->user->username ?? 'Unknown' }}</strong> 
                        • {{ $consultation->created_at->diffForHumans() }}
                    </div>
                </div>
                <span class="badge status-{{ strtolower($consultation->status) }}">{{ ucfirst($consultation->status) }}</span>
            </div>

            <div class="question-text">
                <strong>Question:</strong><br>
                {{ $consultation->question }}
            </div>

            @if($consultation->response)
                <div class="mt-3 p-3" style="background-color: #2a4a2a; border-radius: 5px;">
                    <strong style="color: #99ff99;">✓ Response:</strong><br>
                    <span style="color: #c0e0c0;">{{ $consultation->response }}</span><br>
                    <small style="color: #888;">Answered by {{ $consultation->consultant->username ?? 'Admin' }} on {{ $consultation->answered_at->format('d M Y H:i') }}</small>
                </div>
            @endif

            <div class="mt-3 d-flex justify-content-end">
                <button class="btn btn-primary btn-sm" onclick="openResponseModal({{ $consultation->id }}, '{{ addslashes($consultation->question) }}')">
                    💬 Respond
                </button>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            No pending consultations at the moment.
        </div>
    @endforelse

    <!-- Pagination -->
    @if($consultations->hasPages())
        <div class="mt-4">
            {{ $consultations->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Respond to Consultation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Original Question</label>
                    <div id="questionDisplay" class="form-control" style="background-color: #1e1e1e; color: #d0d0d0; height: auto; min-height: 80px;"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Your Response</label>
                    <textarea class="form-control" id="responseText" rows="6" placeholder="Provide helpful advice..."></textarea>
                    <small class="text-muted">Be professional and helpful</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitResponse()">Send Response</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentConsultationId = null;

    function openResponseModal(id, question) {
        currentConsultationId = id;
        document.getElementById('questionDisplay').textContent = question;
        document.getElementById('responseText').value = '';
        const modal = new bootstrap.Modal(document.getElementById('responseModal'));
        modal.show();
    }

    async function submitResponse() {
        console.log('📝 Submitting response for consultation', currentConsultationId);
        const response = document.getElementById('responseText').value.trim();

        if (!response) {
            alert('Please enter a response');
            return;
        }

        if (response.length < 5) {
            alert('Response must be at least 5 characters');
            return;
        }

        const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenEl) {
            alert('Error: CSRF token not found. Please refresh the page.');
            return;
        }

        const csrfToken = csrfTokenEl.getAttribute('content');
        console.log('Response text:', response);
        console.log('CSRF token found:', csrfToken.substring(0, 10) + '...');

        try {
            console.log('Calling endpoint: /admin/consultation/' + currentConsultationId + '/respond');
            const apiResponse = await fetch(`/admin/consultation/${currentConsultationId}/respond`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ response: response })
            });

            console.log('Response status:', apiResponse.status);
            const data = await apiResponse.json();
            console.log('Response data:', data);
            
            if (data.success) {
                alert(data.message);
                document.getElementById('responseText').value = '';
                const modal = bootstrap.Modal.getInstance(document.getElementById('responseModal'));
                if (modal) modal.hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error submitting response:', error);
            alert('Error submitting response: ' + error.message);
        }
    }
</script>

@endsection
