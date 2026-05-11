 $(function () {
  'use strict';

  // Handle customer type form submission
  $('#customerTypeForm').on('submit', function(e) {
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
          title: isUpdate ? 'Updating Customer Type...' : 'Creating Customer Type...',
          text: 'Please wait while we ' + (isUpdate ? 'update' : 'create') + ' the customer type.',
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
            text: response.message || (isUpdate ? 'Customer type updated successfully.' : 'Customer type created successfully.'),
            timer: 2000,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
          }).then(() => {
            // Redirect to index page
            window.location.href = '/general/customer-types';
          });
        }
      },
      error: function(xhr) {
        Swal.close();
        
        // Show error message
        var errorMessage = 'An error occurred while ' + (isUpdate ? 'updating' : 'creating') + ' the customer type.';
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
});