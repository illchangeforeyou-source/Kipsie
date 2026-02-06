@extends('layouts.app')

@section('title', 'LODIT - Reports Dashboard')

@section('head')
<style>
    .tab-content > .tab-pane { display: none; }
    .tab-content > .active { display: block; }
    
    /* Dark mode text colors */
    html.dark-mode .text-info,
    html.dark-mode .text-primary {
        color: #9d9dff !important;
    }
    
    html.dark-mode a {
        color: #9d9dff;
    }
    
    html.dark-mode a:hover {
        color: #c0c0ff;
    }
    
    html.dark-mode .card {
        background-color: #1a1a1a;
        border-color: #2a2a2a;
        color: #f5f5f5;
    }
    
    html.dark-mode .card-header {
        background-color: #0d0d0d;
        border-color: #2a2a2a;
        color: #f5f5f5;
    }
    
    html.dark-mode .table {
        background-color: #1a1a1a;
        color: #f5f5f5;
        border-color: #2a2a2a;
    }
    
    html.dark-mode .table thead th {
        background-color: #0d0d0d;
        border-color: #2a2a2a;
        color: #f5f5f5;
    }
    
    html.dark-mode .list-group-item {
        background-color: #1a1a1a;
        border-color: #2a2a2a;
        color: #f5f5f5;
    }
    
    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 30px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
@endsection

@section('content')

<br><br>
<!-- =======================halaman======================= -->
<div class="container-fluid mt-4">
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Reports Dashboard</h1>
</div>

<div class="row mb-4">
<div class="col-md-4 mb-3">
<div class="card text-white bg-primary">
<div class="card-body">
<div class="d-flex justify-content-between">
<div>
    <h5 class="card-title">Total Transactions</h5>
    <p class="card-text fs-2" id="totalTransactions">0</p>
</div>
    <i class="bi bi-graph-up-arrow" style="font-size: 2.5rem;"></i>
</div>
</div>
</div>
</div>

<div class="col-md-4 mb-3">
<div class="card text-white bg-info">
<div class="card-body">
<div class="d-flex justify-content-between">
<div>
    <h5 class="card-title">Total Income</h5>
    <p class="card-text fs-2" id="totalIncome">$0</p>
</div>
    <i class="bi bi-cash-coin" style="font-size: 2.5rem;"></i>
</div>
</div>
</div>
</div>
            
<div class="col-md-4 mb-3">
<div class="card text-white bg-warning">
<div class="card-body">
<div class="d-flex justify-content-between">
<div>
    <h5 class="card-title">Total Medicines</h5>
    <p class="card-text fs-2" id="totalMedicines">0</p>
</div>
    <i class="bi bi-capsule" style="font-size: 2.5rem;"></i>
</div>
</div>
</div>
</div>
</div>

<ul class="nav nav-tabs" id="myTab" role="tablist">
<li class="nav-item" role="presentation">
<button class="nav-link active" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions-pane" type="button" role="tab">Transactions</button>
</li>

<li class="nav-item" role="presentation">
<button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports-pane" type="button" role="tab">Analytics</button>
</li>
</ul>

<div class="tab-content" id="myTabContent">

<!-- Transactions Tab -->
<div class="tab-pane fade show active" id="transactions-pane" role="tabpanel">
<div class="card mt-3">
<div class="card-header">
    <h5 class="card-title mb-0">Transaction History</h5>
</div>
<div class="card-body">
<div class="table-responsive">
<table class="table table-hover align-middle">
<thead>
    <tr>
        <th>Date</th>
        <th>Type</th>
        <th>Category</th>
        <th>Amount</th>
        <th>Description</th>
    </tr>
</thead>
<tbody id="transactionsTableBody">
    <tr>
        <td colspan="5" class="text-center text-muted py-4">Loading transactions...</td>
    </tr>
</tbody>
</table>
</div>
</div>
</div>
</div>

<!-- Reports/Analytics Tab -->
<div class="tab-pane fade" id="reports-pane" role="tabpanel">
<div class="mt-3">
    <!-- Chart Type Selector -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex gap-2 align-items-center">
                <label for="chartType" class="form-label mb-0"><strong>Chart Type:</strong></label>
                <select id="chartType" class="form-select" style="max-width: 200px;">
                    <option value="bar">Bar Chart (Income vs Expenses)</option>
                    <option value="pie">Pie Chart (Distribution)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Financial Overview</h5>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="reportChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row mt-4">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Medicine Stock Summary</h5>
                </div>
                <div class="card-body">
                    <div id="medicineStockBody">
                        <p class="text-muted">Loading stock data...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Top Categories</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="topCategoriesList">
                        <li class="list-group-item">Loading categories...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

@endsection

