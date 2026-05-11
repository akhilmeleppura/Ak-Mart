'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dtTable = document.querySelector('.datatables-users');
  const config = window.dataTableConfig;

  if (dtTable && config) {
    const columnKeys = Object.keys(config.columns);
    const ajaxUrl = config.ajaxUrl;

    /** Dynamically build <thead> **/
    const theadRow = dtTable.querySelector('thead tr');
    theadRow.innerHTML = '<th></th>'; // Responsive Control column
    columnKeys.forEach(key => {
      const headerTitle = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
      theadRow.innerHTML += `<th>${headerTitle}</th>`;
    });
    theadRow.innerHTML += '<th>Actions</th>'; // Actions column

    /** Build Columns & ColumnDefs **/
    const columns = [{ data: null }]; // Responsive Control column
    let columnDefs = [
      {
        className: 'control',
        searchable: false,
        orderable: false,
        responsivePriority: 1,
        targets: 0,
        render: () => ''
      }
    ];

    columnKeys.forEach((key, index) => {
      columns.push({ data: key });
      columnDefs.push({
        targets: index + 1, // +1 because 0 is for responsive control
        render: function (data, type, full) {
          const columnConfig = config.columns[key];

          if (columnConfig.type === 'link') {
            let link = `${columnConfig.linkTo}/${full.id}`;
            if (columnConfig.linkSuffix) {
              link += columnConfig.linkSuffix;
            }
            return `<a href="${link}" class="text-primary">${full[key]}</a>`;
          }

          // Default Text Render
          return `<span>${full[key] || '-'}</span>`;
        },
        orderable: false,
        searchable: false
      });
    });

    // Actions column
    columns.push({ data: null });
    columnDefs.push({
      targets: -1,
      title: 'Actions',
      orderable: false,
      searchable: false,
      render: function (data, type, full) {
        let actionButtons = '<div class="d-flex align-items-center gap-2">';

        if (config.permissions?.canView) {
          actionButtons += `<a href="${config.actionsRoutePrefix}/${full.id}/view" class="btn btn-sm btn-info">View</a>`;
        }
        if (config.permissions?.canEdit) {
          actionButtons += `<a href="${config.actionsRoutePrefix}/${full.id}/edit" class="btn btn-sm btn-warning">Edit</a>`;
        }
        if (config.permissions?.canDelete) {
          actionButtons += `
            <button class="btn btn-sm btn-danger delete-journal" data-url="${config.actionsRoutePrefix}/${full.id}">
              <i class="bx bx-trash"></i>
            </button>`;
        }

        actionButtons += '</div>';
        return actionButtons;
      }
    });

    /** Initialize DataTable **/
    const dtInstance = new DataTable(dtTable, {
      processing: true,
      serverSide: true,
      ajax: {
        url: ajaxUrl,
        type: 'GET',
        dataSrc: function (json) {
          json.draw = json.draw || 1;
          json.recordsTotal = json.recordsTotal || 0;
          json.recordsFiltered = json.recordsFiltered || 0;
          return json.data || [];
        }
      },
      columns: columns,
      columnDefs: columnDefs,
      order: [[1, 'desc']],
      responsive: true,
      dom: '<"row mx-2"<"col-md-6"l><"col-md-6 dt-action-buttons text-end d-flex align-items-center justify-content-end gap-2"fB>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      language: {
        sLengthMenu: 'Show _MENU_',
        search: '',
        searchPlaceholder: 'Search...'
      },
      buttons: [
        // Export button (only shown if not hidden)
        ...(config.hideExportButton
          ? []
          : [
              {
                extend: 'collection',
                className: 'btn btn-label-primary dropdown-toggle me-2',
                text: '<i class="bx bx-export me-sm-2"></i>Export',
                buttons: [
                  { extend: 'copy', text: '<i class="bx bx-copy me-1"></i>Copy' },
                  { extend: 'csv', text: '<i class="bx bx-file me-1"></i>CSV' },
                  { extend: 'excel', text: '<i class="bx bx-spreadsheet me-1"></i>Excel' },
                  { extend: 'pdf', text: '<i class="bx bxs-file-pdf me-1"></i>PDF' },
                  { extend: 'print', text: '<i class="bx bx-printer me-1"></i>Print' }
                ]
              }
            ]),
        // Add button (hidden if hideAddButton = true)
        ...(config.permissions?.canAdd && !config.hideAddButton && config.addButton?.url
          ? [
              {
                text: `<i class="bx bx-plus me-sm-2"></i> ${config.addButton.label}`,
                className: 'btn btn-primary',
                action: function () {
                  window.location.href = config.addButton.url;
                }
              }
            ]
          : [])
      ]
    });

    /** Delete Action with SweetAlert **/
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    $(document).on('click', '.delete-journal', function (e) {
      e.preventDefault();
      const url = $(this).data('url');
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
      }).then(result => {
        if (result.isConfirmed) {
          $.ajax({
            url: url,
            type: 'DELETE',
            data: { _token: csrfToken },
            success: function (response) {
              if (response.success) {
                Swal.fire('Deleted!', response.message, 'success');
                dtInstance.ajax.reload(null, false);
              } else {
                Swal.fire('Failed!', response.message || 'Could not delete entry.', 'error');
              }
            },
            error: function (xhr) {
              Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong.', 'error');
            }
          });
        }
      });
    });
  }
});
