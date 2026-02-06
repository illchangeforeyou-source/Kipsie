@extends('layouts.app')

@section('title', 'Transaction Print Page')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/transactions.css') }}">
@endsection

@section('content')
<br><br><br>

@if(session('level') == 2)
<div class="tx-container p-4 border rounded shadow bg-dark text-light">

    <h2 class="fw-bold mb-3 text-white">Transactions</h2>

    <div class="d-flex justify-content-between align-items-start mb-3">

        <div>
            <p><strong>Total Income:</strong> ${{ number_format($totalIncome, 2) }}</p>
            <p><strong>Total Expense:</strong> ${{ number_format($totalExpense, 2) }}</p>
            <p><strong>Balance:</strong> ${{ number_format($balance, 2) }}</p>
        </div>

        <div class="text-end">

            <a href="{{ route('transactions.exportPdf') }}" class="btn btn-outline-danger me-2 tx-btn">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>

            <a href="{{ route('transactions.exportExcel') }}" class="btn btn-outline-success me-2 tx-btn">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>

            <button onclick="window.print()" class="btn btn-outline-secondary me-2 tx-btn">
                <i class="bi bi-printer"></i> Print
            </button>

            <form action="{{ route('transactions.clear') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger tx-btn"
                    onclick="return confirm('Are you sure you want to delete ALL transactions? This cannot be undone.')">
                    <i class="bi bi-trash"></i> Clear All
                </button>
            </form>

            <button class="btn btn-outline-warning tx-btn" data-bs-toggle="collapse" data-bs-target="#selectDate">
                <i class="bi bi-funnel"></i> Filter
            </button>

            <button class="btn btn-primary tx-btn" data-bs-toggle="collapse" data-bs-target="#addTransactionForm">
                <i class="bi bi-plus"></i> Add Transaction
            </button>

        </div>
    </div>

    <div class="collapse mt-3" id="selectDate">
        <form action="{{ route('transactions.filter') }}" method="GET" class="border p-3 rounded bg-secondary text-light">
            <div class="row g-3 align-items-end">

                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select bg-dark text-light border-light">
                        <option value="">All</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control bg-dark text-light border-light" placeholder="Any">
                </div>

                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" class="form-control bg-dark text-light border-light">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Until</label>
                    <input type="date" name="until_date" class="form-control bg-dark text-light border-light">
                </div>

                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-success w-100 tx-btn">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>

            </div>
        </form>
    </div>

    <div class="collapse mt-3" id="addTransactionForm">
        <form action="{{ route('transactions.store') }}" method="POST" class="border p-3 rounded bg-secondary text-light">

            @csrf
            <div class="row g-3 align-items-end">

                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select bg-dark text-light border-light" required>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control bg-dark text-light border-light" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Amount ($)</label>
                    <input type="number" name="amount" step="0.01" class="form-control bg-dark text-light border-light" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control bg-dark text-light border-light" required>
                </div>

                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-success w-100 tx-btn">
                        <i class="bi bi-check-circle"></i> Save
                    </button>
                </div>

            </div>

        </form>
    </div>

    <hr class="border-secondary">

    <table class="table table-dark table-bordered table-striped align-middle tx-table">
        <thead class="table-secondary text-dark">
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Amount ($)</th>
            </tr>
        </thead>

        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->date }}</td>

                <td>
                    @if($t->type === 'income')
                        <span class="text-success fw-bold">{{ ucfirst($t->type) }}</span>
                    @else
                        <span class="text-danger fw-bold">{{ ucfirst($t->type) }}</span>
                    @endif
                </td>

                <td>{{ $t->category }}</td>
                <td>${{ number_format($t->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endif

@endsection