@section('scripts')
<script>
    let transactionsData = [];
    let medicinesData = [];
    let categoriesData = [];
    let chartInstance = null;

    // Fetch all data on page load
    async function fetchAllData() {
        try {
            // Fetch transactions
            const transResponse = await fetch('/api/transactions', { credentials: 'same-origin' }).catch(() => null);
            if (transResponse?.ok) {
                const transData = await transResponse.json();
                transactionsData = transData || [];
            }

            // Fetch medicines
            const medResponse = await fetch('/medicines', { credentials: 'same-origin' });
            if (medResponse.ok) {
                const medData = await medResponse.json();
                medicinesData = medData.data || [];
            }

            // Fetch categories
            const catResponse = await fetch('/categories', { credentials: 'same-origin' });
            if (catResponse.ok) {
                const catData = await catResponse.json();
                categoriesData = catData.data || [];
            }

            updateDashboard();
            renderTransactionsTable();
            renderMedicineStock();
            renderTopCategories();
            renderChart('bar');
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    function updateDashboard() {
        // Calculate totals
        const totalIncome = transactionsData.filter(t => t.type === 'income').reduce((sum, t) => sum + parseFloat(t.amount || 0), 0);
        
        document.getElementById('totalTransactions').textContent = transactionsData.length;
        document.getElementById('totalIncome').textContent = '$' + totalIncome.toFixed(2);
        document.getElementById('totalMedicines').textContent = medicinesData.length;
    }

    function renderTransactionsTable() {
        const tbody = document.getElementById('transactionsTableBody');
        
        if (transactionsData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No transactions found</td></tr>';
            return;
        }

        tbody.innerHTML = transactionsData.map(trans => `
            <tr>
                <td>${new Date(trans.date || trans.created_at).toLocaleDateString()}</td>
                <td>
                    <span class="badge ${trans.type === 'income' ? 'bg-success' : 'bg-danger'}">
                        ${trans.type?.charAt(0).toUpperCase() + trans.type?.slice(1)}
                    </span>
                </td>
                <td>${trans.category || '-'}</td>
                <td class="fw-bold ${trans.type === 'income' ? 'text-success' : 'text-danger'}">
                    ${trans.type === 'income' ? '+' : '-'}$${parseFloat(trans.amount || 0).toFixed(2)}
                </td>
                <td><small>${trans.description || '-'}</small></td>
            </tr>
        `).join('');
    }

    function renderMedicineStock() {
        const container = document.getElementById('medicineStockBody');
        
        if (medicinesData.length === 0) {
            container.innerHTML = '<p class="text-muted">No medicines in stock</p>';
            return;
        }

        const lowStock = medicinesData.filter(m => m.stock < 10).length;
        const outOfStock = medicinesData.filter(m => m.stock === 0).length;

        container.innerHTML = `
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total Medicines</span>
                    <strong>${medicinesData.length}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Low Stock (<10)</span>
                    <strong class="text-warning">${lowStock}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Out of Stock</span>
                    <strong class="text-danger">${outOfStock}</strong>
                </li>
            </ul>
        `;
    }

    function renderTopCategories() {
        const list = document.getElementById('topCategoriesList');
        
        if (categoriesData.length === 0) {
            list.innerHTML = '<li class="list-group-item">No categories available</li>';
            return;
        }

        const topCategories = categoriesData
            .filter(c => !c.parent_id)
            .slice(0, 5)
            .map(cat => `
                <li class="list-group-item d-flex justify-content-between">
                    <span>${cat.name}</span>
                    <small class="text-muted">${cat.subcategories?.length || 0} sub-categories</small>
                </li>
            `).join('');

        list.innerHTML = topCategories || '<li class="list-group-item">No categories available</li>';
    }

    function renderChart(type) {
        const canvas = document.getElementById('reportChart');
        const ctx = canvas.getContext('2d');
        
        // Destroy previous chart if exists
        if (chartInstance) {
            chartInstance.destroy();
        }

        const income = transactionsData.filter(t => t.type === 'income').reduce((sum, t) => sum + parseFloat(t.amount || 0), 0);
        const expense = transactionsData.filter(t => t.type === 'expense').reduce((sum, t) => sum + parseFloat(t.amount || 0), 0);

        const isDarkMode = document.documentElement.classList.contains('dark-mode');
        const textColor = isDarkMode ? '#e8e8e8' : '#000';
        const gridColor = isDarkMode ? '#2a2a2a' : '#e0e0e0';

        if (type === 'bar') {
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Income', 'Expenses'],
                    datasets: [{
                        label: 'Amount ($)',
                        data: [income, expense],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderColor: ['#20c997', '#e74c3c'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: textColor }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: textColor },
                            grid: { color: gridColor }
                        },
                        x: {
                            ticks: { color: textColor },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        } else if (type === 'pie') {
            chartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Income', 'Expenses'],
                    datasets: [{
                        data: [income, expense],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderColor: isDarkMode ? '#1a1a1a' : '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: textColor }
                        }
                    }
                }
            });
        }
    }

    // Event listener for chart type selector
    document.getElementById('chartType').addEventListener('change', (e) => {
        renderChart(e.target.value);
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        fetchAllData();
    });
</script>
@endsection
