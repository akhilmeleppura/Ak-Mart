'use strict';

$(function () {
  const baseUrl = window.location.origin + '/';
  const select2UserSearch = $('.select2-user-search');

  // Initialize Select2
  if (select2UserSearch.length) {
    select2UserSearch.select2({
      placeholder: 'Select a user...',
      dropdownParent: $('#user-roles')
    });
  }

  // --- TAB 1: ROLE MANAGEMENT ---

  // Quick Create Role
  $('#quickCreateRoleForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: `${baseUrl}app/access-hub/roles`,
      type: 'POST',
      data: $(this).serialize(),
      success: function (res) {
        $('#addRoleModal').modal('hide');
        Swal.fire({
          icon: 'success',
          title: 'Role Created!',
          text: res.success,
          customClass: { confirmButton: 'btn btn-success' }
        }).then(() => {
          location.reload();
        });
      },
      error: function (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: err.responseJSON.message || 'Error creating role',
          customClass: { confirmButton: 'btn btn-primary' }
        });
      }
    });
  });

  // Delete Role
  $('.delete-role').on('click', function () {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Are you sure?',
      text: "Users with this role will lose their permissions!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: `${baseUrl}app/access-hub/roles/${id}`,
          type: 'DELETE',
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          success: function (res) {
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: res.success,
              customClass: { confirmButton: 'btn btn-success' }
            }).then(() => { location.reload(); });
          },
          error: function (err) {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: err.responseJSON.error || 'Error deleting role',
              customClass: { confirmButton: 'btn btn-primary' }
            });
          }
        });
      }
    });
  });

  // --- TAB 2: ROLE PERMISSIONS ---

  $('.role-item').on('click', function () {
    const id = $(this).data('id');
    $('.role-item').removeClass('active');
    $(this).addClass('active');

    $('#no-role-selected').addClass('d-none');
    $('#permission-config-container').removeClass('d-none');

    $.get(`${baseUrl}app/access-hub/roles/${id}/permissions`, function (data) {
      $('#selected-role-name').text(data.name);
      $('#config-role-id').val(id);
      $('.permission-checkbox').prop('checked', false);
      $('#selectAllPermissions').prop('checked', false);

      data.permissions.forEach(p => {
        $(`input[value="${p}"]`).prop('checked', true);
      });
    });
  });

  $('#selectAllPermissions').on('change', function () {
    $('.permission-checkbox').prop('checked', $(this).is(':checked'));
  });

  $('#syncRolePermissionsForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: `${baseUrl}app/access-hub/roles/sync-permissions`,
      type: 'POST',
      data: $(this).serialize(),
      success: function (res) {
        Swal.fire({
          icon: 'success',
          title: 'Permissions Synced!',
          text: res.success,
          customClass: { confirmButton: 'btn btn-success' }
        });
      }
    });
  });

  // --- TAB 3: USER ASSIGNMENT ---

  $('.select2-user-search').on('change', function () {
    const userId = $(this).val();
    if (!userId) return;

    $('input[name="roles[]"]').prop('checked', false);

    $.get(`${baseUrl}app/access-hub/users/${userId}/roles`, function (data) {
      data.roles.forEach(r => {
        $(`input[name="roles[]"][value="${r}"]`).prop('checked', true);
      });
    });
  });

  $('#assignUserRoleForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: `${baseUrl}app/access-hub/users/sync-roles`,
      type: 'POST',
      data: $(this).serialize(),
      success: function (res) {
        Swal.fire({
          icon: 'success',
          title: 'Roles Assigned!',
          text: res.success,
          customClass: { confirmButton: 'btn btn-success' }
        });
      }
    });
  });
});
