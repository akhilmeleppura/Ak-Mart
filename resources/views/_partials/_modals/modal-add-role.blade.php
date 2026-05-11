<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="role-title mb-2">Add New Role</h4>
          <p>Set role permissions</p>
        </div>
        <!-- Add role form -->
        <form id="addRoleForm" class="row g-6" method="POST" action="{{ route('app-access-roles-store') }}">
          @csrf
          <input type="hidden" name="_method" id="methodField" value="POST">
          <div class="col-12 form-control-validation">
            <label class="form-label" for="modalRoleName">Role Name</label>
            <input type="text" id="modalRoleName" name="name" class="form-control"
              placeholder="Enter a role name" required />
          </div>
          <div class="col-12">
            <h5 class="mb-6">Role Permissions</h5>
            <!-- Permission table -->
            <div class="table-responsive">
              <table class="table table-flush-spacing mb-0 border-top">
                <tbody>
                  <tr>
                    <td class="text-nowrap fw-medium text-heading">Administrator Access <i
                        class="icon-base bx bx-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Allows a full access to the system"></i></td>
                    <td>
                      <div class="d-flex justify-content-end">
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" id="selectAll" />
                          <label class="form-check-label" for="selectAll"> Select All </label>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @foreach($permissions as $permission)
                  <tr>
                    <td class="text-nowrap fw-medium text-heading">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</td>
                    <td>
                      <div class="d-flex justify-content-end">
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm{{ $permission->id }}" />
                          <label class="form-check-label" for="perm{{ $permission->id }}"> Enable </label>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- Permission table -->
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
              aria-label="Close">Cancel</button>
          </div>
        </form>
        <!--/ Add role form -->
      </div>
    </div>
  </div>
</div>
<!--/ Add Role Modal -->

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');

    if(selectAll) {
      selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
      });
    }
  });
</script>
