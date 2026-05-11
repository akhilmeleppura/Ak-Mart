@extends('accounting::components.layouts.master')

@section('title', 'Journal Preview')


@section('page-style')
  <style>
    @media print {
      body * { visibility: hidden !important; }
      .printable-section, .printable-section * { visibility: visible !important; }
      .printable-section { position: absolute; left: 0; top: 0; width: 100%; }
      .layout-navbar, .layout-menu, .footer { display: none !important; }
    }
    .table-full-border {
      border-collapse: collapse !important;
      width: 100%;
    }
    .table-full-border th, .table-full-border td {
      border: 1px solid #000 !important;
      padding: 8px;
    }
    .table-full-border th {
      background-color: #e9ecef;
    }
    .btn-icon {
      width: 40px;
      height: 40px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: transparent;
      color: #333;
      padding: 0;
    }
    .btn-icon:hover {
      background-color: #f5f5f5;
    }
    .btn-icon i {
      font-size: 18px;
    }
  </style>
@endsection

@section('content')
<div class="row invoice-preview">
  <div class="col-12 d-flex justify-content-end align-items-center gap-2 mb-3 flex-wrap">
    <a href="{{ route('accounting.journal.edit', $journal->id) }}" class="btn-icon" data-bs-toggle="tooltip" title="Edit Journal">
      <i class="bx bx-edit-alt"></i>
    </a>
    <button class="btn-icon" data-bs-toggle="tooltip" title="Download">
      <i class="bx bx-download"></i>
    </button>
    <button class="btn-icon" onclick="window.print();" data-bs-toggle="tooltip" title="Print">
      <i class="bx bx-printer"></i>
    </button>
  </div>

  <div class="col-12 printable-section">
    <div class="card invoice-preview-card p-sm-12 p-6">
      <div class="card-body invoice-preview-header rounded">
        <div class="d-flex justify-content-between flex-wrap align-items-start">
          <div>
            <h5 class="mb-2">Demo Company امتحان</h5>
            <p class="mb-1">company@email.com</p>
            <p class="mb-0">+91 1234567891</p>
          </div>
          <div>
            <h5 class="mb-3">Journal #{{ $journal->journal_number }}</h5>
            <div><strong>Recorded On:</strong> {{ $journal->created_at->format('d-m-Y') }}</div>
            <div><strong>Transaction Date:</strong> {{ \Carbon\Carbon::parse($journal->transaction_date)->format('d-m-Y') }}</div>
          </div>
        </div>
      </div>

      <table class="table table-full-border mt-4">
        <thead>
          <tr>
            <th>Account</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($journal->entries as $entry)
          <tr>
            <td>{{ $entry->chartOfAccount?->account_name ?? '-' }}</td>
            <td class="text-end">{{ $entry->debit_amount > 0 ? number_format($entry->debit_amount, 2) : '' }}</td>
            <td class="text-end">{{ $entry->credit_amount > 0 ? number_format($entry->credit_amount, 2) : '' }}</td>
            <td>{{ $entry->description }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

    </div>
  </div>
</div>
@endsection
