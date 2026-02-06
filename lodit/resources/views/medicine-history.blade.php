@extends('layouts.app')

@section('head')
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
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

        .report-card {
            background: var(--card-bg);
            color: var(--card-text);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        .report-card h2 {
            color: var(--accent);
            border-bottom: 2px solid var(--table-border);
            padding-bottom: 15px;
            margin-bottom: 25px;
            margin-top: 0;
        }

        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 14px;
            color: var(--muted);
        }

        .filter-group input,
        .filter-group select {
            padding: 10px 12px;
            border: 1px solid var(--table-border);
            border-radius: 6px;
            background: var(--input-bg);
            color: var(--card-text);
            font-size: 14px;
        }

        body.dark-mode .filter-group input,
        body.dark-mode .filter-group select {
            background: #2a2a3a;
            color: #e8e8e8;
            border-color: #444;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <!-- Header -->
    <div class="reports-header">
        <h1><i class="bi bi-capsule"></i> My Medicine Purchase History</h1>
        <p>View and manage your past medicine purchases</p>
    </div>

    <!-- History Section -->
    <div class="report-card">
        <h2>Purchase History</h2>

        <div class="filter-section">
            <form action="{{ url('/medicine-history') }}" method="GET" style="display: flex; gap: 15px; width: 100%; flex-wrap: wrap; align-items: flex-end;">
                <div class="filter-group">
                    <label>From Date:</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" />
                </div>
                <div class="filter-group">
                    <label>Until Date:</label>
                    <input type="date" name="until_date" value="{{ request('until_date') }}" />
                </div>
                <div class="filter-group">
                    <label>Sort By:</label>
                    <select name="sort">
                        <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>Newest first</option>
                        <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>Oldest first</option>
                    </select>
                </div>
                <button class="btn btn-primary" style="padding: 10px 20px; border-radius: 6px; margin-top: 21px;">Filter</button>
            </form>
        </div>

        <div style="overflow-x: auto;">
            <table class="table table-bordered" style="width:100%; border-collapse:collapse;">
                <thead style="background:var(--table-header-bg); color: var(--card-text);">
                    <tr>
                        <th style="padding: 12px; text-align: left; border: 1px solid var(--table-border);">Date</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid var(--table-border);">Items</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid var(--table-border);">Total</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid var(--table-border);">Status</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid var(--table-border);">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr style="border-bottom: 1px solid var(--table-border);">
                            <td style="padding: 12px; border: 1px solid var(--table-border);">{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i') }}</td>
                            <td style="padding: 12px; border: 1px solid var(--table-border);">
                                @php $items = json_decode($order->items, true) ?? []; @endphp
                                <ul style="margin:0; padding-left:18px;">
                                    @foreach($items as $item)
                                        <li>{{ $item['name'] ?? 'Unknown' }} × {{ $item['quantity'] ?? 1 }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td style="padding: 12px; border: 1px solid var(--table-border);">${{ number_format($order->total ?? 0, 2) }}</td>
                            <td style="padding: 12px; border: 1px solid var(--table-border);">
                                @php $status = $order->status ?? 'pending'; @endphp
                                <span style="padding:6px 10px; border-radius:4px; color:#fff; background: @if($status==='delivered') #28a745 @elseif($status==='in_process') #0d6efd @elseif($status==='cancelled') #dc3545 @else #ffc107 @endif;">
                                    {{ ucfirst(str_replace('_',' ',$status)) }}
                                </span>
                            </td>
                            <td style="padding: 12px; border: 1px solid var(--table-border);">
                                <button class="btn btn-sm btn-info" onclick="showOrderReceipt({{ $order->id }}, '{{ addslashes($order->customer_name ?? 'Customer') }}', {{ $order->total ?? 0 }}, '{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i') }}')">
                                    <i class="bi bi-file-text"></i> Receipt
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px; border: 1px solid var(--table-border);">No purchases found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- Receipt Modal -->
<div class="modal fade" id="history-receipt-modal" tabindex="-1" role="dialog" aria-labelledby="history-receipt-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background: var(--card-bg); color: var(--card-text); border: 1px solid var(--table-border);">
            <div class="modal-header" style="border-bottom: 1px solid var(--table-border);">
                <h5 class="modal-title" id="history-receipt-modal-label">Order Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 25px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    @if(config('app.logo_path'))
                        <img src="{{ asset(config('app.logo_path')) }}" alt="Logo" style="max-height: 60px; margin-bottom: 15px;">
                    @endif
                    <h4 style="margin: 0; color: var(--accent);">Receipt</h4>
                </div>

                <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--table-border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-weight: 600;">Customer Name:</span>
                        <span id="history-receipt-customer-name">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-weight: 600;">Order Date:</span>
                        <span id="history-receipt-date">-</span>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <h6 style="font-weight: 600; margin-bottom: 10px;">Items Ordered:</h6>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--table-header-bg); border: 1px solid var(--table-border);">
                                <th style="padding: 10px; text-align: left; border: 1px solid var(--table-border);">Item</th>
                                <th style="padding: 10px; text-align: center; border: 1px solid var(--table-border);">Qty</th>
                                <th style="padding: 10px; text-align: right; border: 1px solid var(--table-border);">Price</th>
                            </tr>
                        </thead>
                        <tbody id="history-receipt-items-table">
                            <!-- Items will be populated here -->
                        </tbody>
                    </table>
                </div>

                <div style="padding: 15px; background: var(--table-header-bg); border-radius: 6px; text-align: right; margin-bottom: 20px;">
                    <div style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">
                        Total: <span id="history-receipt-total">$0.00</span>
                    </div>
                </div>

                <div style="padding: 12px; background: var(--table-header-bg); border-radius: 6px; text-align: center;">
                    <span id="history-receipt-status-badge" style="padding: 8px 15px; border-radius: 4px; color: #fff; font-weight: 600; font-size: 14px;">Pending</span>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--table-border); gap: 10px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="sendReceiptWhatsAppHistory()">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </button>
                <button type="button" class="btn btn-warning" onclick="sendReceiptEmailHistory()">
                    <i class="bi bi-envelope"></i> Email
                </button>
                <button type="button" class="btn btn-primary" onclick="printHistoryReceipt()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/medicine-history.js') }}"></script>

@endsection

