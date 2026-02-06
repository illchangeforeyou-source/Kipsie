@extends('layouts.app')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        /* Use global theme variables from layout for consistent theming */

        /* Dark mode text colors - periwinkle to white - ONLY in reports content area */
        body.dark-mode .container {
            --text-primary: #e8e8e8;
            --text-accent: #9d9dff;
        }

        body.dark-mode .container a {
            color: #9d9dff;
        }

        body.dark-mode .container a:hover {
            color: #c0c0ff;
        }

        .reports-header {
            background: var(--card-bg);
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            color: var(--card-text);
        }

        .reports-header h1 {
            color: var(--accent);
            margin: 0;
            font-size: 32px;
        }

        .reports-header p {
            color: var(--muted);
            margin: 10px 0 0 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .report-card {
            background: var(--card-bg);
            color: var(--card-text);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        body.dark-mode .report-card {
            color: #e8e8e8;
        }

        body.dark-mode .report-card p,
        body.dark-mode .report-card span,
        body.dark-mode .report-card td {
            color: #e8e8e8 !important;
        }

        .report-card h2 {
            color: var(--accent);
            border-bottom: 2px solid var(--table-border);
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-container {
            position: relative;
            height: 350px;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-box h3 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .stat-box p {
            margin: 5px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .export-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-export {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .btn-export.btn-excel {
            background: #16a34a;
        }

        .btn-export:hover {
            background: #1e40af;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-export.btn-excel:hover {
            background: #15803d;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            margin-top: 20px;
            color: var(--card-text);
        }

        table thead {
            background: var(--table-head-bg);
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: var(--card-text);
            border: none;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid var(--table-border);
        }

        table tbody tr:hover {
            background: var(--card-hover);
        }

        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .filter-group select,
        .filter-group input {
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        body.dark-mode .filter-group select,
        body.dark-mode .filter-group input {
            background: #1f2937;
            color: #f9fafb;
            border-color: #4b5563;
        }

        .badge-custom {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        body.dark-mode .badge-success {
            background: #064e3b;
            color: #86efac;
        }

        @media (max-width: 768px) {
            .filter-section {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .report-card h2 {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Header -->
        <div class="reports-header">
            <h1><i class="bi bi-graph-up"></i> Reports & Analytics</h1>
            <p>Monitor your pharmacy sales, orders, and performance metrics</p>
        </div>

        <!-- Dashboard Overview -->
        <div class="report-card">
            <h2>
                <span>Monthly Overview</span>
                <div class="export-buttons">
                    <button class="btn-export" onclick="exportMonthlyReport('pdf'); return false;" title="Export as PDF">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </button>
                    <button class="btn-export btn-excel" onclick="exportMonthlyReport('excel'); return false;" title="Export as Excel">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </button>
                </div>
            </h2>

            <div class="filter-section">
                <div class="filter-group">
                    <label>Select Month:</label>
                    <select id="monthSelect" onchange="updateCharts()">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Select Year:</label>
                    <select id="yearSelect" onchange="updateCharts()">
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                    </select>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-box">
                    <h3 id="totalSales">$0</h3>
                    <p>Total Sales</p>
                </div>
                <div class="stat-box" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                    <h3 id="totalOrders">0</h3>
                    <p>Total Orders</p>
                </div>
                <div class="stat-box" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
                    <h3 id="avgOrder">$0</h3>
                    <p>Avg. Order Value</p>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>

        <!-- Transactions & Analytics -->
        <div class="report-card">
            <h2>
                <span><i class="bi bi-graph-up"></i> Financial Analytics</span>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <label for="chartTypeSelect" style="margin: 0; font-weight: 600; color: var(--card-text); font-size: 13px;">Chart:</label>
                    <select id="chartTypeSelect" onchange="updateFinancialChart()" style="padding: 4px 8px; border-radius: 4px; border: 1px solid var(--table-border); background: var(--card-bg); color: var(--card-text); cursor: pointer; font-size: 12px;">
                        <option value="bar">Bar</option>
                        <option value="pie">Pie</option>
                    </select>
                </div>
            </h2>

            <div class="chart-container">
                <canvas id="financialChart"></canvas>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTable">
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--muted);">Loading transactions...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Medicines -->
        <div class="report-card">
            <h2><i class="bi bi-capsule"></i> Top Selling Medicines</h2>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Medicine Name</th>
                            <th>Quantity Sold</th>
                            <th>Revenue</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="topMedicinesTable">
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--muted);">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Annual Report -->
        <div class="report-card">
            <h2>
                <span>Annual Report</span>
                <div class="export-buttons">
                    <button class="btn-export" onclick="exportAnnualReport('pdf'); return false;" title="Export as PDF">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </button>
                    <button class="btn-export btn-excel" onclick="exportAnnualReport('excel'); return false;" title="Export as Excel">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </button>
                </div>
            </h2>

            <div class="filter-section">
                <div class="filter-group">
                    <label>Select Year:</label>
                    <select id="yearSelectAnnual" onchange="updateAnnualChart()">
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                    </select>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="annualSalesChart"></canvas>
            </div>
        </div>

        <!-- 5-Year Trend -->
        <div class="report-card">
            <h2>5-Year Trend</h2>
            <div class="chart-container">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="report-card">
            <h2>Recent Orders</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Items</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="recentOrdersTable">
                        <tr>
                            <td colspan="5" style="text-align: center; color: #9ca3af;">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let monthlySalesChart, annualSalesChart, trendChart, financialChart;
        let transactionsData = [];
        let medicinesData = [];
        let categoriesData = [];

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            document.getElementById('monthSelect').value = today.getMonth() + 1;
            document.getElementById('yearSelect').value = today.getFullYear();
            document.getElementById('yearSelectAnnual').value = today.getFullYear();

            loadData();
        });

        function loadData() {
            fetchTransactions();
            fetchMedicines();
            fetchCategories();
            loadTopMedicines();
            loadRecentOrders();
            updateCharts();
            updateAnnualChart();
            loadTrendData();
        }

        async function fetchTransactions() {
            try {
                const response = await fetch('/api/transactions', { credentials: 'same-origin' });
                if (response.ok) {
                    const data = await response.json();
                    transactionsData = Array.isArray(data) ? data : (data.data || []);
                    renderTransactionsTable();
                    updateFinancialChart();
                } else {
                    console.error('Failed to fetch transactions:', response.status);
                    renderTransactionsTable();
                }
            } catch (error) {
                console.error('Error fetching transactions:', error);
                renderTransactionsTable();
            }
        }

        async function fetchMedicines() {
            try {
                const response = await fetch('/medicines', { credentials: 'same-origin' });
                if (response.ok) {
                    const data = await response.json();
                    medicinesData = data.data || [];
                }
            } catch (error) {
                console.error('Error fetching medicines:', error);
            }
        }

        async function fetchCategories() {
            try {
                const response = await fetch('/categories', { credentials: 'same-origin' });
                if (response.ok) {
                    const data = await response.json();
                    categoriesData = data.data || [];
                }
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        }

        function renderTransactionsTable() {
            const tbody = document.getElementById('transactionsTable');
            
            if (!transactionsData || transactionsData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--muted);">No transactions found</td></tr>';
                return;
            }

            tbody.innerHTML = transactionsData.map(trans => `
                <tr>
                    <td>${new Date(trans.date || trans.created_at).toLocaleDateString()}</td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; background: ${trans.type === 'income' ? '#d1fae5' : '#fee2e2'}; color: ${trans.type === 'income' ? '#065f46' : '#991b1b'};">
                            ${trans.type?.charAt(0).toUpperCase() + trans.type?.slice(1)}
                        </span>
                    </td>
                    <td>${trans.category || '-'}</td>
                    <td style="font-weight: 600; color: ${trans.type === 'income' ? '#10b981' : '#ef4444'};">
                        ${trans.type === 'income' ? '+' : '-'}$${parseFloat(trans.amount || 0).toFixed(2)}
                    </td>
                    <td>${trans.description || '-'}</td>
                </tr>
            `).join('');
        }

        function updateFinancialChart() {
            const canvas = document.getElementById('financialChart');
            const ctx = canvas.getContext('2d');
            
            if (financialChart) {
                financialChart.destroy();
            }

            const chartType = document.getElementById('chartTypeSelect').value;
            const income = transactionsData.filter(t => t.type === 'income').reduce((sum, t) => sum + parseFloat(t.amount || 0), 0);
            const expense = transactionsData.filter(t => t.type === 'expense').reduce((sum, t) => sum + parseFloat(t.amount || 0), 0);

            const isDarkMode = document.body.classList.contains('dark-mode');
            const textColor = isDarkMode ? '#e8e8e8' : '#000';
            const gridColor = isDarkMode ? '#4b5563' : '#e5e7eb';

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: textColor, font: { size: 12 } }
                    }
                }
            };

            if (chartType === 'bar') {
                financialChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Income', 'Expenses'],
                        datasets: [{
                            label: 'Amount ($)',
                            data: [income, expense],
                            backgroundColor: ['#10b981', '#ef4444'],
                            borderColor: ['#059669', '#dc2626'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        ...commonOptions,
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
            } else if (chartType === 'pie') {
                financialChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Income', 'Expenses'],
                        datasets: [{
                            data: [income, expense],
                            backgroundColor: ['#10b981', '#ef4444'],
                            borderColor: isDarkMode ? '#1f2937' : '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: commonOptions
                });
            }
        }

        function updateCharts() {
            const month = document.getElementById('monthSelect').value;
            const year = document.getElementById('yearSelect').value;
            const isDarkMode = document.body.classList.contains('dark-mode');
            const textColor = isDarkMode ? '#e8e8e8' : '#6b7280';
            const gridColor = isDarkMode ? '#4b5563' : '#e5e7eb';

            fetch(`/reports/api/monthly-sales/${year}/${month}`)
                .then(r => {
                    if (!r.ok) throw new Error('Failed to fetch monthly sales');
                    return r.json();
                })
                .then(data => {
                    document.getElementById('totalSales').textContent = '$' + (data.total_sales || 0).toFixed(2);
                    document.getElementById('totalOrders').textContent = data.total_orders || 0;
                    document.getElementById('avgOrder').textContent = '$' + (data.avg_order || 0).toFixed(2);

                    if (monthlySalesChart) monthlySalesChart.destroy();
                    
                    const ctx = document.getElementById('monthlySalesChart').getContext('2d');
                    monthlySalesChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.days || [],
                            datasets: [{
                                label: 'Daily Sales ($)',
                                data: data.daily_sales || [],
                                backgroundColor: '#1e3a8a',
                                borderColor: '#1e3a8a',
                                borderRadius: 8,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                })
                .catch(err => {
                    console.error('Error loading monthly sales:', err);
                    document.getElementById('totalSales').textContent = 'N/A';
                    document.getElementById('totalOrders').textContent = 'N/A';
                    document.getElementById('avgOrder').textContent = 'N/A';
                });
        }

        function updateAnnualChart() {
            const year = document.getElementById('yearSelectAnnual').value;
            const isDarkMode = document.body.classList.contains('dark-mode');
            const textColor = isDarkMode ? '#e8e8e8' : '#6b7280';
            const gridColor = isDarkMode ? '#4b5563' : '#e5e7eb';

            fetch(`/reports/api/annual-sales/${year}`)
                .then(r => r.json())
                .then(data => {
                    if (annualSalesChart) annualSalesChart.destroy();
                    
                    const ctx = document.getElementById('annualSalesChart').getContext('2d');
                    annualSalesChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.months || [],
                            datasets: [{
                                label: 'Monthly Revenue ($)',
                                data: data.revenue || [],
                                borderColor: '#1e3a8a',
                                backgroundColor: 'rgba(30, 58, 138, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 2,
                                pointRadius: 4,
                                pointBackgroundColor: '#1e3a8a'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                });
        }

        function loadTrendData() {
            const isDarkMode = document.body.classList.contains('dark-mode');
            const textColor = isDarkMode ? '#e8e8e8' : '#6b7280';
            const gridColor = isDarkMode ? '#4b5563' : '#e5e7eb';

            fetch('/reports/api/five-year-trend')
                .then(r => r.json())
                .then(data => {
                    if (trendChart) trendChart.destroy();
                    
                    const ctx = document.getElementById('trendChart').getContext('2d');
                    trendChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.years || [],
                            datasets: [
                                {
                                    label: 'Revenue ($)',
                                    data: data.revenues || [],
                                    borderColor: '#1e3a8a',
                                    backgroundColor: 'rgba(30, 58, 138, 0.1)',
                                    tension: 0.4,
                                    borderWidth: 2,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Orders',
                                    data: data.orders || [],
                                    borderColor: '#059669',
                                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                                    tension: 0.4,
                                    borderWidth: 2,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: { color: textColor }
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    position: 'left',
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                },
                                y1: {
                                    type: 'linear',
                                    position: 'right',
                                    ticks: { color: textColor },
                                    grid: { display: false }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                });
        }

        function loadTopMedicines() {
            fetch('/reports/api/top-medicines')
                .then(r => {
                    if (!r.ok) throw new Error('Failed to load medicines');
                    return r.json();
                })
                .then(data => {
                    const tbody = document.getElementById('topMedicinesTable');
                    tbody.innerHTML = '';
                    
                    if (!data || !data.medicines || data.medicines.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">No medicines data available</td></tr>';
                        return;
                    }
                    
                    data.medicines.forEach(med => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${med.name}</td>
                                <td>${med.quantity_sold}</td>
                                <td>$${parseFloat(med.total_revenue || 0).toFixed(2)}</td>
                                <td><span class="badge-custom badge-success">Active</span></td>
                            </tr>
                        `;
                    });
                })
                .catch(err => {
                    console.error('Error loading medicines:', err);
                    const tbody = document.getElementById('topMedicinesTable');
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #ef4444;">Error loading data</td></tr>';
                });
        }

        function loadRecentOrders() {
            fetch('/reports/api/recent-orders')
                .then(r => {
                    if (!r.ok) throw new Error('Failed to load orders');
                    return r.json();
                })
                .then(data => {
                    const tbody = document.getElementById('recentOrdersTable');
                    tbody.innerHTML = '';
                    
                    if (!data || !data.orders || data.orders.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No orders data available</td></tr>';
                        return;
                    }
                    
                    data.orders.forEach(order => {
                        tbody.innerHTML += `
                            <tr>
                                <td>#${order.id}</td>
                                <td>${order.customer_name || 'N/A'}</td>
                                <td>$${parseFloat(order.total_price || 0).toFixed(2)}</td>
                                <td>${order.item_count || 0}</td>
                                <td>${new Date(order.created_at).toLocaleDateString()}</td>
                            </tr>
                        `;
                    });
                })
                .catch(err => {
                    console.error('Error loading orders:', err);
                    const tbody = document.getElementById('recentOrdersTable');
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #ef4444;">Error loading data</td></tr>';
                });
        }

        function exportMonthlyReport(format = 'pdf') {
            const month = document.getElementById('monthSelect').value;
            const year = document.getElementById('yearSelect').value;
            if (format === 'excel') {
                window.location.href = `/reports/export/monthly/${year}/${month}?format=excel`;
            } else {
                window.location.href = `/reports/export/monthly/${year}/${month}`;
            }
        }

        function exportAnnualReport(format = 'pdf') {
            const year = document.getElementById('yearSelectAnnual').value;
            if (format === 'excel') {
                window.location.href = `/reports/export/annual/${year}?format=excel`;
            } else {
                window.location.href = `/reports/export/annual/${year}`;
            }
        }
    </script>
@endsection
