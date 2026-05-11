/**
 * Add Permission Modal JS
 */

'use strict';

// Add permission form validation
document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const addPermissionForm = document.getElementById('addPermissionForm');
    const fv = FormValidation.formValidation(addPermissionForm, {
      fields: {
        modalPermissionName: {
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
      // Send the data to the server
      $.ajax({
        data: $('#addPermissionForm').serialize(),
        url: baseUrl + 'app/access-permission/store',
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (status) {
          // dt_permission.ajax.reload();
          if (window.LaravelDataTables) {
            Object.values(window.LaravelDataTables).forEach(dt => dt.ajax.reload());
          } else {
            // fallback if not using LaravelDataTables or if it's named differently
            $('.datatables-permissions').DataTable().ajax.reload();
          }

          $('#addPermissionModal').modal('hide');
          addPermissionForm.reset();

          // sweetalert
          Swal.fire({
            icon: 'success',
            title: 'Successfully Created!',
            text: 'Permission Created Successfully',
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
});
