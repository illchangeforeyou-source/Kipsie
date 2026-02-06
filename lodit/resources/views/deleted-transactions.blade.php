@php
// This view will be wrapped with header, menu, and footer
@endphp

<style>
    .report-container {
        background-color: #2b2b2b;
        border: 1px solid #444;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        padding: 30px;
        margin-top: 20px;
        margin-bottom: 30px;
    }

    .report-container h1 {
        border-bottom: 2px solid #555;
        padding-bottom: 10px;
        margin-bottom: 30px;
        color: #f0f0f0;
    }

    .warning-banner {
        background-color: rgba(217, 83, 79, 0.2);
        border-left: 4px solid #d9534f;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        color: #ffb3b3;
        font-size: 13px;
    }

    .deletion-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #2a2a2a;
        border: 1px solid #444;
        border-radius: 8px;
        overflow: hidden;
    }

    .deletion-table thead {
        background-color: #1e1e1e;
    }

    .deletion-table th {
        padding: 15px;
        text-align: left;
        color: #ccc;
        font-weight: 600;
        border-bottom: 2px solid #444;
        font-size: 12px;
        text-transform: uppercase;
    }

    .deletion-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #3a3a3a;
        color: #ddd;
        font-size: 13px;
    }

    .deletion-table tbody tr:hover {
        background-color: #3a3a3a;
    }

    .action-cell {
        display: flex;
        gap: 8px;
    }

    .btn-restore {
        background-color: #5cb85c;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
    }

    .btn-restore:hover {
        background-color: #4cae4c;
    }

    .btn-permanent {
        background-color: #d9534f;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
    }

    .btn-permanent:hover {
        background-color: #c9302c;
    }

    .action-restored {
        color: #90ee90;
        font-weight: 600;
    }

    .action-permanent {
        color: #ff6b6b;
        font-weight: 600;
    }

    .action-soft {
        color: #ffb347;
        font-weight: 600;
    }

    .no-deletions {
        text-align: center;
        padding: 40px;
        color: #888;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
    }

    .modal-content {
        background-color: #2b2b2b;
        margin: 10% auto;
        padding: 30px;
        border: 1px solid #444;
        border-radius: 15px;
        width: 90%;
        max-width: 700px;
        color: #ddd;
    }

    .modal-header {
        font-size: 18px;
        font-weight: bold;
        color: #f0f0f0;
        margin-bottom: 20px;
        border-bottom: 1px solid #555;
        padding-bottom: 15px;
    }

    .modal-body {
        margin-bottom: 20px;
        line-height: 1.8;
    }

    .modal-body p {
        margin: 10px 0;
        color: #ccc;
    }

    .modal-body strong {
        color: #f0f0f0;
    }

    .close {
        color: #999;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: #f0f0f0;
    }

    .modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-cancel {
        background-color: #5bc0de;
        color: #1e1e1e;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-confirm {
        background-color: #d9534f;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .data-viewer {
        background-color: #1e1e1e;
        border: 1px solid #444;
        border-radius: 6px;
        padding: 15px;
        max-height: 300px;
        overflow-y: auto;
        margin: 15px 0;
    }

    .data-viewer pre {
        color: #90ee90;
        font-size: 11px;
        margin: 0;
        white-space: pre-wrap;
        word-break: break-all;
    }
</style>

