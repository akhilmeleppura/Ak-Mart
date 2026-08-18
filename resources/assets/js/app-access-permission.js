/**
 * App user list (js)
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  const dataTablePermissions = document.querySelector('.datatables-permissions'),
    userList = baseUrl + 'app/user/list';
  let dt_permission;

  // Users List datatable
  if (dataTablePermissions) {
    dt_permission = new DataTable(dataTablePermissions, {
      ajax: baseUrl + 'permissions/data',
      columns: [
        // columns according to JSON
        { data: 'id' },
        { data: 'id' },
        { data: 'name' },
        { data: 'assigned_to' },
        { data: 'created_date' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          orderable: false,
          searchable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          targets: 1,
          searchable: false,
          visible: false
        },
        {
          // Name
          targets: 2,
          render: function (data, type, full, meta) {
            let name = full['name'];
            return '<span class="text-nowrap text-heading">' + name + '</span>';
          }
        },
        {
          // User Role
          targets: 3,
          orderable: false,
          render: function (data, type, full, meta) {
            const assignedTo = full['assigned_to'];
            let output = '';
            const roleBadgeObj = {
              Admin: `<a href="${userList}"><span class="badge bg-label-primary me-4">Administrator</span></a>`,
              Manager: `<a href="${userList}"><span class="badge bg-label-warning me-4">Manager</span></a>`,
              Users: `<a href="${userList}"><span class="badge bg-label-success me-4">Users</span></a>`,
              Support: `<a href="${userList}"><span class="badge bg-label-info me-4">Support</span></a>`,
              Restricted: `<a href="${userList}"><span class="badge bg-label-danger me-4">Restricted User</span></a>`
            };

            assignedTo.forEach(role => {
              output += roleBadgeObj[role] || `<a href="${userList}"><span class="badge bg-label-secondary me-4">${role}</span></a>`;
            });

            return `<span class="text-nowrap">${output}</span>`;
          }
        },
        {
          // remove ordering from Name
          targets: 4,
          orderable: false,
          render: function (data, type, full, meta) {
            let date = full['created_date'];
            return '<span class="text-nowrap">' + date + '</span>';
          }
        },
        {
          // Actions
          targets: -1,
          searchable: false,
          title: 'Actions',
          orderable: false,
          render: function (data, type, full, meta) {
            let id = full['id'];
            return `
              <div class="d-flex align-items-center">
                <span class="text-nowrap">
                  <button class="btn btn-icon me-1 edit-permission" data-id="${id}" data-bs-target="#editPermissionModal" data-bs-toggle="modal">
                    <i class="icon-base bx bx-edit icon-md"></i>
                  </button>
                  <button class="btn btn-icon delete-permission" data-id="${id}">
                    <i class="icon-base bx bx-trash icon-md"></i>
                  </button>
                </span>
              </div>
            `;
          }
        }
      ],
      order: [[1, 'asc']],
      layout: {
        topStart: {
          rowClass: 'row m-3 my-0 justify-content-between',
          features: [
            {
              pageLength: {
                menu: [10, 25, 50, 100],
                text: 'Show_MENU_'
              }
            }
          ]
        },
        topEnd: {
          features: [
            {
              search: {
                placeholder: 'Search Permission',
                text: '_INPUT_'
              }
            },
            {
              buttons: [
                {
                  text: `<i class="icon-base bx bx-plus icon-xs me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add Permission</span>`,
                  className: 'add-new btn btn-primary',
                  attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#addPermissionModal'
                  }
                }
              ]
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
              return 'Details of ' + data['name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            const data = columns
              .map(function (col) {
                return col.title !== '' //? Do not show row in modal popup if title is blank (for check box)
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
      }
    });
  }

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    const elementsToModify = [
      { selector: '.dt-buttons .btn', classToRemove: 'btn-secondary' },
      { selector: '.dt-search', classToAdd: 'me-4' },
      { selector: '.dt-search .form-control', classToRemove: 'form-control-sm' },
      { selector: '.dt-length', classToAdd: 'mb-0 mb-md-5' },
      { selector: '.dt-length .form-select', classToRemove: 'form-select-sm' },
      { selector: '.dt-buttons', classToAdd: 'mb-0 w-auto' },
      { selector: '.dt-layout-start', classToAdd: 'mt-0 px-5' },
      {
        selector: '.dt-layout-end',
        classToAdd: 'justify-content-md-between justify-content-center d-flex',
        classToRemove: 'justify-content-between d-md-flex'
      },
      { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
      { selector: '.dt-layout-full', classToRemove: 'col-md col-12', classToAdd: 'table-responsive' }
    ];

    // Delete record
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

  // Delete Permission
  $(document).on('click', '.delete-permission', function () {
    var permission_id = $(this).data('id'),
      dName = $(this).closest('tr').find('td:nth-child(2)').text();

    // sweetalert for confirmation of delete
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        // delete the data
        $.ajax({
          type: 'DELETE',
          url: `${baseUrl}app/access-permission/${permission_id}/delete`,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function () {
            dt_permission.ajax.reload();
            // success alert
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: 'The permission has been deleted.',
              customClass: {
                confirmButton: 'btn btn-success'
              }
            });
          },
          error: function (error) {
            console.log(error);
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
          title: 'Cancelled',
          text: 'The permission is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });
});
