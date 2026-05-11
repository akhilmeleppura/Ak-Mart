 $(function () {
  'use strict';

  // Variables
  var dt_customer_table = $('.datatables-customers'),
    customer_type_filter = $('#customer-type-filter'),
    customer_status_filter = $('#customer-status-filter'),
    add_customer_form = $('#addNewCustomerForm');

  // Customers datatable
  if (dt_customer_table.length) {
    var dt_customer = dt_customer_table.DataTable({
      processing: true,
      serverSide: false,
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      responsive: true,
      // Use existing HTML table data instead of AJAX
      initComplete: function () {
        // Initialize filters
        customer_type_filter.on('change', function () {
          var val = $(this).val();
          dt_customer.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
        });

        customer_status_filter.on('change', function () {
          var val = $(this).val();
          dt_customer.column(6).search(val ? '^' + val + '$' : '', true, false).draw();
        });
      },
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Customer'
      }
    });
  }

  // Filter form control to default size
  // ================================
  $('.dataTables_filter .form-control').removeClass('form-control-sm');
  $('.dataTables_length .form-select').removeClass('form-select-sm');

  // Handle delete form submission
  $(document).on('submit', '.delete-form', function(e) {
    e.preventDefault();
    var form = $(this);
    var url = form.attr('action');
    var token = form.find('input[name="_token"]').val();
    var method = form.find('input[name="_method"]').val();
    var customerName = form.closest('tr').find('td:eq(2)').text();

    // Show confirmation dialog
    Swal.fire({
      title: 'Delete Customer?',
      html: `Are you sure you want to delete <strong>${customerName}</strong>?<br><br>You won't be able to revert this!`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: 'POST',
          data: {
            '_token': token,
            '_method': method
          },
          beforeSend: function() {
            Swal.fire({
              title: 'Deleting...',
              text: 'Please wait while we delete the customer.',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
          },
          success: function(response) {
            // Remove the row from the table
            var row = form.closest('tr');
            dt_customer.row(row).remove().draw();
            
            // Show success message
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: response.message || 'Customer has been deleted.',
              timer: 2000,
              showConfirmButton: false,
              position: 'top-end',
              toast: true
            });
          },
          error: function(xhr) {
            // Show error message
            var errorMessage = 'An error occurred while deleting the customer.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
              errorMessage = xhr.responseJSON.error;
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: errorMessage,
              confirmButtonColor: '#3085d6'
            });
          }
        });
      }
    });
  });

  // Handle toggle status
  $(document).on('click', '.toggle-status', function(e) {
    e.preventDefault();
    var element = $(this);
    var url = element.data('url');
    var currentStatus = element.data('current-status');
    var customerName = element.closest('tr').find('td:eq(2)').text();
    var newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    var actionText = newStatus === 'active' ? 'activate' : 'deactivate';

    // Show confirmation dialog
    Swal.fire({
      title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} Customer?`,
      html: `Are you sure you want to ${actionText} <strong>${customerName}</strong>?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: newStatus === 'active' ? '#3085d6' : '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: `Yes, ${actionText}!`,
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: 'PATCH',
          data: {
            '_token': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function() {
            Swal.fire({
              title: 'Updating...',
              text: 'Please wait while we update the customer status.',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
          },
          success: function(response) {
            // Update the status badge
            var statusCell = element.closest('tr').find('td:eq(6)');
            var newBadgeClass = newStatus === 'active' ? 'bg-label-success' : 'bg-label-secondary';
            statusCell.html(`<span class='badge ${newBadgeClass}'>${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}</span>`);
            
            // Update the toggle button text
            element.html(`<i class="bx bx-power-off me-1"></i> ${newStatus === 'active' ? 'Deactivate' : 'Activate'}`);
            element.data('current-status', newStatus);
            
            // Show success message
            Swal.fire({
              icon: 'success',
              title: 'Updated!',
              text: response.message || 'Customer status has been updated.',
              timer: 2000,
              showConfirmButton: false,
              position: 'top-end',
              toast: true
            });
          },
          error: function(xhr) {
            // Show error message
            var errorMessage = 'An error occurred while updating customer status.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
              errorMessage = xhr.responseJSON.error;
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: errorMessage,
              confirmButtonColor: '#3085d6'
            });
          }
        });
      }
    });
  });

  // Handle add customer form submission
  if (add_customer_form.length) {
    add_customer_form.on('submit', function(e) {
      e.preventDefault();
      
      var form = $(this);
      var url = form.attr('action');
      var formData = form.serialize();
      
      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        beforeSend: function() {
          // Show loading
          Swal.fire({
            title: 'Creating Customer...',
            text: 'Please wait while we create the customer.',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
        },
        success: function(response) {
          Swal.close();
          
          if (response.success) {
            // Show success message
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response.message || 'Customer created successfully.',
              timer: 3000,
              showConfirmButton: false,
              position: 'top-end',
              toast: true
            }).then(() => {
              // Close the offcanvas
              var offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasAddCustomer'));
              offcanvas.hide();
              
              // Reset the form
              form[0].reset();
              
              // Reload the page to show the new customer
              window.location.reload();
            });
          }
        },
        error: function(xhr) {
          Swal.close();
          
          // Show error message
          var errorMessage = 'An error occurred while creating the customer.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          
          // If there are validation errors, show them
          if (xhr.responseJSON && xhr.responseJSON.errors) {
            var errors = xhr.responseJSON.errors;
            var errorList = '<ul class="text-start">';
            for (var field in errors) {
              errorList += '<li>' + errors[field][0] + '</li>';
            }
            errorList += '</ul>';
            
            Swal.fire({
              icon: 'error',
              title: 'Validation Error!',
              html: errorList,
              confirmButtonColor: '#3085d6',
              width: '500px'
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: errorMessage,
              confirmButtonColor: '#3085d6'
            });
          }
        }
      });
    });
  }

  // Phone number formatting
  if ($('.phone-mask').length) {
    $('.phone-mask').each(function() {
      new Cleave(this, {
        phone: true,
        phoneRegionCode: 'US'
      });
    });
  }

  // Initialize select2 for customer type filter
  if (customer_type_filter.length) {
    customer_type_filter.select2({
      minimumResultsForSearch: -1,
      dropdownParent: customer_type_filter.parent()
    });
  }

  // Initialize select2 for customer status filter
  if (customer_status_filter.length) {
    customer_status_filter.select2({
      minimumResultsForSearch: -1,
      dropdownParent: customer_status_filter.parent()
    });
  }

  // Initialize select2 for customer type in form
  if ($('#customer-type').length) {
    $('#customer-type').select2({
      dropdownParent: $('#offcanvasAddCustomer')
    });
  }
});