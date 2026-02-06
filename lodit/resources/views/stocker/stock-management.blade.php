@extends('layouts.app')

@section('content')
<style>
    .stock-container {
        background-color: var(--card-bg, #2a2a2a);
        border: 1px solid var(--border-color, #444);
        border-radius: 10px;
        padding: 30px;
        margin-top: 30px;
    }

    .page-header h1 {
        color: var(--text-primary, #f0f0f0);
        border-bottom: 2px solid var(--border-color, #555);
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .page-header p {
        color: var(--text-secondary, #999);
    }

    .medicine-card {
        background-color: var(--card-secondary-bg, #3a3a3a);
        border-left: 4px solid #6ba;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .medicine-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .medicine-name {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--text-primary, #f0f0f0);
    }

    .medicine-header small {
        color: var(--text-secondary, #999);
    }

    .stock-display {
        display: flex;
        gap: 20px;
        margin: 15px 0;
        font-size: 1.1rem;
    }

    .stock-info {
        background-color: var(--input-bg, #2a2a2a);
        padding: 10px 15px;
        border-radius: 5px;
    }

    .stock-label {
        color: var(--text-secondary, #999);
        font-size: 0.9rem;
    }

    .stock-value {
        color: var(--text-primary, #5a9);
        font-weight: bold;
        font-size: 1.3rem;
    }

    .medicine-price {
        font-size: 1.4rem;
        font-weight: bold;
        color: var(--text-primary, #5a9);
        margin: 15px 0;
        padding: 10px 0;
        border-top: 1px solid var(--border-color, #555);
        border-bottom: 1px solid var(--border-color, #555);
    }

    .stock-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-add {
        background-color: #5a9;
        color: white;
    }

    .btn-add:hover {
        background-color: #6ba;
    }

    .btn-set {
        background-color: #a86;
        color: white;
    }

    .btn-set:hover {
        background-color: #b97;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        color: var(--text-primary, #e0e0e0);
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        background-color: var(--input-bg, #2f2f2f);
        border: 1px solid var(--border-color, #555);
        border-radius: 5px;
        color: var(--text-primary, #f5f5f5);
        font-size: 1rem;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--text-secondary, #777);
        background-color: var(--input-focus-bg, #333);
    }

    .search-box {
        margin-bottom: 25px;
    }

    .search-box input {
        width: 300px;
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

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: var(--card-bg, #2a2a2a);
        padding: 30px;
        border-radius: 10px;
        border: 1px solid var(--border-color, #444);
        width: 90%;
        max-width: 500px;
    }

    .modal-header {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--text-primary, #f0f0f0);
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border-color, #555);
        padding-bottom: 15px;
    }

    .modal-footer {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .btn-cancel {
        background-color: #555;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #666;
    }

    .btn-submit {
        background-color: #5a9;
        color: white;
    }

    .btn-submit:hover {
        background-color: #6ba;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        border-left: 4px solid;
    }

    .alert-success {
        background-color: #1a3a2a;
        border-color: #5a9;
        color: #5a9;
    }

    .alert-error {
        background-color: #3a1a1a;
        border-color: #a54;
        color: #a54;
    }

    /* Light mode overrides */
    html.light-mode .stock-container {
        background-color: #ffffff;
    }

    html.light-mode .medicine-card {
        background-color: #f5f5f5;
        border-left-color: #5a9;
    }

    html.light-mode .stock-info {
        background-color: #eeeeee;
    }

    html.light-mode .medicine-name,
    html.light-mode .stock-value,
    html.light-mode .medicine-price,
    html.light-mode .stock-label,
    html.light-mode .form-label,
    html.light-mode .modal-header {
        color: #1a1a1a !important;
    }

    html.light-mode .form-control {
        background-color: #ffffff;
        color: #1a1a1a;
        border-color: #ddd;
    }

    html.light-mode .modal-content {
        background-color: #ffffff;
        border-color: #ddd;
    }
</style>

<div class="container-lg stock-container">
    <div class="page-header">
        <h1>📦 Stock Management</h1>
        <p class="text-muted">Add or adjust medicine stock levels</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Search -->
    <div class="search-box">
        <input type="text" id="searchInput" class="form-control" placeholder="Search medicine by name...">
    </div>

    <!-- Medicines List -->
    <div id="medicinesList">
        @forelse($medicines as $medicine)
            <div class="medicine-card" data-medicine-name="{{ strtolower($medicine->name) }}">
                <div class="medicine-header">
                    <div>
                        <div class="medicine-name">{{ $medicine->name }}</div>
                        <small class="text-muted">ID: {{ $medicine->id }} | Price: Rp{{ number_format($medicine->price, 0, ',', '.') }}</small>
                    </div>
                </div>

                <div class="stock-display">
                    <div class="stock-info">
                        <div class="stock-label">Current Stock</div>
                        <div class="stock-value">{{ $medicine->stock }}</div>
                    </div>
                </div>

                <div class="medicine-price">
                    💰 {{ number_format($medicine->price, 0, ',', '.') }}.000
                </div>

                <div class="stock-actions">
                    <button class="btn btn-add" onclick="openAddModal({{ $medicine->id }}, '{{ $medicine->name }}')">
                        ➕ Add Stock
                    </button>
                    <button class="btn btn-set" onclick="openSetModal({{ $medicine->id }}, '{{ $medicine->name }}', {{ $medicine->stock }})">
                        ⚙️ Set Stock
                    </button>
                </div>
            </div>
        @empty
            <div class="alert alert-error">
                No medicines found.
            </div>
        @endforelse
    </div>
</div>

<!-- Add Stock Modal -->
<div id="addStockModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            ➕ Add Stock to <span id="addMedicineName"></span>
        </div>
        <div>
            <div class="form-group">
                <label class="form-label">Quantity to Add</label>
                <input type="number" id="addQuantity" class="form-control" min="1" placeholder="Enter quantity...">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeAddModal()">Cancel</button>
            <button class="btn btn-submit" onclick="submitAddStock()">Add Stock</button>
        </div>
    </div>
</div>

<!-- Set Stock Modal -->
<div id="setStockModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            ⚙️ Set Stock for <span id="setMedicineName"></span>
        </div>
        <div>
            <div class="form-group">
                <label class="form-label">Current Stock: <span id="currentStockDisplay">0</span></label>
            </div>
            <div class="form-group">
                <label class="form-label">New Stock Value</label>
                <input type="number" id="setQuantity" class="form-control" min="0" placeholder="Enter new stock value...">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeSetModal()">Cancel</button>
            <button class="btn btn-submit" onclick="submitSetStock()">Update Stock</button>
        </div>
    </div>
</div>

<script>
    let currentMedicineId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.medicine-card').forEach(card => {
            const medicineName = card.getAttribute('data-medicine-name');
            if (medicineName.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Add Stock Modal
    function openAddModal(medicineId, medicineName) {
        currentMedicineId = medicineId;
        document.getElementById('addMedicineName').textContent = medicineName;
        document.getElementById('addQuantity').value = '';
        document.getElementById('addStockModal').classList.add('show');
    }

    function closeAddModal() {
        document.getElementById('addStockModal').classList.remove('show');
    }

    async function submitAddStock() {
        const quantity = parseInt(document.getElementById('addQuantity').value);

        if (!quantity || quantity < 1) {
            alert('Please enter a valid quantity');
            return;
        }

        try {
            const response = await fetch('/stocker/stock/add', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    medicine_id: currentMedicineId,
                    quantity: quantity
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
            alert('Error adding stock');
        }
    }

    // Set Stock Modal
    function openSetModal(medicineId, medicineName, currentStock) {
        currentMedicineId = medicineId;
        document.getElementById('setMedicineName').textContent = medicineName;
        document.getElementById('currentStockDisplay').textContent = currentStock;
        document.getElementById('setQuantity').value = currentStock;
        document.getElementById('setStockModal').classList.add('show');
    }

    function closeSetModal() {
        document.getElementById('setStockModal').classList.remove('show');
    }

    async function submitSetStock() {
        const quantity = parseInt(document.getElementById('setQuantity').value);

        if (quantity === null || quantity < 0) {
            alert('Please enter a valid stock value');
            return;
        }

        try {
            const response = await fetch('/stocker/stock/set', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    medicine_id: currentMedicineId,
                    quantity: quantity
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
            alert('Error setting stock');
        }
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const addModal = document.getElementById('addStockModal');
        const setModal = document.getElementById('setStockModal');
        if (event.target === addModal) closeAddModal();
        if (event.target === setModal) closeSetModal();
    });
</script>
@endsection
