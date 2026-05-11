/**
 * Edit Permission Modal JS
 */

'use strict';

// Edit permission form validation
document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const editPermissionForm = document.getElementById('editPermissionForm');
    const fv = FormValidation.formValidation(editPermissionForm, {
      fields: {
        editPermissionName: {
          validators: {
            notEmpty: {
              message: 'Please enter permission name'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.form-control-validation'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        // Submit the form when all fields are valid
        // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    }).on('core.form.valid', function () {
      var id = $('#editPermissionId').val();
      // Send the data to the server
      $.ajax({
        data: $('#editPermissionForm').serialize(),
        url: baseUrl + 'app/access-permission/' + id + '/update',
        type: 'PUT',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (status) {
          if (window.LaravelDataTables) {
            Object.values(window.LaravelDataTables).forEach(dt => dt.ajax.reload());
          } else {
            $('.datatables-permissions').DataTable().ajax.reload();
          }

          $('#editPermissionModal').modal('hide');

          // sweetalert
          Swal.fire({
            icon: 'success',
            title: 'Successfully Updated!',
            text: 'Permission Updated Successfully',
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        },
        error: function (err) {
          Swal.fire({
            title: 'Error!',
            text: err.responseJSON.message,
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-primary'
            }
          });
        }
      });
    });
  })();

  // Edit Permission click handler to populate modal
  $(document).on('click', '.edit-permission', function () {
    var id = $(this).data('id');
    $.get(baseUrl + 'app/access-permission/' + id + '/edit', function (data) {
      $('#editPermissionId').val(data.id);
      $('#editPermissionName').val(data.name);
    });
  });
});
