@extends('layouts/layoutMaster')

@section('title', __('Currency Management') . ' — AK-Mart')

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
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Supported Currencies') }}</h5>
                <div class="d-flex align-items-center gap-3">
                    <div class="w-px-250">
                        <input type="text" class="form-control date-picker" placeholder="{{ __('Filter by Date Range') }}" id="dateRange" />
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCurrencyModal">
                        <i class="bx bx-plus me-1"></i> {{ __('Add Currency') }}
                    </button>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-currencies table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Symbol') }}</th>
                            <th>{{ __('Exchange Rate') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Currency Modal -->
<div class="modal fade" id="addCurrencyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Add New Currency') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addCurrencyForm" action="{{ route('app-saas-currencies-store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-4">
            <label class="form-label">{{ __('Currency Name') }}</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Euro" required>
          </div>
          <div class="mb-4">
            <label class="form-label">{{ __('Currency Code (ISO)') }}</label>
            <input type="text" name="code" class="form-control" placeholder="e.g. EUR" maxlength="3" required>
          </div>
          <div class="mb-4">
            <label class="form-label">{{ __('Symbol') }}</label>
            <input type="text" name="symbol" class="form-control" placeholder="e.g. €" required>
          </div>
          <div class="mb-4">
            <label class="form-label">{{ __('Exchange Rate (vs USD)') }}</label>
            <input type="number" step="0.00000001" name="exchange_rate" class="form-control" placeholder="e.g. 0.92" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save Currency') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function (e) {
  const dt_currency_table = document.querySelector('.datatables-currencies');

  if (dt_currency_table) {
    const dt_currencies = $(dt_currency_table).DataTable({
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
        { data: 'code' },
        { data: 'symbol' },
        { data: 'exchange_rate' },
        { data: 'is_active' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          targets: 2,
          render: function (data) {
            return `<code class="fw-bold">${data}</code>`;
          }
        },
        {
          targets: 4,
          render: function (data, type, full) {
            return `1 USD = ${parseFloat(data).toFixed(4)} ${full['code']}`;
          }
        },
        {
          targets: 5,
          render: function (data, type, full) {
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
                        placeholder: @json(__('Search Currency'))
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

    // Toggle Status
    $(document).on('click', '.toggle-status', function () {
      const id = $(this).data('id');
      $.post(`${baseUrl}app/saas/currencies/toggle/${id}`, { _token: '{{ csrf_token() }}' }, function (res) {
        dt_currencies.ajax.reload();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: @json(__('Updated!')),
              text: @json(__('Currency status updated successfully.')),
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
                url: `${baseUrl}app/saas/currencies/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                  dt_currencies.ajax.reload();
                  Swal.fire({
                    icon: 'success',
                    title: @json(__('Deleted!')),
                    text: @json(__('Currency has been deleted.')),
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
          dt_currencies.ajax.reload();
        }
      }
    });
  }
});
</script>
@endsection