<div class="container" style="margin-top: 100px;">
    <div class="report-container">
        <h1>Deleted Transactions Log</h1>

        <div class="warning-banner">
            This log shows all deleted transactions. You can restore soft-deleted transactions or permanently delete them from this page.
        </div>

        @if($deletions->count() > 0)
            <div style="overflow-x: auto;">
                <table class="deletion-table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Deleted By</th>
                            <th>Deleted At</th>
                            <th>IP Address</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deletions as $deletion)
                            <tr>
                                <td>#{{ $deletion->transaction_id }}</td>
                                <td>{{ $deletion->deleted_by_username ?? 'System' }}</td>
                                <td>{{ \Carbon\Carbon::parse($deletion->deleted_at)->format('M d, Y H:i') }}</td>
                                <td><code style="background-color: #1e1e1e; padding: 2px 6px; border-radius: 3px;">{{ $deletion->ip_address }}</code></td>
                                <td>
                                    @if($deletion->action === 'soft_delete')
                                        <span class="action-soft">Soft Deleted</span>
                                    @elseif($deletion->action === 'restored')
                                        <span class="action-restored">Restored</span>
                                    @elseif($deletion->action === 'permanent_delete')
                                        <span class="action-permanent">Permanently Deleted</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-cell">
                                        @if($deletion->action === 'soft_delete')
                                            <button class="btn-restore" onclick="showDetails({{ $deletion->id }})">View Details</button>
                                            <button class="btn-restore" onclick="restoreTransaction({{ $deletion->id }})">Restore</button>
                                            <button class="btn-permanent" onclick="confirmPermanentDelete({{ $deletion->id }})">Delete</button>
                                        @else
                                            <span style="color: #999; font-size: 11px;">No Actions</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-deletions">
                <p>No deleted transactions found.</p>
            </div>
        @endif
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('detailsModal')">&times;</span>
        <div class="modal-header">Transaction Details</div>
        <div class="modal-body" id="detailsBody">
            <!-- Details will be populated here -->
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteModal')">&times;</span>
        <div class="modal-header">Permanently Delete Transaction?</div>
        <div class="modal-body">
            <p><strong>⚠️ WARNING:</strong> This action cannot be undone. The transaction will be completely removed from the system.</p>
            <p>Are you sure you want to permanently delete this transaction?</p>
        </div>
        <div class="modal-buttons">
            <button class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
            <button class="btn-confirm" id="confirmDeleteBtn">Permanently Delete</button>
        </div>
    </div>
</div>

<script>
    let currentDeletionId = null;

    function showDetails(deletionId) {
        const deletion = @json($deletions);
        const record = deletion.find(d => d.id === deletionId);
        
        if (!record) return;

        const transactionData = JSON.parse(record.transaction_data || '{}');
        
        let html = `
            <p><strong>Transaction ID:</strong> #${record.transaction_id}</p>
            <p><strong>Deleted By:</strong> ${record.deleted_by_username || 'System'}</p>
            <p><strong>Deleted At:</strong> ${new Date(record.deleted_at).toLocaleString()}</p>
            <p><strong>IP Address:</strong> <code>${record.ip_address || 'N/A'}</code></p>
            <p><strong>User Agent:</strong></p>
            <div class="data-viewer"><pre>${record.user_agent || 'N/A'}</pre></div>
            <p><strong>Original Transaction Data:</strong></p>
            <div class="data-viewer"><pre>${JSON.stringify(transactionData, null, 2)}</pre></div>
        `;
        
        document.getElementById('detailsBody').innerHTML = html;
        document.getElementById('detailsModal').style.display = 'block';
    }

    function confirmPermanentDelete(deletionId) {
        currentDeletionId = deletionId;
        document.getElementById('deleteModal').style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    window.onclick = function(event) {
        const detailsModal = document.getElementById('detailsModal');
        const deleteModal = document.getElementById('deleteModal');
        if (event.target === detailsModal) {
            detailsModal.style.display = 'none';
        }
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
        }
    }

    async function restoreTransaction(deletionId) {
        if (!confirm('Restore this transaction?')) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        try {
            const response = await fetch(`/transaction/${deletionId}/restore`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                credentials: 'same-origin'
            });

            const data = await response.json();
            if (data.success) {
                alert('Transaction restored successfully');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Error restoring transaction: ' + error.message);
        }
    }

    async function permanentlyDeleteTransaction() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        try {
            const response = await fetch(`/transaction/${currentDeletionId}/permanent-delete`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                credentials: 'same-origin'
            });

            const data = await response.json();
            if (data.success) {
                alert('Transaction permanently deleted');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    document.getElementById('confirmDeleteBtn')?.addEventListener('click', permanentlyDeleteTransaction);
</script>
