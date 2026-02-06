@extends('layouts.app')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')

<style>
    .container {
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
        margin-bottom: 5px;
        font-size: 2rem;
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

    .btn-primary {
        background-color: #1e3a8a;
        border: none;
        color: #ffffff;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #1e40af;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
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

    .btn-danger {
        background-color: #dc2626;
        border: none;
        color: #ffffff;
    }

    .btn-danger:hover {
        background-color: #991b1b;
        color: #ffffff;
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

    .status-closed {
        background-color: #4a4a4a;
        color: #cccccc;
    }

    .question-item {
        background-color: #3a3a3a;
        border-left: 4px solid #b0b0b0;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .question-title {
        color: #f0f0f0;
        font-size: 1rem;
        margin-bottom: 8px;
    }

    .question-meta {
        color: #999;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .question-text {
        color: #d0d0d0;
        margin: 10px 0;
    }

    .response-box {
        background-color: #2a4a2a;
        border-left: 4px solid #99ff99;
        padding: 12px;
        border-radius: 5px;
        margin-top: 10px;
    }

    .response-text {
        color: #c0e0c0;
        margin-bottom: 5px;
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

    .form-label {
        color: #e0e0e0;
        font-weight: 500;
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
        background-color: #1e3a8a;
        border-color: #1e3a8a;
        color: #ffffff;
    }

    .text-muted {
        color: #b0b0b0 !important;
    }

    .text-end {
        text-align: right;
    }
</style>

<div class="container">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>❓ My Consultations</h1>
            <p class="text-muted">Ask questions to pharmacists and doctors before buying medicines</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#askQuestionModal">
                ➕ Ask a Question
            </button>
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
            <strong>No consultations yet.</strong> Click the button above to ask a question!
        </div>
    @else
        <!-- Consultations List -->
        @foreach($consultations as $consultation)
            <div class="question-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h5 class="question-title mb-1">
                            @if($consultation->medicine)
                                💊 {{ $consultation->medicine->name }}
                            @else
                                ❓ General Question
                            @endif
                        </h5>
                        <div class="question-meta">
                            Asked by <strong>{{ $consultation->user->username ?? 'Unknown' }}</strong> {{ $consultation->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <span class="badge status-{{ strtolower($consultation->status) }}">{{ ucfirst($consultation->status) }}</span>
                </div>

                <div class="question-text">
                    <strong>Your Question:</strong><br>
                    {{ $consultation->question }}
                </div>

                @if($consultation->response)
                    <div class="response-box">
                        <div class="response-text">
                            <strong>✓ Response from {{ $consultation->consultant->username ?? 'Doctor/Pharmacist' }}:</strong>
                        </div>
                        <div style="color: #b0e0b0;">{{ $consultation->response }}</div>
                        <small style="color: #888;">Answered {{ $consultation->answered_at->diffForHumans() }}</small>
                    </div>
                @endif

                <div class="mt-3 d-flex justify-content-end">
                    @if($consultation->status == 'pending')
                        <small class="text-muted" style="flex-grow: 1;">⏳ Waiting for a response from our team...</small>
                        <button class="btn btn-primary btn-sm" onclick="openResponseModal({{ $consultation->id }}, '{{ addslashes($consultation->question) }}')">
                            💬 Add Response
                        </button>
                    @elseif($consultation->status == 'answered')
                        <button class="btn btn-secondary btn-sm" onclick="closeConsultation({{ $consultation->id }})">
                            Close Question
                        </button>
                    @else
                        <small class="text-muted">✓ Closed</small>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        @if($consultations->hasPages())
            <div class="mt-4">
                {{ $consultations->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

<!-- Ask Question Modal -->
<div class="modal fade" id="askQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ask a Question</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Medicine (Optional)</label>
                    <select class="form-select" id="medicineSelect">
                        <option value="">-- Select a medicine or leave empty for general question --</option>
                        <!-- Populated via JavaScript -->
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Your Question</label>
                    <textarea class="form-control" id="questionText" rows="5" placeholder="Ask your question here. Be specific about your health concern or medicine question..."></textarea>
                    <small class="text-muted">Max 1000 characters</small>
                </div>

                <div class="alert alert-info">
                    <strong>💡 Tip:</strong> Our pharmacists and doctors will review your question and provide helpful advice to help you make informed decisions about your medicine.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitQuestion()">Send Question</button>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Response</h5>
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
    // Load medicines on page load
    document.addEventListener('DOMContentLoaded', async function() {
        console.log('🔧 Consultation page loaded, fetching medicines...');
        try {
            const response = await fetch('/api/medicines-list');
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            const medicines = await response.json();
            console.log('✓ Medicines loaded:', medicines);
            const select = document.getElementById('medicineSelect');
            
            medicines.forEach(medicine => {
                const option = document.createElement('option');
                option.value = medicine.id;
                option.textContent = medicine.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('⚠️ Error loading medicines:', error);
            // Medicines optional - form still works without selection
        }
    });

    async function submitQuestion() {
        console.log('📝 Submitting question...');
        const question = document.getElementById('questionText').value.trim();
        const medicineId = document.getElementById('medicineSelect').value || null;
        const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
        
        if (!csrfTokenEl) {
            alert('Error: CSRF token not found. Please refresh the page.');
            return;
        }
        
        const csrfToken = csrfTokenEl.getAttribute('content');
        console.log('Question:', question, 'Medicine ID:', medicineId);

        if (!question || question.length < 5) {
            alert('Please enter a question (at least 5 characters)');
            return;
        }

        if (question.length > 1000) {
            alert('Question must be less than 1000 characters');
            return;
        }

        try {
            console.log('Sending to /consultation/ask with CSRF:', csrfToken.substring(0, 10) + '...');
            const response = await fetch('/consultation/ask', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    question: question,
                    medicine_id: medicineId
                })
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                alert(data.message);
                document.getElementById('questionText').value = '';
                const modal = bootstrap.Modal.getInstance(document.getElementById('askQuestionModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error submitting question: ' + error.message);
        }
    }

    async function closeConsultation(id) {
        if (!confirm('Are you sure you want to close this consultation?')) return;

        const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenEl) {
            alert('Error: CSRF token not found.');
            return;
        }
        const csrfToken = csrfTokenEl.getAttribute('content');

        try {
            console.log('Closing consultation', id);
            const response = await fetch(`/consultation/${id}/close`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error closing consultation: ' + error.message);
        }
    }

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
