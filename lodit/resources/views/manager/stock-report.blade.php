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

    .stat-box {
        background-color: #3a3a3a;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #b0b0b0;
        margin-bottom: 10px;
    }

    .stat-label {
        color: #999;
        font-size: 0.85rem;
    }

    .stat-value {
        color: #b0b0b0;
        font-size: 1.5rem;
        font-weight: bold;
        margin-top: 5px;
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

    .stock-bar {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .progress {
        flex: 1;
        height: 20px;
        background-color: #1e1e1e;
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background-color: #5a9;
        transition: width 0.3s;
    }

    .progress-bar.warning {
        background-color: #a85;
    }

    .progress-bar.danger {
        background-color: #a54;
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
</style>

<div class="container-fluid">
    <div class="row mb-4 page-header">
        <div class="col-md-8">
            <h1>📦 Stock Report</h1>
            <p class="text-muted">Medicine inventory and stock management</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('manager.dashboard') }}" class="btn btn-secondary me-2">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-label">Total Medicines</div>
                <div class="stat-value">{{ $totalMedicines }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-label">Low Stock (< 10)</div>
                <div class="stat-value" style="color: #ffc99a;">{{ $lowStockCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-label">Out of Stock</div>
                <div class="stat-value" style="color: #ff9999;">{{ $outOfStockCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-label">Total Stock Value</div>
                <div class="stat-value" style="color: #99ff99;">Rp{{ number_format($totalValue, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('manager.stock-report') }}" class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Search Medicine</label>
                <input type="text" name="search" class="form-control" placeholder="Medicine name..." value="{{ $searchTerm }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Filter by Stock</label>
                <select name="filter_stock" class="form-select">
                    <option value="">All</option>
                    <option value="high" {{ $filterStock == 'high' ? 'selected' : '' }}>High Stock (50+)</option>
                    <option value="low" {{ $filterStock == 'low' ? 'selected' : '' }}>Low Stock (1-9)</option>
                    <option value="out" {{ $filterStock == 'out' ? 'selected' : '' }}>Out of Stock (0)</option>
                </select>
            </div>

            <div class="col-md-4" style="display: flex; gap: 10px; align-items: flex-end;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Filter</button>
                <a href="{{ route('manager.stock-report') }}" class="btn btn-secondary" style="flex: 1;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Stock Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Medicine Inventory</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="20%">Medicine Name</th>
                        <th width="12%">Price</th>
                        <th width="15%">Stock Level</th>
                        <th width="35%">Stock Status</th>
                        <th width="18%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $medicine)
                        <tr>
                            <td>
                                <strong>{{ $medicine->name }}</strong>
                            </td>
                            <td>Rp{{ number_format($medicine->price, 0, ',', '.') }}</td>
                            <td>
                                <strong>{{ $medicine->stock }} unit(s)</strong>
                            </td>
                            <td>
                                <div class="stock-bar">
                                    <div class="progress">
                                        @php
                                            $percentage = min(100, max(0, ($medicine->stock / 50) * 100));
                                            $barClass = 'progress-bar';
                                            if ($medicine->stock == 0) {
                                                $barClass .= ' danger';
                                            } elseif ($medicine->stock < 10) {
                                                $barClass .= ' warning';
                                            }
                                        @endphp
                                        <div class="{{ $barClass }}" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    @if($medicine->stock == 0)
                                        <span class="badge" style="background-color: #5a2a2a; color: #ff9999;">OUT</span>
                                    @elseif($medicine->stock < 10)
                                        <span class="badge" style="background-color: #5a4a2a; color: #ffc99a;">LOW</span>
                                    @else
                                        <span class="badge" style="background-color: #2a5a2a; color: #99ff99;">OK</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editStock({{ $medicine->id }}, {{ $medicine->stock }}, '{{ $medicine->name }}')">
                                    ✎ Edit Stock
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No medicines found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $medicines->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Edit Stock Modal -->
<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="medicineName" style="color: #b0b0b0; font-weight: bold; margin-bottom: 15px;"></div>
                <div class="mb-3">
                    <label class="form-label">New Stock Level</label>
                    <input type="number" id="newStock" class="form-control" min="0" placeholder="Enter new stock quantity">
                    <small class="text-muted">Current: <span id="currentStock">0</span> units</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStockUpdate()">Update Stock</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentMedicineId = null;

    function editStock(id, currentStock, name) {
        currentMedicineId = id;
        document.getElementById('medicineName').textContent = name;
        document.getElementById('currentStock').textContent = currentStock;
        document.getElementById('newStock').value = currentStock;
        
        const modal = new bootstrap.Modal(document.getElementById('editStockModal'));
        modal.show();
    }

    async function submitStockUpdate() {
        const newStock = document.getElementById('newStock').value;

        if (newStock === '' || newStock < 0) {
            alert('Please enter a valid stock quantity');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/manager/medicine/${currentMedicineId}/update-stock`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    stock: parseInt(newStock)
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
            alert('Error updating stock');
        }
    }
</script>

@php
    echo view('footer');
@endphp
