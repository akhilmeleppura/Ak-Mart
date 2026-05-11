/**
 * User Management - DataTable Logic
 */
'use strict';

// Column Renderer Helpers
function columnTypeRenderer(type, data) {
  switch (type) {
    case 'email':
      return renderEmail(data);
    case 'avatar':
      return renderAvatar(data);
    case 'name':
      return renderName(data);
    case 'verification':
      return renderVerification(data);
    case 'nameWithAvatar':
      return renderNameWithAvatar(data);
    case 'text':
      return renderText(data);

    default:
      return `<span style="color: red;">404: Column type "${type}" does not exist</span>`;
  }
}

function renderText(data) {
  return `<span>${data.id}</span>`;
}

function renderNameWithAvatar(data) {
  const avatar = columnTypeRenderer('avatar', data);
  const name = columnTypeRenderer('name', data);
  return `
    <div class="d-flex justify-content-start align-items-center user-name">
      <div class="avatar-wrapper">
        <div class="avatar avatar-sm me-4">
          ${avatar}
        </div>
      </div>
      <div class="d-flex flex-column">
        <a href="${baseUrl + 'app/user/view/account'}" class="text-truncate text-heading">
          ${name}
        </a>
      </div>
    </div>
  `;
}

function renderEmail(data) {
  return `<span class="user-email">${data.email}</span>`;
}

function renderAvatar(data) {
  const initials = (data.name.match(/\b\w/g) || []).join('').toUpperCase().slice(0, 2);
  const state = ['success', 'danger', 'warning', 'info', 'dark', 'primary'][Math.floor(Math.random() * 6)];
  return `<span class="avatar-initial rounded-circle bg-label-${state}">${initials}</span>`;
}

function renderName(data) {
  return `<span class="fw-medium">${data.name}</span>`;
}

function renderVerification(data) {
  const verified = data.email_verified_at;
  return verified
    ? '<i class="icon-base bx fs-4 bx-check-shield text-success"></i>'
    : '<i class="icon-base bx fs-4 bx-shield-x text-danger"></i>';
}

document.addEventListener('DOMContentLoaded', function () {
  const dt_user_table = document.querySelector('.datatables-users');

  if (dt_user_table && window.standardDataTableConfig) {
    const tableConfig = window.standardDataTableConfig || {};
    const columnRenderConfig = tableConfig.table || {};
    const ajaxUrl = tableConfig.otherConfig?.ajaxUrl || 'user-list';

    const tableHeaders = Object.entries(columnRenderConfig).map(([key, config]) => {
      return { data: config.dbColumn || key };
    });

    let columnDefs = [
      {
        className: 'control',
        searchable: false,
        orderable: false,
        responsivePriority: 2,
        targets: 0,
        render: function () {
          return '';
        }
      }
    ];

    tableHeaders.forEach((header, index) => {
      const key = Object.keys(columnRenderConfig).find(k => columnRenderConfig[k].dbColumn === header.data);

      const config = columnRenderConfig[key];

      if (config && config.type) {
        columnDefs.push({
          targets: index + 1,
          render: function (data, type, full, meta) {
            return columnTypeRenderer(config.type, full);
          },
          ...(config.responsivePriority && { responsivePriority: config.responsivePriority }),
          ...(config.className && { className: config.className }),
          searchable: false,
          orderable: false
        });
      }
    });

    columnDefs.push({
      targets: -1,
      title: 'Actions',
      searchable: false,
      orderable: false,
      render: function (data, type, full, meta) {
        return `
          <div class="d-flex align-items-center gap-4">
            <button class="btn btn-sm btn-icon edit-record" data-id="${full.id}" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="icon-base bx bx-edit icon-22px"></i></button>
            <button class="btn btn-sm btn-icon delete-record" data-id="${full.id}"><i class="icon-base bx bx-trash icon-22px"></i></button>
            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded icon-22px"></i></button>
            <div class="dropdown-menu dropdown-menu-end m-0">
              <a href="${baseUrl + 'app/user/view/account'}" class="dropdown-item">View</a>
              <a href="javascript:;" class="dropdown-item">Suspend</a>
            </div>
          </div>
        `;
      }
    });

    const dt_user = new DataTable(dt_user_table, {
      processing: true,
      serverSide: true,
      ajax: {
        url: baseUrl + ajaxUrl,
        dataSrc: function (json) {
          if (typeof json.recordsTotal !== 'number') json.recordsTotal = 0;
          if (typeof json.recordsFiltered !== 'number') json.recordsFiltered = 0;
          json.data = Array.isArray(json.data) ? json.data : [];
          return json.data;
        }
      },
      columns: [{ data: 'id' }, ...tableHeaders, { data: null }],
      columnDefs: columnDefs,
      order: [[2, 'desc']],
      dom: '<"row mx-2"<"col-md-6"<"me-3"l>><"col-md-6"<"dt-action-buttons text-end d-flex align-items-center justify-content-end gap-2"fB>>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      language: {
        sLengthMenu: 'Show MENU',
        search: '',
        searchPlaceholder: 'Search..'
      },
      buttons: [
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
        },
        {
          text: '<i class="bx bx-plus me-sm-2"></i> Add User',
          className: 'add-new btn btn-primary',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAddUser'
          }
        }
      ],
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== ''
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');
            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      },
      initComplete: function () {
        this.api()
          .columns()
          .every(function () {
            var column = this;
            var select = $('<select class="form-select text-nowrap"><option value="">Select Item</option></select>')
              .appendTo($(column.footer()).empty())
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });

            column
              .data()
              .unique()
              .sort()
              .each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>');
              });
          });

        setTimeout(() => {
          $('.dataTables_filter .form-control').removeClass('form-control-sm');
          $('.dataTables_length .form-select').removeClass('form-select-sm');
        }, 300);
      }
    });
  }
});
