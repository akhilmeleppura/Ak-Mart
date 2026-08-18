/**
 * app-ecommerce-product-list
 */

'use strict';

// Datatable (js)
document.addEventListener('DOMContentLoaded', function (e) {
  let borderColor, bodyBg, headingColor;

  borderColor = config.colors.borderColor;
  bodyBg = config.colors.bodyBg;
  headingColor = config.colors.headingColor;

  // Variable declaration for table
  const dt_product_table = document.querySelector('.datatables-products'),
    productAdd = baseUrl + 'products/create',
    statusObj = {
      1: { title: 'Scheduled', class: 'bg-label-warning' },
      2: { title: 'Publish', class: 'bg-label-success' },
      3: { title: 'Inactive', class: 'bg-label-danger' }
    },
    categoryObj = {
      0: { title: 'Household' },
      1: { title: 'Office' },
      2: { title: 'Electronics' },
      3: { title: 'Shoes' },
      4: { title: 'Accessories' },
      5: { title: 'Game' }
    },
    stockObj = {
      0: { title: 'Out_of_Stock' },
      1: { title: 'In_Stock' }
    },
    stockFilterValObj = {
      0: { title: 'Out of Stock' },
      1: { title: 'In Stock' }
    };

  // E-commerce Products datatable

  if (dt_product_table) {
    var dt_products = new DataTable(dt_product_table, {
      ajax: {
        url: baseUrl + 'products',
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
        // columns according to JSON
        { data: 'id' },
        { data: 'id', orderable: false, render: DataTable.render.select() },
        { data: 'product_name' },
        { data: 'category' },
        { data: 'stock' },
        { data: 'sku' },
        { data: 'price' },
        { data: 'quantity' },
        { data: 'branch_name' },
        { data: 'status' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
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
          responsivePriority: 3,
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
          responsivePriority: 1,
          render: function (data, type, full, meta) {
            let name = full['product_name'],
              id = full['id'],
              productBrand = full['product_brand'],
              image = full['image'];

            let output;

            if (image) {
              // For Product image
              output = `<img src="${assetsPath}img/ecommerce-images/${image}" alt="Product-${id}" class="rounded">`;
            } else {
              // For Product badge
              let stateNum = Math.floor(Math.random() * 6);
              let states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
              let state = states[stateNum];
              let initials = (productBrand.match(/\b\w/g) || []).slice(0, 2).join('').toUpperCase();

              output = `<span class="avatar-initial rounded-2 bg-label-${state}">${initials}</span>`;
            }

            // Creates full output for Product name and product_brand
            let rowOutput = `
              <div class="d-flex justify-content-start align-items-center product-name">
                <div class="avatar-wrapper">
                  <div class="avatar avatar me-2 me-sm-4 rounded-2 bg-label-secondary">${output}</div>
                </div>
                <div class="d-flex flex-column">
                  <h6 class="text-nowrap mb-0">${name}</h6>
                  <small class="text-truncate d-none d-sm-block">${productBrand}</small>
                </div>
              </div>
            `;

            return rowOutput;
          }
        },
        {
          targets: 3,
          responsivePriority: 5,
          render: function (data, type, full, meta) {
            let category = full['category'];

            if (type === 'display') {
              let categoryBadgeObj = {
                Household: 'bx-briefcase',
                Office: 'bx-home-smile',
                Electronics: 'bx-headphone',
                Shoes: 'bx-walk',
                Accessories: 'bxs-watch',
                Game: 'bx-laptop',
                Computer: 'bx-laptop',
                Mobile: 'bx-mobile-alt',
                Fashion: 'bx-closet'
              };
              
              let icon = categoryBadgeObj[category] || 'bx-cube';
              let state = ['warning', 'info', 'danger', 'success', 'secondary', 'primary'][Math.floor(Math.random() * 6)];

              return `
                <span class="text-truncate d-flex align-items-center text-heading">
                  <span class="w-px-30 h-px-30 rounded-circle d-flex justify-content-center align-items-center bg-label-${state} me-4">
                    <i class="icon-base bx ${icon} icon-18px"></i>
                  </span>
                  ${category}
                </span>`;
            } else {
              return category;
            }
          }
        },
        {
          targets: 4,
          orderable: false,
          responsivePriority: 3,
          render: function (data, type, full, meta) {
            let stock = full['stock'];
            let stockTitle = stockObj[stock].title;

            if (type === 'display') {
              let stockSwitchObj = {
                Out_of_Stock: `
                  <label class="switch switch-primary switch-sm">
                  <input type="checkbox" class="switch-input" id="switch">
                    <span class="switch-toggle-slider">
                      <span class="switch-off"></span>
                    </span>
                  </label>`,
                In_Stock: `
                  <label class="switch switch-primary switch-sm">
                    <input type="checkbox" class="switch-input" checked>
                    <span class="switch-toggle-slider">
                      <span class="switch-on"></span>
                    </span>
                  </label>`
              };

              return `
                <span class="text-truncate">
                  ${stockSwitchObj[stockTitle]}
                  <span class="d-none">${stockTitle}</span>
                </span>`;
            } else {
              return stockTitle;
            }
          }
        },
        {
          // Sku
          targets: 5,
          render: function (data, type, full, meta) {
            const sku = full['sku'];

            return '<span>' + sku + '</span>';
          }
        },
        {
          // price
          targets: 6,
          render: function (data, type, full, meta) {
            const price = full['price'];

            return '<span>' + price + '</span>';
          }
        },
        {
          // qty
          targets: 7,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            const qty = full['qty'];

            return '<span>' + qty + '</span>';
          }
        },
        {
          // Branch
          targets: 8,
          render: function (data, type, full, meta) {
            const branch = full['branch_name'];
            return '<span class="text-nowrap">' + branch + '</span>';
          }
        },
        {
          // Status
          targets: 9,
          render: function (data, type, full, meta) {
            const status = full['status'];

            return (
              '<span class="badge ' +
              statusObj[status].class +
              '" text-capitalized>' +
              statusObj[status].title +
              '</span>'
            );
          }
        },
        {
          targets: 10,
          title: 'Actions',
          searchable: false,
          orderable: false,
          responsivePriority: 1,
          render: function (data, type, full, meta) {
            let id = full['id'];
            return `
              <div class="d-inline-block text-nowrap">
                <a href="${baseUrl}products/${id}/edit" class="btn btn-icon"><i class="icon-base bx bx-edit icon-md"></i></a>
                <button class="btn btn-icon delete-record" data-id="${id}"><i class="icon-base bx bx-trash icon-md text-danger"></i></button>
              </div>
            `;
          }
        }
      ],
      drawCallback: function (settings) {
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
                url: `${baseUrl}products/${id}`,
                type: 'DELETE',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (result) {
                  dt_products.ajax.reload();
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
                    text: 'Error deleting product',
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
      order: [2, 'asc'],
      layout: {
        topStart: {
          rowClass: 'card-header d-flex border-top rounded-0 flex-wrap py-0 flex-column flex-md-row align-items-start',
          features: [
            {
              search: {
                className: 'me-5 ms-n4 pe-5 mb-n6 mb-md-0',
                placeholder: 'Search Product',
                text: '_INPUT_'
              }
            }
          ]
        },
        topEnd: {
          rowClass: 'row m-3 my-0 justify-content-between',
          features: [
            {
              pageLength: {
                menu: [10, 25, 50, 100],
                text: '_MENU_'
              },
              buttons: [
                {
                  extend: 'collection',
                  className: 'btn btn-label-secondary dropdown-toggle me-4',
                  text: '<span class="d-flex align-items-center gap-2"><i class="icon-base bx bx-export icon-xs"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
                  buttons: [
                    {
                      extend: 'print',
                      text: `<span class="d-flex align-items-center"><i class="icon-base bx bx-printer me-1"></i>Print</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;

                            // Check if inner is HTML content
                            if (inner.indexOf('<') > -1) {
                              const parser = new DOMParser();
                              const doc = parser.parseFromString(inner, 'text/html');

                              // Get all text content
                              let text = '';

                              // Handle specific elements
                              const userNameElements = doc.querySelectorAll('.product-name');
                              if (userNameElements.length > 0) {
                                userNameElements.forEach(el => {
                                  // Get text from nested structure
                                  const nameText =
                                    el.querySelector('.fw-medium')?.textContent ||
                                    el.querySelector('.d-block')?.textContent ||
                                    el.textContent;
                                  text += nameText.trim() + ' ';
                                });
                              } else {
                                // Get regular text content
                                text = doc.body.textContent || doc.body.innerText;
                              }

                              return text.trim();
                            }

                            return inner;
                          }
                        }
                      },
                      customize: function (win) {
                        win.document.body.style.color = config.colors.headingColor;
                        win.document.body.style.borderColor = config.colors.borderColor;
                        win.document.body.style.backgroundColor = config.colors.bodyBg;
                        const table = win.document.body.querySelector('table');
                        table.classList.add('compact');
                        table.style.color = 'inherit';
                        table.style.borderColor = 'inherit';
                        table.style.backgroundColor = 'inherit';
                      }
                    },
                    {
                      extend: 'csv',
                      text: `<span class="d-flex align-items-center"><i class="icon-base bx bx-file me-1"></i>Csv</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;

                            // Parse HTML content
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(inner, 'text/html');

                            let text = '';

                            // Handle product-name elements specifically
                            const userNameElements = doc.querySelectorAll('.product-name');
                            if (userNameElements.length > 0) {
                              userNameElements.forEach(el => {
                                // Get text from nested structure - try different selectors
                                const nameText =
                                  el.querySelector('.fw-medium')?.textContent ||
                                  el.querySelector('.d-block')?.textContent ||
                                  el.textContent;
                                text += nameText.trim() + ' ';
                              });
                            } else {
                              // Handle other elements (status, role, etc)
                              text = doc.body.textContent || doc.body.innerText;
                            }

                            return text.trim();
                          }
                        }
                      }
                    },
                    {
                      extend: 'excel',
                      text: `<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-export me-1"></i>Excel</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;

                            // Parse HTML content
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(inner, 'text/html');

                            let text = '';

                            // Handle product-name elements specifically
                            const userNameElements = doc.querySelectorAll('.product-name');
                            if (userNameElements.length > 0) {
                              userNameElements.forEach(el => {
                                // Get text from nested structure - try different selectors
                                const nameText =
                                  el.querySelector('.fw-medium')?.textContent ||
                                  el.querySelector('.d-block')?.textContent ||
                                  el.textContent;
                                text += nameText.trim() + ' ';
                              });
                            } else {
                              // Handle other elements (status, role, etc)
                              text = doc.body.textContent || doc.body.innerText;
                            }

                            return text.trim();
                          }
                        }
                      }
                    },
                    {
                      extend: 'pdf',
                      text: `<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-pdf me-1"></i>Pdf</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;

                            // Parse HTML content
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(inner, 'text/html');

                            let text = '';

                            // Handle product-name elements specifically
                            const userNameElements = doc.querySelectorAll('.product-name');
                            if (userNameElements.length > 0) {
                              userNameElements.forEach(el => {
                                // Get text from nested structure - try different selectors
                                const nameText =
                                  el.querySelector('.fw-medium')?.textContent ||
                                  el.querySelector('.d-block')?.textContent ||
                                  el.textContent;
                                text += nameText.trim() + ' ';
                              });
                            } else {
                              // Handle other elements (status, role, etc)
                              text = doc.body.textContent || doc.body.innerText;
                            }

                            return text.trim();
                          }
                        }
                      }
                    },
                    {
                      extend: 'copy',
                      text: `<i class="icon-base bx bx-copy me-1"></i>Copy`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;

                            // Parse HTML content
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(inner, 'text/html');

                            let text = '';

                            // Handle product-name elements specifically
                            const userNameElements = doc.querySelectorAll('.product-name');
                            if (userNameElements.length > 0) {
                              userNameElements.forEach(el => {
                                // Get text from nested structure - try different selectors
                                const nameText =
                                  el.querySelector('.fw-medium')?.textContent ||
                                  el.querySelector('.d-block')?.textContent ||
                                  el.textContent;
                                text += nameText.trim() + ' ';
                              });
                            } else {
                              // Handle other elements (status, role, etc)
                              text = doc.body.textContent || doc.body.innerText;
                            }

                            return text.trim();
                          }
                        }
                      }
                    }
                  ]
                },
                {
                  text: '<i class="icon-base bx bx-plus me-0 me-sm-1 icon-xs"></i><span class="d-none d-sm-inline-block">Add Product</span>',
                  className: 'add-new btn btn-primary',
                  action: function () {
                    window.location.href = productAdd;
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
      // For responsive popup
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: function (row) {
              const data = row.data();
              return 'Details of ' + data['product_name'];
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
      },
      initComplete: function () {
        const api = this.api();

        // Adding status filter once table is initialized
        api.columns(-2).every(function () {
          const column = this;
          const select = document.createElement('select');
          select.id = 'ProductStatus';
          select.className = 'form-select text-capitalize';
          select.innerHTML = '<option value="">Status</option>';

          document.querySelector('.product_status').appendChild(select);

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
              option.value = statusObj[d].title;
              option.textContent = statusObj[d].title;
              select.appendChild(option);
            });
        });

        // Adding category filter once table is initialized
        api.columns(3).every(function () {
          const column = this;
          const select = document.createElement('select');
          select.id = 'ProductCategory';
          select.className = 'form-select text-capitalize';
          select.innerHTML = '<option value="">Category</option>';

          document.querySelector('.product_category').appendChild(select);

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
              option.textContent = d;
              select.appendChild(option);
            });
        });

        // Adding stock filter once table is initialized
        api.columns(4).every(function () {
          const column = this;
          const select = document.createElement('select');
          select.id = 'ProductStock';
          select.className = 'form-select text-capitalize';
          select.innerHTML = '<option value="">Stock</option>';

          document.querySelector('.product_stock').appendChild(select);

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
              option.value = stockObj[d].title;
              option.textContent = stockObj[d].title;
              select.appendChild(option);
            });
        });
      }
    });
  }

  // Filter form control to default size
  // ? setTimeout used for product-list table initialization
  setTimeout(() => {
    const elementsToModify = [
      { selector: '.dt-buttons .btn', classToRemove: 'btn-secondary' },
      { selector: '.dt-search .form-control', classToRemove: 'form-control-sm', classToAdd: 'ms-0' },
      { selector: '.dt-search', classToAdd: 'mb-0 mb-md-6' },
      { selector: '.dt-length .form-select', classToRemove: 'form-select-sm' },
      { selector: '.dt-length', classToAdd: 'mb-md-6 mb-4' },
      { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
      { selector: '.dt-layout-start', classToAdd: 'mt-0' },
      {
        selector: '.dt-layout-end',
        classToRemove: 'justify-content-between',
        classToAdd: 'justify-content-md-between justify-content-center d-flex flex-wrap gap-2 mb-md-0 mb-4 mt-0'
      },
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

  // Initialize Flatpickr
  const dateRangeInput = document.querySelector('#dateRange');
  if (dateRangeInput) {
    flatpickr(dateRangeInput, {
      mode: 'range',
      dateFormat: 'Y-m-d',
      onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
          if (typeof dt_products !== 'undefined') {
            dt_products.ajax.reload();
          }
        }
      }
    });
  }
});
