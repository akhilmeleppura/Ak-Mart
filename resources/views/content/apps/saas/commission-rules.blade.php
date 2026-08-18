@extends('layouts/layoutMaster')

@section('title', __('Commission Rules') . ' — AK-Mart')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/moment/moment.js'
])
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-4">
        <div>
          <h5 class="card-title mb-1">{{ __('Commission Rules') }}</h5>
          <p class="text-muted mb-0 small">{{ __('Define platform fees applied to vendor sales.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="w-px-250">
                <input type="text" class="form-control date-picker" placeholder="{{ __('Filter by Date Range') }}" id="dateRange" />
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRuleModal">
              <i class="bx bx-plus me-1"></i> {{ __('Add Rule') }}
            </button>
        </div>
      </div>
      <div class="card-datatable table-responsive">
        <table class="datatables-commissions table border-top">
          <thead>
            <tr>
              <th></th>
              <th>{{ __('Name') }}</th>
              <th>{{ __('Type') }}</th>
              <th>{{ __('Value') }}</th>
              <th>{{ __('Scope') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Actions') }}</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Advanced Feature: Commission Tiers --}}
<div class="row mt-6">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-4">
        <div>
          <h5 class="card-title mb-1">{{ __('Volume-Based Tiers (Advanced)') }}</h5>
          <p class="text-muted mb-0 small">{{ __('Override base commission rules when a vendor\'s monthly sales hit thresholds.') }}</p>
        </div>
        <button type="button" class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#addTierModal">
          <i class="bx bx-layer-plus me-1"></i> {{ __('Add Tier') }}
        </button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>{{ __('Min Monthly Sales') }}</th>
                <th>{{ __('Max Monthly Sales') }}</th>
                <th>{{ __('Applied Percentage') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tiers as $tier)
              <tr>
                <td><strong>${{ number_format($tier->min_amount, 2) }}</strong></td>
                <td>{{ $tier->max_amount ? '$' . number_format($tier->max_amount, 2) : '∞' }}</td>
                <td><span class="badge bg-label-success">{{ $tier->percentage }}%</span></td>
                <td><span class="badge bg-label-primary">{{ __('Active') }}</span></td>
                <td>
                  <button class="btn btn-sm btn-icon btn-label-danger btn-delete-tier" data-id="{{ $tier->id }}">
                    <i class="bx bx-trash"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-6">{{ __('No volume tiers defined.') }}</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Add Tier Modal --}}
<div class="modal fade" id="addTierModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Add Commission Tier') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addTierForm">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('Min Sales Amount ($)') }}</label>
              <input type="number" name="min_amount" class="form-control" placeholder="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Max Sales Amount ($)') }}</label>
              <input type="number" name="max_amount" class="form-control" placeholder="{{ __('Optional') }}">
            </div>
            <div class="col-12">
              <label class="form-label">{{ __('Reward Commission (%)') }}</label>
              <input type="number" name="percentage" class="form-control" step="0.01" placeholder="e.g. 3.5" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save Tier') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Add Rule Modal --}}
<div class="modal fade" id="addRuleModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Add Commission Rule') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addRuleForm">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">{{ __('Rule Name') }}</label>
            <input type="text" name="name" class="form-control" placeholder="e.g., Global 10% Fee" required>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label class="form-label">{{ __('Type') }}</label>
              <select name="type" class="form-select" required>
                <option value="percentage">{{ __('Percentage (%)') }}</option>
                <option value="flat">{{ __('Flat Amount ($)') }}</option>
              </select>
            </div>
            <div class="col">
              <label class="form-label">{{ __('Value') }}</label>
              <input type="number" name="value" class="form-control" step="0.01" min="0" placeholder="10.00" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('Scope') }}</label>
            <select name="scope" class="form-select" id="scopeSelect">
              <option value="global">{{ __('Global (All Vendors)') }}</option>
              <option value="category">{{ __('Specific Category') }}</option>
            </select>
          </div>
          <div class="mb-3 d-none" id="categoryField">
            <label class="form-label">{{ __('Category') }}</label>
            <select name="category_id" class="form-select">
              <option value="">{{ __('Select Category') }}</option>
              @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <input type="hidden" name="is_global" id="isGlobalInput" value="1">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save Rule') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function (e) {
  const dt_commission_table = document.querySelector('.datatables-commissions');

  if (dt_commission_table) {
    const dt_commissions = $(dt_commission_table).DataTable({
      ajax: {
        url: window.location.href,
        data: function (d) {
          const dateRange = $('#dateRange').val();
          if (dateRange && dateRange.includes(' to ')) {
            const dates = dateRange.split(' to ');
            d.start_date = dates[0];
            d.end_date = dates[1];
          }
        }
      },
      columns: [
        { data: 'id', visible: false },
        { data: 'name' },
        { data: 'type' },
        { data: 'value' },
        { data: 'is_global' },
        { data: 'is_active' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          targets: 1,
          render: function (data) {
            return `<strong>${data}</strong>`;
          }
        },
        {
          targets: 2,
          render: function (data) {
            return `<span class="badge bg-label-info text-capitalize">${data}</span>`;
          }
        },
        {
          targets: 3,
          render: function (data, type, full) {
            return full['type'] === 'percentage' ? `<span class="text-primary fw-bold">${data}%</span>` : `<span class="text-primary fw-bold">$${parseFloat(data).toFixed(2)}</span>`;
          }
        },
        {
          targets: 4,
          render: function (data, type, full) {
            if (data) return '<span class="badge bg-label-warning">' + @json(__('Global')) + '</span>';
            if (full['branch']) return `<span class="badge bg-label-primary">Vendor: ${full['branch'].name}</span>`;
            if (full['category']) return `<span class="badge bg-label-secondary">Category: ${full['category'].name}</span>`;
            return '<span class="badge bg-label-dark">' + @json(__('Custom')) + '</span>';
          }
        },
        {
          targets: 5,
          render: function (data) {
            const status = data ? 'success' : 'danger';
            const label = data ? @json(__('Active')) : @json(__('Inactive'));
            return `<span class="badge bg-label-${status}">${label}</span>`;
          }
        },
        {
          targets: -1,
          title: @json(__('Actions')),
          searchable: false,
          orderable: false,
          render: function (data, type, full) {
            return `
              <div class="d-flex align-items-center">
                <button class="btn btn-sm btn-icon toggle-status" data-id="${data}"><i class="bx ${full['is_active'] ? 'bx-block' : 'bx-check'}"></i></button>
                <button class="btn btn-sm btn-icon delete-record text-danger" data-id="${data}"><i class="bx bx-trash"></i></button>
              </div>`;
          }
        }
      ],
      order: [1, 'asc'],
      layout: {
        topStart: {
            features: [
                {
                    search: {
                        placeholder: @json(__('Search Rule'))
                    }
                }
            ]
        },
        topEnd: {
            features: [
                {
                    buttons: [
                        {
                            extend: 'collection',
                            className: 'btn btn-label-secondary dropdown-toggle',
                            text: '<i class="bx bx-export me-1"></i> ' + @json(__('Export')),
                            buttons: [
                                { extend: 'print', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4] } },
                                { extend: 'csv', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4] } },
                                { extend: 'excel', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4] } },
                                { extend: 'pdf', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4] } }
                            ]
                        }
                    ]
                }
            ]
        }
      }
    });

    // Toggle Scope
    $('#scopeSelect').on('change', function () {
      if ($(this).val() === 'category') {
        $('#categoryField').removeClass('d-none');
        $('#isGlobalInput').val('0');
      } else {
        $('#categoryField').addClass('d-none');
        $('#isGlobalInput').val('1');
      }
    });

    // Submit Add Rule Form
    $('#addRuleForm').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
        url: `${baseUrl}app/saas/commission-rules`,
        type: 'POST',
        data: $(this).serialize(),
        success: function (res) {
          $('#addRuleModal').modal('hide');
          $('#addRuleForm')[0].reset();
          dt_commissions.ajax.reload();
          if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: @json(__('Saved!')),
                text: res.message,
                customClass: { confirmButton: 'btn btn-success' }
              });
          }
        },
        error: function (xhr) {
          if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'error',
                title: @json(__('Error')),
                text: xhr.responseJSON ? xhr.responseJSON.message : @json(__('Failed to save rule.')),
                customClass: { confirmButton: 'btn btn-danger' }
              });
          }
        }
      });
    });

    // Submit Add Tier Form
    $('#addTierForm').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
        url: `${baseUrl}app/saas/commission-tiers`,
        type: 'POST',
        data: $(this).serialize(),
        success: function (res) {
          $('#addTierModal').modal('hide');
          $('#addTierForm')[0].reset();
          location.reload();
        },
        error: function (xhr) {
          if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'error',
                title: @json(__('Error')),
                text: xhr.responseJSON ? xhr.responseJSON.message : @json(__('Failed to save tier.')),
                customClass: { confirmButton: 'btn btn-danger' }
              });
          }
        }
      });
    });

    // Delete Tier
    $(document).on('click', '.btn-delete-tier', function () {
      const id = $(this).data('id');
      if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: @json(__('Are you sure?')),
            text: @json(__("You won't be able to revert this!")),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: @json(__('Yes, delete it!')),
            customClass: {
              confirmButton: 'btn btn-primary me-3',
              cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
          }).then(function (result) {
            if (result.value) {
              $.ajax({
                url: `${baseUrl}app/saas/commission-tiers/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                  location.reload();
                }
              });
            }
          });
      }
    });

    // Toggle Status
    $(document).on('click', '.toggle-status', function () {
      const id = $(this).data('id');
      $.post(`${baseUrl}app/saas/commission-rules/toggle/${id}`, { _token: '{{ csrf_token() }}' }, function (res) {
        dt_commissions.ajax.reload();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: @json(__('Updated!')),
              text: @json(__('Rule status updated successfully.')),
              customClass: { confirmButton: 'btn btn-success' }
            });
        }
      });
    });

    // Delete
    $(document).on('click', '.delete-record', function () {
      const id = $(this).data('id');
      if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: @json(__('Are you sure?')),
            text: @json(__("You won't be able to revert this!")),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: @json(__('Yes, delete it!')),
            customClass: {
              confirmButton: 'btn btn-primary me-3',
              cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
          }).then(function (result) {
            if (result.value) {
              $.ajax({
                url: `${baseUrl}app/saas/commission-rules/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                  dt_commissions.ajax.reload();
                  Swal.fire({
                    icon: 'success',
                    title: @json(__('Deleted!')),
                    text: @json(__('Rule has been deleted.')),
                    customClass: { confirmButton: 'btn btn-success' }
                  });
                }
              });
            }
          });
      }
    });

    // Flatpickr
    flatpickr('#dateRange', {
      mode: 'range',
      dateFormat: 'Y-m-d',
      onChange: function (selectedDates) {
        if (selectedDates.length === 2) {
          dt_commissions.ajax.reload();
        }
      }
    });
  }
});
</script>
@endsection
