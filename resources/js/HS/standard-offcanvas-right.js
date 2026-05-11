/**
 * User Management - Add/Edit/Delete/User Actions
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  document.addEventListener('click', function (e) {
    if (e.target.closest('.edit-record')) {
      const editBtn = e.target.closest('.edit-record');
      const user_id = editBtn.dataset.id;

      const dtrModal = document.querySelector('.dtr-bs-modal.show');
      if (dtrModal) {
        const bsModal = bootstrap.Modal.getInstance(dtrModal);
        bsModal.hide();
      }

      document.getElementById('offcanvasRightLabel').innerText = 'Edit User';

      fetch(`${baseUrl}${ajaxUrl}/${user_id}/edit`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('user_id').value = data.id ?? '';

          document.querySelectorAll('#addNewUserForm input, #addNewUserForm select').forEach(field => {
            const name = field.name;
            if (data.hasOwnProperty(name)) {
              if ($(field).hasClass('select2-hidden-accessible')) {
                $(field).val(data[name]).trigger('change');
              } else {
                field.value = data[name] ?? '';
              }
            } else {
              field.value = '';
              if ($(field).hasClass('select2-hidden-accessible')) {
                $(field).val('').trigger('change');
              }
            }
          });

          // ✅ Role select field (if needed)
          if (data.role || data.role_id) {
            $('#role')
              .val(data.role_id ?? data.role)
              .trigger('change');
          }

          // ✅ Set is_super_admin if visible
          const superAdminField = document.getElementById('is_super_admin');
          if (superAdminField && data.hasOwnProperty('is_super_admin')) {
            const value = String(data.is_super_admin) === '1' ? '1' : '0';
            superAdminField.value = value;
            superAdminField.dispatchEvent(new Event('change', { bubbles: true }));
          }

          // ✅ Show the offcanvas
          const offcanvasEl = document.getElementById('offcanvasAddUser');
          const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
          bsOffcanvas.show();
        })

        .catch(err => {
          console.error('Edit fetch failed:', err);
        });
    }
  });

  // Add mode
  document.getElementById('addUserBtn')?.addEventListener('click', function () {
    const form = document.getElementById('addNewUserForm');
    form.reset();

    document.getElementById('offcanvasRightLabel').innerText = 'Add User';
    document.getElementById('user_id').value = '';

    $('#country').val('').trigger('change');
    $('#role').val('').trigger('change');
  });

  document.getElementById('addNewUserForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const userId = formData.get('id');

    fetch(`${baseUrl}${ajaxUrl}`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
      .then(response => response.json())
      .then(data => {
        if (data === 'Created' || data === 'Updated') {
          const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasAddUser'));
          offcanvas.hide();
          $('.datatables-users').DataTable().ajax.reload();
          Swal.fire({
            icon: 'success',
            title: `User ${data}`,
            confirmButtonText: 'OK'
          });
        } else if (data.message) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message,
            confirmButtonText: 'OK'
          });
        }
      })
      .catch(error => {
        console.error('Submit error:', error);
      });
  });

  // Delete record
  document.addEventListener('click', function (e) {
    if (e.target.closest('.delete-record')) {
      const deleteBtn = e.target.closest('.delete-record');
      const user_id = deleteBtn.dataset.id;
      const dtrModal = document.querySelector('.dtr-bs-modal.show');
      if (dtrModal) {
        const bsModal = bootstrap.Modal.getInstance(dtrModal);
        bsModal.hide();
      }

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
          fetch(`${baseUrl}${ajaxUrl}/${user_id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Content-Type': 'application/json'
            }
          })
            .then(response => {
              if (response.ok) {
                $('.datatables-users').DataTable().draw();
                Swal.fire({
                  icon: 'success',
                  title: 'Deleted!',
                  text: 'The user has been deleted!',
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                });
              } else {
                throw new Error('Delete failed');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to delete the user.',
                customClass: {
                  confirmButton: 'btn btn-danger'
                }
              });
            });
        }
      });
    }
  });

  // Add new user reset
  const addNewBtn = document.querySelector('.add-new');
  if (addNewBtn) {
    addNewBtn.addEventListener('click', function () {
      document.getElementById('user_id').value = '';
      document.getElementById('offcanvasRightLabel').innerHTML = 'Add User';
    });
  }

  // Phone input formatting
  const phoneMaskList = document.querySelectorAll('.phone-mask');
  if (phoneMaskList) {
    phoneMaskList.forEach(function (phoneMask) {
      phoneMask.addEventListener('input', event => {
        const cleanValue = event.target.value.replace(/\D/g, '');
        phoneMask.value = formatGeneral(cleanValue, {
          blocks: [3, 3, 4],
          delimiters: [' ', ' ']
        });
      });
      registerCursorTracker({
        input: phoneMask,
        delimiter: ' '
      });
    });
  }

  // Initialize Select2
  const select2 = $('.select2');
  if (select2.length) {
    const $this = select2;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select Country',
      dropdownParent: $this.parent()
    });
  }

  // CSRF Setup for AJAX
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
});
