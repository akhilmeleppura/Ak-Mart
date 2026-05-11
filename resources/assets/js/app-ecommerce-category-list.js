/**
 * App eCommerce Category List
 */

'use strict';

// Comment editor

const commentEditor = document.querySelector('.comment-editor');

if (commentEditor) {
  var quill = new Quill(commentEditor, {
    modules: {
      toolbar: '.comment-toolbar'
    },
    placeholder: 'Write a Comment...',
    theme: 'snow'
  });

  // Sync quill to hidden input
  quill.on('text-change', function() {
    var hiddenDescription = document.getElementById('hiddenDescription');
    if(hiddenDescription) {
        hiddenDescription.value = quill.root.innerHTML;
    }
  });
}

// Datatable (js)
document.addEventListener('DOMContentLoaded', function (e) {
  var dt_category_list_table = document.querySelector('.datatables-category-list');

  //select2 for dropdowns in offcanvas

  var select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        dropdownParent: $this.parent(),
        placeholder: $this.data('placeholder') //for dynamic placeholder
      });
    });
  }

  // Customers List Datatable

  if (dt_category_list_table) {
    var dt_category = new DataTable(dt_category_list_table, {
      ajax: {
        url: baseUrl + 'app/ecommerce/product/category',
        data: function (d) {
          const dateRange = $('#dateRange').val();
          if (dateRange && dateRange.includes(' to ')) {
            const dates = dateRange.split(' to ');
            d.start_date = dates[0];
            d.end_date = dates[1];
          }
          // Get Active Tab for filtering
          const activeTab = $('.category-filter.active').data('filter');
          if (activeTab) {
            d.category_filter = activeTab;
          }
        }
      },
      columns: [
        // columns according to JSON
        { data: 'id' },
        { data: 'id', orderable: false, render: DataTable.render.select() },
        { data: 'categories' },
        { data: 'total_products' },
        { data: 'total_earnings' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 1,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          // For Checkboxes
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 4,
          checkboxes: true,
          render: function () {
            return '<input type="checkbox" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          targets: 2,
          responsivePriority: 2,
          render: function (data, type, full, meta) {
            const name = full['categories'];
            const categoryDetail = full['category_detail'];
            const image = full['cat_image'];
            const id = full['id'];
            let output;
            if (image) {
              // For Product image
              output = `<img src="${assetsPath}img/ecommerce-images/${image}" alt="Product-${id}" class="rounded">`;
            } else {
              // For Product badge
              const stateNum = Math.floor(Math.random() * 6);
              const states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
              const state = states[stateNum];
              const initials = (categoryDetail.match(/\b\w/g) || []).slice(0, 2).join('').toUpperCase();
              output = `<span class="avatar-initial rounded-2 bg-label-${state}">${initials}</span>`;
            }
            // Creates full output for Categories and Category Detail
            const rowOutput = `
              <div class="d-flex align-items-center">
                <div class="avatar-wrapper me-3 rounded-2 bg-label-secondary">
                  <div class="avatar">${output}</div>
                </div>
                <div class="d-flex flex-column justify-content-center">
                  <span class="text-heading text-wrap fw-medium">${name}</span>
                  <span class="text-truncate mb-0 d-none d-sm-block text-muted"><small>Parent: ${full['parent_name']}</small></span>
                  <span class="text-truncate mb-0 d-none d-sm-block"><small>${categoryDetail}</small></span>
                </div>
              </div>`;
            return rowOutput;
          }
        },
        {
          // Total products
          targets: 3,
          responsivePriority: 3,
          render: function (data, type, full, meta) {
            const total_products = full['total_products'];
            return '<div class="text-sm-end">' + total_products + '</div>';
          }
        },
        {
          // Total Earnings
          targets: 4,
          orderable: false,
          render: function (data, type, full, meta) {
            const total_earnings = full['total_earnings'];
            return "<div class='mb-0 text-sm-end'>" + total_earnings + '</div';
          }
        },
        {
          // Actions
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            let id = full['id'];
            return `
              <div class="d-flex align-items-sm-center justify-content-sm-center">
                <button class="btn btn-icon edit-record" data-id="${id}" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEcommerceCategoryList"><i class="icon-base bx bx-edit icon-md"></i></button>
                <button class="btn btn-icon delete-record" data-id="${id}"><i class="icon-base bx bx-trash icon-md text-danger"></i></button>
              </div>
            `;
          }
        }
      ],
      drawCallback: function (settings) {
        // Tab Filtering Logic
        $('.category-filter').off('click').on('click', function () {
          $('.category-filter').removeClass('active');
          $(this).addClass('active');
          dt_category.ajax.reload();
        });

        // Handle Edit Record
        $('.edit-record').off('click').on('click', function () {
          const id = $(this).data('id');
          const rowData = dt_category.row($(this).closest('tr')).data();
          
          // Populate Form
          $('#offcanvasEcommerceCategoryListLabel').html('Edit Category');
          $('.add-new.btn-primary').html('Save Changes');
          $('#categoryId').val(rowData.id);
          $('#ecommerce-category-title').val(rowData.categories);
          $('#ecommerce-category-slug').val(rowData.slug);
          $('#ecommerce-category-parent-category').val(rowData.parent_id).trigger('change');
          if (quill) quill.root.innerHTML = rowData.category_detail;
          
          // Change form action and method
          const form = $('#eCommerceCategoryListForm');
          form.attr('action', `${baseUrl}app/ecommerce/product/category/${rowData.id}`);
          $('#formMethod').val('PUT');
          
          // Update button text
          form.find('button[type="submit"]').html('Update');
        });

        // Handle Delete Record
        $('.delete-record').off('click').on('click', function () {
          const id = $(this).data('id');
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
              $.ajax({
                url: `${baseUrl}app/ecommerce/product/category/${id}`,
                type: 'DELETE',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (result) {
                  dt_category.ajax.reload();
                  Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: result.message,
                    customClass: {
                      confirmButton: 'btn btn-success'
                    }
                  });
                },
                error: function (error) {
                  console.log(error);
                  Swal.fire({
                    title: 'Error!',
                    text: 'Error deleting category',
                    icon: 'error',
                    customClass: {
                      confirmButton: 'btn btn-primary'
                    }
                  });
                }
              });
            }
          });
        });
      },
      select: {
        style: 'multi',
        selector: 'td:nth-child(2)'
      },
      order: [2, 'desc'],
      layout: {
        topStart: {
          rowClass: 'row m-3 my-0 justify-content-between',
          features: [
            {
              search: {
                placeholder: 'Search Category',
                text: '_INPUT_'
              }
            }
          ]
        },
        topEnd: {
          rowClass: 'row m-3 my-0 justify-content-between',
          features: {
            pageLength: {
              menu: [10, 25, 50, 100],
              text: '_MENU_'
            },
            buttons: [
              {
                extend: 'collection',
                className: 'btn btn-label-secondary dropdown-toggle me-3',
                text: '<span class="d-flex align-items-center gap-2"><i class="icon-base bx bx-export icon-xs"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
                buttons: [
                  {
                    extend: 'print',
                    text: `<i class="icon-base bx bx-printer me-2"></i>Print`,
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4] }
                  },
                  {
                    extend: 'csv',
                    text: `<i class="icon-base bx bx-file me-2"></i>Csv`,
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4] }
                  },
                  {
                    extend: 'excel',
                    text: `<i class="icon-base bx bxs-file-export me-2"></i>Excel`,
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4] }
                  },
                  {
                    extend: 'pdf',
                    text: `<i class="icon-base bx bxs-file-pdf me-2"></i>Pdf`,
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4] }
                  },
                  {
                    extend: 'copy',
                    text: `<i class="icon-base bx bx-copy me-2"></i>Copy`,
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4] }
                  }
                ]
              },
              {
                text: `<i class="icon-base bx bx-plus icon-sm me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add Category</span>`,
                className: 'add-new btn btn-primary',
                attr: {
                  'data-bs-toggle': 'offcanvas',
                  'data-bs-target': '#offcanvasEcommerceCategoryList'
                },
                action: function (e, dt, node, config) {
                  // Reset Form for New Category
                  $('#offcanvasEcommerceCategoryListLabel').html('Add Category');
                  const form = $('#eCommerceCategoryListForm');
                  form.attr('action', `${baseUrl}app/ecommerce/product/category`);
                  $('#formMethod').val('POST');
                  $('#categoryId').val('');
                  form[0].reset();
                  $('#ecommerce-category-parent-category').val('').trigger('change');
                  if (quill) quill.root.innerHTML = '';
                  form.find('button[type="submit"]').html('Add');
                  
                  // Open Offcanvas
                  const offCanvasElement = document.querySelector('#offcanvasEcommerceCategoryList');
                  const offCanvasInstance = bootstrap.Offcanvas.getInstance(offCanvasElement) || new bootstrap.Offcanvas(offCanvasElement);
                  offCanvasInstance.show();
                }
              }
            ]
          }
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
      // For responsive popup
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: function (row) {
              const data = row.data();
              return 'Details of ' + data['categories'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            const data = columns
              .map(function (col) {
                return col.title !== '' // Do not show row in modal popup if title is blank (for check box)
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
  // ? setTimeout used for category-list table initialization
  setTimeout(() => {
    const elementsToModify = [
      { selector: '.dt-buttons .btn', classToRemove: 'btn-secondary' },
      { selector: '.dt-search .form-control', classToRemove: 'form-control-sm', classToAdd: 'ms-0' },
      { selector: '.dt-search', classToAdd: 'mb-0 mb-md-6' },
      { selector: '.dt-length .form-select', classToRemove: 'form-select-sm' },
      { selector: '.dt-layout-table', classToRemove: 'row mt-2', classToAdd: 'border-top' },
      { selector: '.dt-layout-start', classToAdd: 'px-3 mt-0' },
      { selector: '.dt-layout-end', classToAdd: 'px-3 column-gap-2 mt-0 mb-md-0 mb-4' },
      { selector: '.dt-layout-full', classToAdd: 'table-responsive' }
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

  // Initialize Flatpickr
  const dateRangeInput = document.querySelector('#dateRange');
  if (dateRangeInput) {
    flatpickr(dateRangeInput, {
      mode: 'range',
      dateFormat: 'Y-m-d',
      onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
          if (typeof dt_category !== 'undefined') {
            dt_category.ajax.reload();
          }
        }
      }
    });
  }
});

//For form validation
(function () {
  const eCommerceCategoryListForm = document.getElementById('eCommerceCategoryListForm');

  //Add New customer Form Validation
  const fv = FormValidation.formValidation(eCommerceCategoryListForm, {
    fields: {
      categoryTitle: {
        validators: {
          notEmpty: {
            message: 'Please enter category title'
          }
        }
      },
      slug: {
        validators: {
          notEmpty: {
            message: 'Please enter slug'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: 'is-valid',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          return '.form-control-validation';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });
})();
