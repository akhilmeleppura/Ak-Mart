 $(function () {
  'use strict';

  // Handle customer form submission
  $('#customerForm').on('submit', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var url = form.attr('action');
    var formData = form.serialize();
    var isUpdate = url.includes('/update') || form.find('input[name="_method"]').val() === 'PUT';
    
    $.ajax({
      url: url,
      type: 'POST',
      data: formData,
      beforeSend: function() {
        // Show loading
        Swal.fire({
          title: isUpdate ? 'Updating Customer...' : 'Creating Customer...',
          text: 'Please wait while we ' + (isUpdate ? 'update' : 'create') + ' the customer.',
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
            text: response.message || (isUpdate ? 'Customer updated successfully.' : 'Customer created successfully.'),
            timer: 3000,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
          }).then(() => {
            // Redirect to index page
            window.location.href = '/general/customers';
          });
        }
      },
      error: function(xhr) {
        Swal.close();
        
        // Show error message
        var errorMessage = 'An error occurred while ' + (isUpdate ? 'updating' : 'creating') + ' the customer.';
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

  // Phone number formatting
  if ($('.phone-mask').length) {
    $('.phone-mask').each(function() {
      new Cleave(this, {
        phone: true,
        phoneRegionCode: 'US'
      });
    });
  }

  // Initialize select2 for customer type
  if ($('#customer_type_id').length) {
    $('#customer_type_id').select2({
      dropdownParent: $('#customer_type_id').parent()
    });
  }
});