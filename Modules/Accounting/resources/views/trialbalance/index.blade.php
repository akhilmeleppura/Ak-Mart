@extends('accounting::components.layouts.master')

@section('title', 'Trial Balance Report')

@section('page-style')
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Modern Styling */
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f0f2f5;
        color: #333;
    }

    .app-container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 25px 30px;
    }

    /* Updated Filter Bar */
    .filter-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 25px;
        border: 1px solid #eee;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px; /* Provides spacing between items */
    }

    .filter-group .input-group-text {
        font-weight: 500;
        color: #555;
        background: transparent;
        border: none;
        padding: 0;
    }
    
    .filter-group .form-control {
        border: 1px solid #ddd;
        padding: 8px 12px;
        border-radius: 4px;
        width: 250px; /* Fixed width for date picker */
    }

    .btn {
        padding: 8px 18px;
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
        transition: all 0.2s ease-in-out;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .btn-light {
        background-color: #f8f9fa;
        border-color: #ddd;
        color: #333;
    }
    
    .btn-light:hover {
        background-color: #e2e6ea;
    }

    /* Title Section */
    .title-section {
        margin-bottom: 25px;
    }

    .title-section h4 {
        font-size: 26px;
        font-weight: 700;
        color: #333;
        text-transform: uppercase;
        margin: 0;
    }

    /* Info Bar */
    .info-bar {
        background: #f7f9fc;
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 25px;
        display: flex;
        flex-wrap: wrap;
    }

    .info-item {
        flex: 1;
        min-width: 200px;
    }

    .info-item strong {
        color: #6c757d;
        font-weight: normal;
        font-size: 12px;
        display: block;
    }

    /* Table Styling */
    .data-card {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .table-responsive {
        border-radius: 8px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: #21355c;
        color: white;
    }

    .table thead th {
        padding: 14px 15px;
        font-weight: 500;
        text-align: left;
        color: white !important;
    }

    .table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    .table tbody tr:nth-child(even) {
        background: #fbfdff;
    }

    .account-link {
        color: #002F6C;
        text-decoration: underline;
    }

    .numeric-cell {
        text-align: right;
    }

    .table tfoot {
        background: #21355c;
        color: white;
        font-weight: bold;
    }

    .table tfoot td {
        padding: 14px 15px;
        color: white !important;
    }

    /* PDF Specific Styles */
    @media print {
        body {
            background: white;
            color: black;
        }
        .table thead th,
        .table tfoot td {
            color: white !important;
            background-color: #21355c !important;
        }
        .account-link {
            color: #002F6C !important;
        }
    }
</style>
@endsection

@section('content')
<div class="app-container">
    <!-- Filter Section -->
    <form method="GET" action="{{ route('accounting.trial-balance.index') }}">
        <div class="filter-container">
            <div class="filter-group">
                <span class="input-group-text">Duration</span>
                <input type="text" name="date_range" class="form-control flatpickr-range" 
                       value="{{ request('date_range') }}" placeholder="Start Date to End Date">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{ route('accounting.trial-balance.index') }}" class="btn btn-light">Clear</a>
            </div>
            <div class="filter-group">
                <button type="submit" formaction="{{ route('accounting.trial-balance.export-pdf') }}" 
                        class="btn btn-secondary">
                    Export as PDF
                </button>
            </div>
        </div>
    </form>

    <!-- Title Section -->
    <div class="title-section">
        <h4>TRIAL BALANCE</h4>
    </div>

    <!-- Info Bar -->
    <div class="info-bar">
        <div class="info-item">
            <strong>Data Period:</strong>
            <span>{{ request('date_range') ?? 'All Time' }}</span>
        </div>
        <div class="info-item">
            <strong>Generated By:</strong>
            <span>Admin</span>
        </div>
        <div class="info-item">
            <strong>Generated On:</strong>
            <span>{{ now()->format('D d, Y') }}</span>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th rowspan="2" style="color: white !important;">Account</th>
                        <th colspan="2" style="color: white !important;">Opening</th>
                        <th colspan="2" style="color: white !important;">Transaction</th>
                        <th colspan="2" style="color: white !important;">Closing</th>
                    </tr>
                    <tr>
                        <th style="color: white !important;">Debit (SAR)</th>
                        <th style="color: white !important;">Credit (SAR)</th>
                        <th style="color: white !important;">Debit (SAR)</th>
                        <th style="color: white !important;">Credit (SAR)</th>
                        <th style="color: white !important;">Debit (SAR)</th>
                        <th style="color: white !important;">Credit (SAR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalOpeningDebit = $totalOpeningCredit = 0;
                        $totalTransactionDebit = $totalTransactionCredit = 0;
                        $totalClosingDebit = $totalClosingCredit = 0;

                        $filteredData = $trialBalanceData->filter(function($row){
                            return $row['transaction_debit'] > 0 || $row['transaction_credit'] > 0;
                        });
                    @endphp

                    @if($filteredData->count() > 0)
                        @foreach($filteredData as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('accounting.ledger.details', ['id' => $row['account_id']]) }}{{ request('date_range') ? '?date_range=' . urlencode(request('date_range')) : '' }}" 
                                       target="_blank" class="account-link">
                                        {{ $row['account_name'] }}
                                    </a>
                                </td>
                                <td class="numeric-cell">{{ number_format($row['opening_debit'], 2) }}</td>
                                <td class="numeric-cell">{{ number_format($row['opening_credit'], 2) }}</td>
                                <td class="numeric-cell">{{ number_format($row['transaction_debit'], 2) }}</td>
                                <td class="numeric-cell">{{ number_format($row['transaction_credit'], 2) }}</td>
                                <td class="numeric-cell">{{ number_format($row['closing_debit'], 2) }}</td>
                                <td class="numeric-cell">{{ number_format($row['closing_credit'], 2) }}</td>
                            </tr>
                            @php
                                $totalOpeningDebit += $row['opening_debit'];
                                $totalOpeningCredit += $row['opening_credit'];
                                $totalTransactionDebit += $row['transaction_debit'];
                                $totalTransactionCredit += $row['transaction_credit'];
                                $totalClosingDebit += $row['closing_debit'];
                                $totalClosingCredit += $row['closing_credit'];
                            @endphp
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">No data available for the selected date range.</td>
                        </tr>
                    @endif
                </tbody>
                @if($filteredData->count() > 0)
                <tfoot>
                    <tr>
                        <td style="color: white !important;">Total</td>
                        <td class="numeric-cell" style="color: white !important;">{{ number_format($totalOpeningDebit, 2) }}</td>
                        <td class="numeric-cell" style="color: white !important;">{{ number_format($totalOpeningCredit, 2) }}</td>
                        <td class="numeric-cell" style="color: white !important;">{{ number_format($totalTransactionDebit, 2) }}</td>
                        <td class="numeric-cell" style="color: white !important;">{{ number_format($totalTransactionCredit, 2) }}</td>
                        <td class="numeric-cell" style="color: white !important;">{{ number_format($totalClosingDebit, 2) }}</td>
                        <td class="numeric-cell" style="color: white !important;">{{ number_format($totalClosingCredit, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date range picker
    flatpickr(".flatpickr-range", {
        mode: "range",
        dateFormat: "Y-m-d",
        locale: {
            rangeSeparator: " to "
        }
    });
});
</script>
@endsection