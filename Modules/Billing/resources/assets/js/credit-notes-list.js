'use strict';
document.addEventListener('DOMContentLoaded', function () {
  const dt_credit_note_table = document.querySelector('.credit-note-list-table');
  if (dt_credit_note_table) {
    const dt_credit_note = new DataTable(dt_credit_note_table, {
      ajax: baseUrl + 'accounting/billings/credit-notes/list',
      columns: [
        { data: 'credit_note_id' },
        { data: 'credit_note_id', orderable: false, render: DataTable.render.select() },
        { data: 'credit_note_id' },
        { data: 'credit_note_status' },
        { data: 'client_name' },
        { data: 'total' },
        { data: 'issued_date' },
        { data: 'balance' },
        { data: 'credit_note_status' },
        { data: 'action' }
      ],
      columnDefs: [
        {
          className: 'control',
          responsivePriority: 2,
          searchable: false,
          targets: 0,
          render: function () {
            return '';
          }
        },
        {
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 4,
          render: function () {
            return '<input type="checkbox" class="dt-checkboxes form-check-input">';
          }
        },
        {
          targets: 2,
          render: function (data, type, full) {
            const docPrefix = full['document_prefix'] || '';
            const docNumber = full['document_number'] || '';
            const displayNumber = docPrefix + docNumber;

            return `<a href="${baseUrl}accounting/billings/credit-notes/${full['credit_note_id']}">#${displayNumber}</a>`;
          }
        },
        {
          targets: 3,
          render: function (data, type, full) {
            const creditNoteStatus = full['credit_note_status'];
            const balance = full['balance'];
            const dueDate = full['due_date'] ? new Date(full['due_date']) : null;
            const formattedDate =
              dueDate && !isNaN(dueDate.getTime())
                ? dueDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                : 'Not set';
            const roleBadgeObj = {
              Sent: '<span class="badge p-1_5 rounded-pill bg-label-secondary"><i class="icon-base icon-16px bx bx-envelope"></i></span>',
              Draft:
                '<span class="badge p-1_5 rounded-pill bg-label-primary"><i class="icon-base icon-16px bx bx-folder"></i></span>',
              'Past Due':
                '<span class="badge p-1_5 rounded-pill bg-label-danger"><i class="icon-base icon-16px bx bx-error"></i></span>',
              'Partial Payment':
                '<span class="badge p-1_5 rounded-pill bg-label-success"><i class="icon-base icon-16px bx bx-check"></i></span>',
              Paid: '<span class="badge p-1_5 rounded-pill bg-label-warning"><i class="icon-base icon-16px bx bx-pie-chart-alt"></i></span>',
              Downloaded:
                '<span class="badge p-1_5 rounded-pill bg-label-info"><i class="icon-base icon-16px bx bx-down-arrow-alt"></i></span>'
            };
            const tooltipContent = `
              ${creditNoteStatus}<br>
              <span class="fw-medium">Balance:</span> ${balance}<br>
              <span class="fw-medium">Due Date:</span> ${formattedDate}
            `.replace(/"/g, '&quot;');
            return `
              <span class="d-inline-block" data-bs-toggle="tooltip" data-bs-html="true" title="<span>${tooltipContent}"> 
                ${roleBadgeObj[creditNoteStatus] || ''} 
              </span>
            `;
          }
        },
        {
          targets: 4,
          responsivePriority: 2,
          render: function (data, type, full) {
            const name = full['client_name'] || 'Unknown';
            const service = full['service'] || 'General Service';
            const image = full['avatar_image'] || false;
            const randNum = Math.floor(Math.random() * 11) + 1;
            const userImg = `${randNum}.png`;
            let output;
            if (image === true) {
              output = `<img src="${assetsPath}img/avatars/${userImg}" alt="Avatar" class="rounded-circle">`;
            } else {
              const stateNum = Math.floor(Math.random() * 6);
              const states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
              const state = states[stateNum];
              const initials = (name.match(/\b\w/g) || [])
                .slice(0, 2)
                .map(letter => letter.toUpperCase())
                .join('');
              output = `<span class="avatar-initial rounded-circle bg-label-${state}">${initials}</span>`;
            }
            return `
              <div class="d-flex justify-content-start align-items-center">
                <div class="avatar-wrapper">
                  <div class="avatar avatar-sm me-3">
                    ${output}
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <a href="${baseUrl}pages/profile-user" class="text-heading text-truncate"><span class="fw-medium">${name}</span></a>
                  <small class="text-truncate">${service}</small>
                </div>
              </div>
            `;
          }
        },
        {
          targets: 5,
          render: function (data, type, full) {
            const total = full['total'] || '0.00';
            return `<span class="d-none">${total}</span>$${total}`;
          }
        },
        {
          targets: 6,
          render: function (data, type, full) {
            try {
              const rawDate = full['issued_date'];
              if (!rawDate) {
                return '<span class="text-muted">Not set</span>';
              }
              const dateOnly = rawDate.split(' ')[0];
              const issueDate = new Date(dateOnly);
              if (isNaN(issueDate.getTime())) {
                return '<span class="text-muted">Invalid date</span>';
              }
              return `
                <span class="d-none">${issueDate.toISOString().slice(0, 10).replace(/-/g, '')}</span>
                ${issueDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}
              `;
            } catch (e) {
              console.error('Date formatting error:', e);
              return '<span class="text-muted">Invalid date</span>';
            }
          }
        },
        {
          targets: 7,
          orderable: false,
          render: function (data, type, full) {
            const balance = full['balance'] || '0.00';
            if (balance === 0 || balance === '0.00') {
              return '<span class="badge bg-label-success text-capitalized"> Paid </span>';
            } else {
              return `<span class="d-none">${balance}</span><span class="text-heading">${balance}</span>`;
            }
          }
        },
        {
          targets: 8,
          visible: false
        },
        {
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full) {
            return (
              '<div class="d-flex align-items-center">' +
              `<a href="javascript:;" data-bs-toggle="tooltip" class="btn btn-icon delete-record" data-id="${full['credit_note_id']}" data-bs-placement="top" title="Delete"><i class="icon-base bx bx-trash icon-md"></i></a>` +
              `<a href="${baseUrl}accounting/billings/credit-notes/${full['credit_note_id']}" data-bs-toggle="tooltip" class="btn btn-icon" data-bs-placement="top" title="Preview Credit Note"><i class="icon-base bx bx-show icon-md"></i></a>` +
              '<div class="dropdown">' +
              '<a href="javascript:;" class="btn dropdown-toggle hide-arrow btn-icon p-0" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded icon-md"></i></a>' +
              '<div class="dropdown-menu dropdown-menu-end">' +
              '<a href="javascript:;" class="dropdown-item">Download</a>' +
              `<a href="${baseUrl}accounting/billings/credit-notes/${full['credit_note_id']}/edit" class="dropdown-item">Edit</a>` +
              '<a href="javascript:;" class="dropdown-item">Duplicate</a>' +
              '</div>' +
              '</div>'
            );
          }
        }
      ],
      select: {
        style: 'multi',
        selector: 'td:nth-child(2)'
      },
      order: [[2, 'desc']],
      displayLength: 10,
      layout: {
        topStart: {
          rowClass: 'row m-3 justify-content-between',
          features: [
            {
              pageLength: {
                menu: [10, 25, 50, 100],
                text: 'Show_MENU_'
              },
              buttons: [
                {
                  text: '<i class="icon-base icon-16px bx bx-plus me-md-2"></i><span class="d-md-inline-block d-none">Create Credit Note</span>',
                  className: 'btn btn-primary',
                  action: function () {
                    window.location = baseUrl + 'accounting/billings/credit-notes/create';
                  }
                }
              ]
            }
          ]
        },
        topEnd: {
          rowClass: 'row mx-3 justify-content-between',
          features: [
            {
              search: {
                placeholder: 'Search Credit Note',
                text: '_INPUT_'
              }
            }
          ]
        },
        bottomStart: {
          rowClass: 'row mx-3 justify-content-between',
          features: ['info']
        },
        bottomEnd: 'paging'
      },
      language: {
        paginate: {
          next: '<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-18px"></i>',
          previous: '<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-18px"></i>',
          first: '<i class="icon-base bx bx-chevrons-left scaleX-n1-rtl icon-18px"></i>',
          last: '<i class="icon-base bx bx-chevrons-right scaleX-n1-rtl icon-18px"></i>'
        }
      },
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: function (row) {
              const data = row.data();
              return 'Details of ' + data['client_name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            const data = columns
              .map(function (col) {
                return col.title !== ''
                  ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                      <td>${col.title}:</td>
                      <td>${col.data}</td>
                    </tr>`
                  : '';
              })
              .join('');
            if (data) {
              const div = document.createElement('div');
              div.classList.add('table-responsive');
              const table = document.createElement('table');
              div.appendChild(table);
              table.classList.add('table');
              const tbody = document.createElement('tbody');
              tbody.innerHTML = data;
              table.appendChild(tbody);
              return div;
            }
            return false;
          }
        }
      },
      initComplete: function () {
        let creditNoteStatusContainer = document.querySelector('.credit_note_status');
        if (!creditNoteStatusContainer) {
          creditNoteStatusContainer = document.createElement('div');
          creditNoteStatusContainer.className = 'credit_note_status';
          const filterArea = document.querySelector('.dt-layout-end');
          if (filterArea) {
            filterArea.appendChild(creditNoteStatusContainer);
          }
        }
        this.api()
          .columns(8)
          .every(function () {
            const column = this;
            const select = document.createElement('select');
            select.id = 'UserRole';
            select.className = 'form-select';
            select.innerHTML = '<option value=""> Credit Note Status </option>';
            creditNoteStatusContainer.appendChild(select);
            select.addEventListener('change', function () {
              const val = select.value ? `^${select.value}$` : '';
              column.search(val, true, false).draw();
            });
            column
              .data()
              .unique()
              .sort()
              .each(function (d) {
                const option = document.createElement('option');
                option.value = d;
                option.className = 'text-capitalize';
                option.textContent = d;
                select.appendChild(option);
              });
          });
      }
    });

    // DELETE function with SweetAlert + AJAX call
    function handleDelete(creditNoteId, row, modal = null) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'This credit note will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
      }).then(result => {
        if (result.isConfirmed) {
          fetch(`${baseUrl}accounting/billings/credit-notes/${creditNoteId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
            .then(res => res.json())
            .then(data => {
              Swal.fire('Deleted!', data.message || 'Credit note has been deleted.', 'success');
              dt_credit_note.ajax.reload();
              if (modal) {
                const closeButton = modal.querySelector('.btn-close');
                if (closeButton) closeButton.click();
              }
            })
            .catch(() => {
              Swal.fire('Error!', 'Something went wrong while deleting.', 'error');
            });
        }
      });
    }

    // Binding delete button events
    function bindDeleteEvent() {
      const creditNoteTable = document.querySelector('.credit-note-list-table');
      const modal = document.querySelector('.dtr-bs-modal');
      if (creditNoteTable && creditNoteTable.classList.contains('collapsed')) {
        if (modal) {
          modal.addEventListener('click', function (event) {
            if (event.target.closest('.delete-record')) {
              const btn = event.target.closest('.delete-record');
              const creditNoteId = btn.getAttribute('data-id');
              const tooltipInstance = bootstrap.Tooltip.getInstance(btn);
              if (tooltipInstance) tooltipInstance.dispose();
              handleDelete(creditNoteId, null, modal);
            }
          });
        }
      } else {
        const tableBody = creditNoteTable?.querySelector('tbody');
        if (tableBody) {
          tableBody.addEventListener('click', function (event) {
            if (event.target.closest('.delete-record')) {
              const btn = event.target.closest('.delete-record');
              const creditNoteId = btn.getAttribute('data-id');
              const row = btn.closest('tr');
              const tooltipInstance = bootstrap.Tooltip.getInstance(btn);
              if (tooltipInstance) tooltipInstance.dispose();
              handleDelete(creditNoteId, row);
            }
          });
        }
      }
    }

    bindDeleteEvent();
    document.addEventListener('show.bs.modal', function (event) {
      if (event.target.classList.contains('dtr-bs-modal')) {
        bindDeleteEvent();
      }
    });
    document.addEventListener('hide.bs.modal', function (event) {
      if (event.target.classList.contains('dtr-bs-modal')) {
        bindDeleteEvent();
      }
    });

    dt_credit_note.on('draw', function () {
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      tooltipTriggerList.forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl, {
          boundary: document.body
        });
      });
    });
  }

  setTimeout(() => {
    const elementsToModify = [
      { selector: '.dt-buttons .btn', classToRemove: 'btn-secondary' },
      { selector: '.dt-buttons ', classToAdd: 'd-block mb-0 w-auto', classToRemove: 'flex-wrap' },
      { selector: '.dt-length', classToAdd: 'd-flex align-items-center mx-2 my-md-5 my-0' },
      { selector: '.dt-length .form-select', classToRemove: 'form-select-sm' },
      { selector: '.dt-search', classToAdd: 'me-sm-0 me-4' },
      { selector: '.dt-layout-end .dt-search .form-control', classToRemove: 'form-control-sm' },
      {
        selector: '.dt-layout-end',
        classToRemove: 'justify-content-between ms-auto',
        classToAdd:
          'justify-content-md-between justify-content-center d-flex flex-wrap gap-sm-4 mb-sm-0 mb-5 mt-0 pe-md-3 ps-0'
      },
      {
        selector: '.dt-layout-start',
        classToRemove: 'd-md-flex justify-content-between',
        classToAdd: 'px-3 pe-md-0 mt-0 d-flex justify-content-md-between justify-content-center mt-md-0 mt-5'
      },
      { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
      { selector: '.dt-layout-full', classToRemove: 'col-md col-12', classToAdd: 'table-responsive' }
    ];

    elementsToModify.forEach(({ selector, classToRemove, classToAdd }) => {
      document.querySelectorAll(selector).forEach(element => {
        if (classToRemove) {
          classToRemove.split(' ').forEach(className => element.classList.remove(className));
        }
        if (classToAdd) {
          classToAdd.split(' ').forEach(className => element.classList.add(className));
        }
      });
    });
  }, 100);
});
