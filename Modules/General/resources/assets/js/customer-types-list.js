 $(function () {
  'use strict';

  // Handle delete form submission for customer types
  $(document).on('submit', '.delete-customer-type-form', function(e) {
    e.preventDefault();
    var form = $(this);
    var url = form.attr('action');
    var token = form.find('input[name="_token"]').val();
    var method = form.find('input[name="_method"]').val();
    var typeName = form.closest('tr').find('td:eq(1)').text();

    // Show confirmation dialog
    Swal.fire({
      title: 'Delete Customer Type?',
      html: `Are you sure you want to delete <strong>${typeName}</strong>?<br><br>You won't be able to revert this!`,
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
              text: 'Please wait while we delete the customer type.',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });
          },
          success: function(response) {
            // Remove the row from the table
            var row = form.closest('tr');
            $('.datatables-customer-types').DataTable().row(row).remove().draw();
            
            // Show success message
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: response.message || 'Customer type has been deleted.',
              timer: 2000,
              showConfirmButton: false,
              position: 'top-end',
              toast: true
            });
          },
          error: function(xhr) {
            // Show error message
            var errorMessage = 'An error occurred while deleting the customer type.';
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

  // Initialize DataTable for customer types
  if ($('.datatables-customer-types').length) {
    $('.datatables-customer-types').DataTable({
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      responsive: true,
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Customer Type'
      }
    });
  }

  // Filter form control to default size
  $('.dataTables_filter .form-control').removeClass('form-control-sm');
  $('.dataTables_length .form-select').removeClass('form-select-sm');
});