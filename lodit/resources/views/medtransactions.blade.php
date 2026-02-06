@extends('layouts.app')

@section('title', 'Medicine Transactions')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/medtransactions.css') }}">
@endsection

@section('content')
<br><br><br>

<div class="medtx-container p-4 border rounded shadow bg-dark text-light">

    <h2 class="fw-bold mb-3 text-white">Medicine Transactions</h2>

    <div class="d-flex justify-content-between align-items-start mb-3">

        {{-- LEFT INFO --}}
        <div>
            <p><strong>Total Sales:</strong> ${{ number_format($totalSales, 2) }}</p>

            @if(request('from_date') && request('until_date'))
                <small>
                    Showing results from
                    <strong>{{ request('from_date') }}</strong>
                    to
                    <strong>{{ request('until_date') }}</strong>
                </small>
            @endif
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="text-end">

            <a href="{{ url('/exportMedPdf') }}?{{ request()->getQueryString() }}"
               class="btn btn-outline-danger me-2 medtx-btn">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>

            <a href="{{ url('/exportMedExcel') }}?{{ request()->getQueryString() }}"
               class="btn btn-outline-success me-2 medtx-btn">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>

            <button onclick="window.print()"
                    class="btn btn-outline-secondary me-2 medtx-btn">
                <i class="bi bi-printer"></i> Print
            </button>

            <form action="{{ url('/clearMedTransactions') }}"
                  method="POST"
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="btn btn-outline-danger medtx-btn"
                        onclick="return confirm('Delete ALL medicine transactions?')">
                    <i class="bi bi-trash"></i> Clear All
                </button>
            </form>

            <button class="btn btn-outline-warning medtx-btn"
                    data-bs-toggle="collapse"
                    data-bs-target="#filterMedTx">
                <i class="bi bi-funnel"></i> Filter
            </button>

        </div>
    </div>

    {{-- FILTER --}}
    <div class="collapse mt-3" id="filterMedTx">
        <form action="{{ url('/medtransactions') }}"
              method="GET"
              class="border p-3 rounded bg-secondary text-light">

            <div class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date"
                           name="from_date"
                           value="{{ request('from_date') }}"
                           class="form-control bg-dark text-light border-light">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Until</label>
                    <input type="date"
                           name="until_date"
                           value="{{ request('until_date') }}"
                           class="form-control bg-dark text-light border-light">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sort</label>
                    <select name="sort"
                            class="form-select bg-dark text-light border-light">
                        <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>
                            Newest First
                        </option>
                        <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>
                            Oldest First
                        </option>
                    </select>
                </div>

                <div class="col-md-3 text-end">
                    <button type="submit"
                            class="btn btn-success w-100 medtx-btn">
                        <i class="bi bi-check-circle"></i> Apply
                    </button>
                </div>

            </div>
        </form>
    </div>

    <hr class="border-secondary">
{{-- TABLE --}}
<table class="table table-dark table-bordered table-striped align-middle medtx-table">
    <thead class="table-secondary text-dark">
        <tr>
            <th>Date</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Total ($)</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}</td>

            <td>{{ $order->customer_name }}</td>

            <td>
                @php
                    $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                    $items = $items ?? [];
                @endphp

                <ul class="mb-0">
                    @foreach($items as $item)
                        <li>
                            {{ $item['name'] ?? 'Unknown' }}
                            × {{ $item['quantity'] ?? 1 }}
                        </li>
                    @endforeach
                </ul>
            </td>

            <td>${{ number_format($order->total, 2) }}</td>

            {{-- STATUS --}}
            <td>
                @php
                    $status = $order->status ?? 'pending';
                @endphp

                <span class="badge
                    @if($status === 'pending') bg-warning
                    @elseif($status === 'in_process') bg-primary
                    @elseif($status === 'delivered') bg-success
                    @elseif($status === 'cancelled') bg-danger
                    @else bg-secondary
                    @endif
                ">
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </span>
            </td>

            {{-- ACTION --}}
            <td>
                @if($status !== 'cancelled' && $status !== 'delivered')
                    <form action="{{    route('orders.updateStatus', $order->id)    }}"
                          method="POST">
                        @csrf
                        @method('PATCH')

                        <select name="status"
                                class="form-select form-select-sm bg-dark text-light border-light"
                                onchange="this.form.submit()">

                            <option disabled selected>Change</option>
                            <option value="pending">Pending</option>
                            <option value="in_process">In Process</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>

                        </select>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
