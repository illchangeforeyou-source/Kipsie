@extends('layouts.app')

@section('content')

<style>
    .catalog-box {
        background-color: var(--card-bg, #2b2b2b);
        border: 1px solid var(--border-color, #444);
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        padding: 30px;
        margin-top: 20px;
    }

    .catalog-box h1 {
        border-bottom: 2px solid var(--border-color, #555);
        padding-bottom: 10px;
        margin-bottom: 30px;
        color: var(--text-primary, #f0f0f0);
    }

    .controls-section {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
        align-items: center;
    }

    .controls-section input,
    .controls-section select {
        background-color: var(--input-bg, #1e1e1e);
        border: 1px solid var(--border-color, #444);
        border-radius: 6px;
        padding: 10px 12px;
        color: var(--text-primary, #fff);
        font-size: 13px;
        min-width: 150px;
    }

    .controls-section input:focus,
    .controls-section select:focus {
        border-color: var(--text-secondary, #666);
        outline: none;
        background-color: var(--input-focus-bg, #333);
    }

    .stock-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .stock-card {
        background-color: var(--card-secondary-bg, #2a2a2a);
        color: var(--text-primary, #fff);
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.4);
        padding: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stock-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.6);
    }

    .stock-card h5 {
        color: var(--text-primary, #f0f0f0);
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
    }

    .stock-card .category-label {
        color: var(--text-secondary, #999);
        font-size: 11px;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stock-info {
        margin-bottom: 12px;
    }

    .stock-info p {
        margin: 5px 0;
        color: var(--text-primary, #ccc);
        font-size: 13px;
    }

    .stock-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--text-primary, #5a9);
        margin: 10px 0;
    }

    .stock-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
    }

    .stock-critical {
        background-color: #d9534f;
        color: white;
    }

    .stock-warning {
        background-color: #f0ad4e;
        color: #000;
    }

    .stock-ok {
        background-color: #5cb85c;
        color: white;
    }

    .stock-controls {
        display: flex;
        gap: 8px;
        margin-top: 15px;
    }

    .stock-controls input {
        flex: 0 0 70px;
        padding: 8px;
        border: 1px solid var(--border-color, #444);
        border-radius: 6px;
        background-color: var(--input-bg, #1e1e1e);
        color: var(--text-primary, #fff);
        text-align: center;
        font-size: 12px;
        min-width: 0;
    }

    .stock-controls button {
        flex: 1;
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: 0.2s;
        color: #1e1e1e;
    }

    .btn-add-stock {
        background-color: #a6a6a6;
        border: none;
    }

    .btn-add-stock:hover {
        background-color: #c7c7c7;
    }

    .btn-set-stock {
        background-color: #a6a6a6;
        border: none;
    }

    .btn-set-stock:hover {
        background-color: #c7c7c7;
    }

    .alert {
        background-color: var(--card-secondary-bg, #2d2d2d);
        border: 1px solid var(--border-color, #444);
        border-radius: 8px;
        padding: 12px 20px;
        margin-bottom: 20px;
        color: var(--text-primary, #f5f5f5);
    }

    .alert-success {
        background-color: rgba(76, 175, 80, 0.2);
        border-color: #4caf50;
        color: #c8e6c9;
    }

    .alert-danger {
        background-color: rgba(244, 67, 54, 0.2);
        border-color: #f44336;
        color: #ffcdd2;
    }

    .no-results {
        text-align: center;
        color: var(--text-secondary, #999);
        padding: 40px 20px;
        font-size: 14px;
    }

    /* Light mode overrides */
    html.light-mode .stock-info p,
    html.light-mode .stock-price {
        color: var(--text-primary, #333) !important;
    }

    html.light-mode .stock-card {
        background-color: #ffffff;
    }

    html.light-mode .catalog-box {
        background-color: #ffffff;
    }
</style>

<div class="container" style="margin-top: 100px;">
    <div class="catalog-box">
        <h1>Stock Management</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="controls-section">
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Search medicine..."
                style="flex: 1; min-width: 200px;"
            >

            <select id="categoryFilter" style="min-width: 180px;">
                <option value="">All Categories</option>
            </select>

            <select id="sortSelect" style="min-width: 200px;">
                <option value="">Sort by</option>
                <option value="name-asc">Name A–Z</option>
                <option value="name-desc">Name Z–A</option>
                <option value="stock-asc">Stock (Low to High)</option>
                <option value="stock-desc">Stock (High to Low)</option>
                <option value="price-asc">Price (Low to High)</option>
                <option value="price-desc">Price (High to Low)</option>
            </select>
        </div>

        @if($medicines->count() > 0)
            <div class="stock-grid" id="stock-grid">
                @foreach($medicines as $medicine)
                    <div class="stock-card" data-medicine-id="{{ $medicine->id }}" data-stock="{{ $medicine->stock }}" data-name="{{ $medicine->name }}" data-price="{{ $medicine->price }}" data-category="{{ $medicine->category->name ?? 'Uncategorized' }}">
                        <h5>{{ $medicine->name }}</h5>
                        
                        @if($medicine->category)
                            <div class="category-label">{{ $medicine->category->name }}</div>
                        @endif
                        
                        <div class="stock-info">
                            <p><strong>Current Stock:</strong></p>
                            <p>
                                <span class="stock-badge {{ $medicine->stock < 25 ? 'stock-critical' : ($medicine->stock < 50 ? 'stock-warning' : 'stock-ok') }}">
                                    {{ $medicine->stock }} units
                                </span>
                            </p>
                        </div>

                        <div class="stock-price">
                            Rp{{ number_format($medicine->price, 0, ',', '.') }}
                        </div>

                        @if($medicine->description)
                            <div class="stock-info">
                                <p><strong>Description:</strong> {{ substr($medicine->description, 0, 50) }}{{ strlen($medicine->description) > 50 ? '...' : '' }}</p>
                            </div>
                        @endif

                        <div class="stock-controls">
                            <input type="number" class="qty-input" placeholder="Qty" min="1" value="1">
                            <button class="btn-add-stock" onclick="addStock({{ $medicine->id }})">Add Stock</button>
                            <button class="btn-set-stock" onclick="setStock({{ $medicine->id }})">Set Stock</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-results">No medicines found.</div>
        @endif
    </div>
</div>

<script>
    let allMedicines = [];

    document.addEventListener('DOMContentLoaded', function() {
        // Load all medicine data from cards
        const cards = document.querySelectorAll('.stock-card');
        cards.forEach(card => {
            allMedicines.push({
                id: card.dataset.medicineId,
                name: card.dataset.name,
                stock: parseInt(card.dataset.stock),
                price: parseFloat(card.dataset.price),
                category: card.dataset.category,
                element: card
            });
        });

        // Load categories for filter
        const categories = new Set(allMedicines.map(m => m.category));
        const categoryFilter = document.getElementById('categoryFilter');
        categories.forEach(cat => {
            if (cat) {
                const option = document.createElement('option');
                option.value = cat;
                option.textContent = cat;
                categoryFilter.appendChild(option);
            }
        });

        // Add event listeners
        document.getElementById('searchInput').addEventListener('input', filterAndSort);
        document.getElementById('categoryFilter').addEventListener('change', filterAndSort);
        document.getElementById('sortSelect').addEventListener('change', filterAndSort);
    });

    function filterAndSort() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        const sortValue = document.getElementById('sortSelect').value;

        let filtered = allMedicines.filter(m => {
            const matchesSearch = m.name.toLowerCase().includes(searchTerm);
            const matchesCategory = !categoryFilter || m.category === categoryFilter;
            return matchesSearch && matchesCategory;
        });

        // Sort
        if (sortValue) {
            filtered.sort((a, b) => {
                switch(sortValue) {
                    case 'name-asc': return a.name.localeCompare(b.name);
                    case 'name-desc': return b.name.localeCompare(a.name);
                    case 'stock-asc': return a.stock - b.stock;
                    case 'stock-desc': return b.stock - a.stock;
                    case 'price-asc': return a.price - b.price;
                    case 'price-desc': return b.price - a.price;
                    default: return 0;
                }
            });
        }

        // Show/hide cards
        const grid = document.getElementById('stock-grid');
        allMedicines.forEach(m => {
            m.element.style.display = filtered.some(f => f.id === m.id) ? 'block' : 'none';
        });

        // Reorder grid
        filtered.forEach(m => {
            grid.appendChild(m.element);
        });

        // Show no results message
        const hasVisibleCards = filtered.length > 0;
        let noResultsMsg = document.querySelector('.no-results');
        if (!hasVisibleCards) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results';
                noResultsMsg.textContent = 'No medicines found.';
                grid.after(noResultsMsg);
            }
            noResultsMsg.style.display = 'block';
        } else if (noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }
    }

    async function addStock(medicineId) {
        const qtyInput = event.target.parentElement.querySelector('.qty-input');
        const qty = qtyInput.value;
        if (!qty || qty < 1) return alert('Please enter a valid quantity');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            const response = await fetch('/stock/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ medicine_id: medicineId, quantity: parseInt(qty), action: 'add' }),
                credentials: 'same-origin'
            });

            const data = await response.json();
            if (data.success) {
                alert('Stock added successfully. New stock: ' + data.new_stock);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Error updating stock: ' + error.message);
        }
    }

    async function setStock(medicineId) {
        const qtyInput = event.target.parentElement.querySelector('.qty-input');
        const qty = qtyInput.value;
        if (!qty || qty < 0) return alert('Please enter a valid quantity');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            const response = await fetch('/stock/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ medicine_id: medicineId, quantity: parseInt(qty), action: 'set' }),
                credentials: 'same-origin'
            });

            const data = await response.json();
            if (data.success) {
                alert('Stock set to ' + qty + ' units');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Error updating stock: ' + error.message);
        }
    }
</script>
@endsection
