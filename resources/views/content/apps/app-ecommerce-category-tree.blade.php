@extends('layouts/layoutMaster')

@section('title', 'Category Tree Hierarchy (Drag & Drop) - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/jstree/jstree.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/select2/select2.scss'
])
<style>
  .jstree-default .jstree-wholerow-clicked,
  .jstree-default .jstree-wholerow-hovered {
    background: #EEF2FF !important;
    border-radius: 8px;
  }
  .jstree-default .jstree-clicked {
    color: #4F46E5 !important;
    font-weight: 700;
  }
  .jstree-node {
    margin: 4px 0;
  }
  .tree-card-container {
    min-height: 540px;
    background: #FAFAFB;
    border: 1.5px dashed #E2E8F0;
    border-radius: 16px;
    padding: 20px;
  }
  .vakata-context {
    z-index: 1065 !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15) !important;
    border: 1px solid #E2E8F0 !important;
    padding: 6px !important;
    font-family: inherit !important;
  }
  .vakata-context li > a {
    border-radius: 8px !important;
    padding: 6px 12px !important;
    font-weight: 600 !important;
    font-size: 13px !important;
  }
  .vakata-context li > a:hover {
    background: #4F46E5 !important;
    color: #FFFFFF !important;
  }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/jstree/jstree.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/select2/select2.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Breadcrumb & Actions -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1">
        <i class="icon-base bx bx-git-repo-forked text-primary me-2"></i>{{ __('Category Visual Tree (Drag & Drop)') }}
      </h4>
      <p class="text-muted mb-0">{{ __('Reorganize aisles, nested subcategories, and parent-child levels freely with drag and drop or right-click context menu.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('app-ecommerce-product-category') }}" class="btn btn-label-secondary">
        <i class="icon-base bx bx-list-ul me-1"></i> {{ __('Switch to Table View') }}
      </a>
      <button type="button" class="btn btn-primary" onclick="openCreateModal(null, '')">
        <i class="icon-base bx bx-plus me-1"></i> {{ __('Add Main Aisle') }}
      </button>
    </div>
  </div>

  <!-- Metric Badges Row -->
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
      <div class="card border shadow-xs rounded-4">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted small text-uppercase fw-bold">{{ __('Total Aisles & Categories') }}</span>
            <h3 class="fw-bold my-1 text-dark" id="statTotalCount">{{ number_format($totalCategories) }}</h3>
            <span class="badge bg-label-primary small">{{ __('Entire Catalog') }}</span>
          </div>
          <div class="avatar avatar-lg bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
            <i class="icon-base bx bx-category fs-3"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-4">
      <div class="card border shadow-xs rounded-4">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted small text-uppercase fw-bold">{{ __('Main Parent Aisles') }}</span>
            <h3 class="fw-bold my-1 text-success" id="statMainCount">{{ number_format($mainCategoriesCount) }}</h3>
            <span class="badge bg-label-success small">{{ __('Top-Level Aisles') }}</span>
          </div>
          <div class="avatar avatar-lg bg-label-success rounded-circle d-flex align-items-center justify-content-center">
            <i class="icon-base bx bx-folder-open fs-3"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-4">
      <div class="card border shadow-xs rounded-4">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted small text-uppercase fw-bold">{{ __('Nested Subcategories') }}</span>
            <h3 class="fw-bold my-1 text-info" id="statSubCount">{{ number_format($subCategoriesCount) }}</h3>
            <span class="badge bg-label-info small">{{ __('Sub-Sections & Children') }}</span>
          </div>
          <div class="avatar avatar-lg bg-label-info rounded-circle d-flex align-items-center justify-content-center">
            <i class="icon-base bx bx-git-branch fs-3"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Tree Management Interface -->
  <div class="row g-4">
    <!-- Left Column: Treeview with Action Bar -->
    <div class="col-12 col-lg-8">
      <div class="card border shadow-xs rounded-4 h-100">
        <div class="card-header border-bottom bg-light py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
          <div class="d-flex align-items-center gap-2 flex-grow-1 me-md-4" style="max-width: 320px;">
            <div class="input-group input-group-merge">
              <span class="input-group-text bg-white"><i class="icon-base bx bx-search"></i></span>
              <input type="text" id="categoryTreeSearch" class="form-control" placeholder="{{ __('Search tree nodes...') }}">
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-label-secondary" id="btnExpandAll" title="{{ __('Expand All') }}">
              <i class="icon-base bx bx-expand me-1"></i> {{ __('Expand All') }}
            </button>
            <button type="button" class="btn btn-sm btn-label-secondary" id="btnCollapseAll" title="{{ __('Collapse All') }}">
              <i class="icon-base bx bx-collapse me-1"></i> {{ __('Collapse All') }}
            </button>
            <button type="button" class="btn btn-sm btn-label-primary" id="btnRefreshTree" title="{{ __('Reload Tree') }}">
              <i class="icon-base bx bx-refresh"></i>
            </button>
          </div>
        </div>

        <div class="card-body p-4">
          <div class="alert alert-info d-flex align-items-center gap-2 rounded-3 mb-3 py-2 px-3">
            <i class="icon-base bx bx-info-circle fs-5"></i>
            <span class="small">
              <strong>{{ __('Pro Tip:') }}</strong> {{ __('Drag any category onto another to nest it as a subcategory. Drag out to root space to make it a main aisle. Right-click any category to add subcategories directly!') }}
            </span>
          </div>

          <div class="tree-card-container">
            <div id="categoryJsTree" class="overflow-auto"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Node Inspector & Fast Edit -->
    <div class="col-12 col-lg-4">
      <div class="card border shadow-xs rounded-4 sticky-top" style="top: 80px;" id="nodeInspectorCard">
        <div class="card-header border-bottom bg-light py-3 d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-slider text-primary"></i>
            <span>{{ __('Category Inspector') }}</span>
          </h5>
          <span class="badge bg-label-primary" id="inspectorLevelBadge">{{ __('Details') }}</span>
        </div>
        <div class="card-body p-4">
          <!-- Empty State when nothing selected -->
          <div id="inspectorEmptyState" class="text-center py-5">
            <i class="icon-base bx bx-pointer fs-1 text-muted mb-2"></i>
            <h6 class="text-muted fw-bold">{{ __('Select a category in the tree') }}</h6>
            <p class="small text-muted mb-0">{{ __('Click on any category node in the tree to inspect details, add subcategories under it, or update hierarchy.') }}</p>
          </div>

          <!-- Active Node Inspector Details -->
          <div id="inspectorContent" class="d-none">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                  <i class="icon-base bx bx-purchase-tag fs-4" id="inspectorIcon"></i>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold" id="inspectorTitle">Category Name</h6>
                  <span class="badge bg-label-success rounded-pill font-monospace small" id="inspectorProductsBadge">0 Products</span>
                </div>
              </div>
            </div>

            <!-- Quick Subcategory Creation Trigger -->
            <div class="d-grid gap-2 mb-4">
              <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" id="btnInspectorAddChild">
                <i class="icon-base bx bx-plus-circle me-1"></i> {{ __('Add Subcategory Under This') }}
              </button>
              <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill d-none" id="btnInspectorPromoteRoot">
                <i class="icon-base bx bx-up-arrow-circle me-1"></i> {{ __('Promote to Top-Level Main Aisle') }}
              </button>
            </div>

            <form id="inspectorForm" method="POST">
              @csrf
              <input type="hidden" name="_method" value="PUT">
              <input type="hidden" name="id" id="inspectorId">

              <div class="mb-3">
                <label class="form-label small fw-bold">{{ __('Category Title') }}</label>
                <input type="text" class="form-control" name="categoryTitle" id="inspectorInputTitle" required>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold">{{ __('URL Slug') }}</label>
                <input type="text" class="form-control font-monospace small" name="slug" id="inspectorInputSlug">
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold">{{ __('Parent Category / Aisle') }}</label>
                <select name="parent_id" id="inspectorParentSelect" class="form-select">
                  <option value="">{{ __('None (Top-Level Main Aisle)') }}</option>
                  @foreach($allCategories as $catOption)
                    <option value="{{ $catOption->id }}" id="optParent_{{ $catOption->id }}">
                      {{ $catOption->hierarchy_name }}
                    </option>
                  @endforeach
                </select>
                <span class="form-text text-muted small">{{ __('Change parent here or simply drag & drop in the tree.') }}</span>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold">{{ __('Description') }}</label>
                <textarea name="description" id="inspectorDescription" class="form-control" rows="3" placeholder="Category details..."></textarea>
              </div>

              <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary flex-grow-1">
                  <i class="icon-base bx bx-save me-1"></i> {{ __('Save Changes') }}
                </button>
                <button type="button" class="btn btn-label-danger" id="inspectorDeleteBtn" title="{{ __('Delete Category') }}">
                  <i class="icon-base bx bx-trash"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Add New Category or Subcategory -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="createModalTitle">
          <i class="icon-base bx bx-folder-plus text-primary me-2"></i>{{ __('Create Category') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('app-ecommerce-category-add') }}" method="POST" id="createCategoryForm">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('Category Title') }} <span class="text-danger">*</span></label>
            <input type="text" name="categoryTitle" id="createInputTitle" class="form-control" placeholder="e.g. Fresh Organic Juices" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('URL Slug (Optional)') }}</label>
            <input type="text" name="slug" id="createInputSlug" class="form-control" placeholder="e.g. fresh-organic-juices">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('Parent Category / Aisle') }}</label>
            <select name="parent_id" id="createParentSelect" class="form-select">
              <option value="">{{ __('None (Top-Level Main Aisle)') }}</option>
              @foreach($allCategories as $catOption)
                <option value="{{ $catOption->id }}">
                  {{ $catOption->hierarchy_name }}
                </option>
              @endforeach
            </select>
            <span class="form-text text-muted small">{{ __('Select any category to nest under it, or leave blank to make a top-level aisle.') }}</span>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('Description') }}</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Category or shelf details..."></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary fw-bold">{{ __('Create Category') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
let currentSelectedNode = null;

function openCreateModal(parentId = null, parentName = '') {
  const modal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
  const select = document.getElementById('createParentSelect');
  const title = document.getElementById('createModalTitle');

  if (parentId) {
    select.value = parentId;
    title.innerHTML = `<i class="icon-base bx bx-git-branch text-primary me-2"></i>Add Subcategory under <strong>${parentName}</strong>`;
  } else {
    select.value = '';
    title.innerHTML = `<i class="icon-base bx bx-folder-plus text-primary me-2"></i>Create Top-Level Main Aisle`;
  }

  document.getElementById('createInputTitle').value = '';
  document.getElementById('createInputSlug').value = '';
  modal.show();
}

document.addEventListener('DOMContentLoaded', function () {
  const treeContainer = $('#categoryJsTree');
  const treeSearch = $('#categoryTreeSearch');

  // Initialize jsTree with Drag & Drop, Search, Types, and Context Menu
  treeContainer.jstree({
    core: {
      check_callback: function (operation, node, node_parent, node_position, more) {
        // Operation callback allows unrestricted nesting & drag-drop
        return true;
      },
      themes: {
        responsive: true
      },
      data: {
        url: '{{ route("app-ecommerce-category-tree-data") }}',
        dataType: 'json'
      }
    },
    plugins: ['dnd', 'search', 'types', 'wholerow', 'contextmenu'],
    types: {
      default: {
        icon: 'icon-base bx bx-folder text-primary'
      }
    },
    contextmenu: {
      items: function (node) {
        const d = node.data;
        return {
          addChild: {
            label: "➕ Add Subcategory Under This",
            action: function () {
              openCreateModal(d.id, d.name);
            }
          },
          promoteRoot: {
            label: "🔼 Promote to Top-Level (Main Aisle)",
            _disabled: !d.parent_id,
            action: function () {
              moveCategoryAjax(d.id, null);
            }
          },
          edit: {
            label: "✏️ Inspect / Edit Details",
            action: function () {
              treeContainer.jstree('select_node', node);
            }
          },
          deleteItem: {
            label: "🗑️ Delete Category",
            separator_before: true,
            action: function () {
              deleteCategoryAjax(d.id, d.name);
            }
          }
        };
      }
    }
  });

  // Search Filter Handler with Debounce
  let searchTimeout = null;
  treeSearch.on('keyup', function () {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function () {
      const v = treeSearch.val();
      treeContainer.jstree(true).search(v);
    }, 250);
  });

  // Expand / Collapse / Refresh Controls
  $('#btnExpandAll').on('click', function () {
    treeContainer.jstree('open_all');
  });

  $('#btnCollapseAll').on('click', function () {
    treeContainer.jstree('close_all');
  });

  $('#btnRefreshTree').on('click', function () {
    treeContainer.jstree(true).refresh();
  });

  // Handle Drag and Drop Reparenting
  treeContainer.on('move_node.jstree', function (e, data) {
    const nodeId = data.node.id;
    const parentId = data.parent === '#' ? null : data.parent;
    moveCategoryAjax(nodeId, parentId);
  });

  function moveCategoryAjax(nodeId, parentId) {
    $.ajax({
      url: '{{ route("app-ecommerce-category-move-node") }}',
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json'
      },
      data: {
        id: nodeId,
        parent_id: parentId
      },
      success: function (res) {
        Swal.fire({
          icon: 'success',
          title: 'Hierarchy Updated!',
          text: res.message,
          timer: 2200,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });

        // Update live metric cards
        if (res.stats) {
          $('#statTotalCount').text(res.stats.total);
          $('#statMainCount').text(res.stats.main);
          $('#statSubCount').text(res.stats.sub);
        }

        treeContainer.jstree(true).refresh();
      },
      error: function (err) {
        treeContainer.jstree(true).refresh();
        Swal.fire({
          icon: 'error',
          title: 'Cannot Move',
          text: err.responseJSON?.message || 'Failed to update category position.'
        });
      }
    });
  }

  // Node Selected -> Populate Inspector
  treeContainer.on('select_node.jstree', function (e, data) {
    const d = data.node.data;
    if (!d) return;

    currentSelectedNode = d;

    $('#inspectorEmptyState').addClass('d-none');
    $('#inspectorContent').removeClass('d-none');

    $('#inspectorId').val(d.id);
    $('#inspectorTitle').text(d.name);
    $('#inspectorInputTitle').val(d.name);
    $('#inspectorInputSlug').val(d.slug);
    $('#inspectorDescription').val(d.description);
    $('#inspectorProductsBadge').text((d.products_count || 0) + ' Products');
    $('#inspectorParentSelect').val(d.parent_id || '');

    if (d.parent_id) {
      $('#inspectorLevelBadge').text('Subcategory').removeClass('bg-label-primary').addClass('bg-label-info');
      $('#btnInspectorPromoteRoot').removeClass('d-none');
    } else {
      $('#inspectorLevelBadge').text('Top-Level Aisle').removeClass('bg-label-info').addClass('bg-label-primary');
      $('#btnInspectorPromoteRoot').addClass('d-none');
    }

    // Disable selecting self in parent dropdown
    $('#inspectorParentSelect option').prop('disabled', false);
    $(`#optParent_${d.id}`).prop('disabled', true);

    $('#inspectorForm').attr('action', `/products/categories/${d.id}`);
  });

  // Add child button inside inspector
  $('#btnInspectorAddChild').on('click', function () {
    if (currentSelectedNode) {
      openCreateModal(currentSelectedNode.id, currentSelectedNode.name);
    }
  });

  // Promote to root button inside inspector
  $('#btnInspectorPromoteRoot').on('click', function () {
    if (currentSelectedNode) {
      moveCategoryAjax(currentSelectedNode.id, null);
    }
  });

  // Inspector Form Submit AJAX
  $('#inspectorForm').on('submit', function (e) {
    e.preventDefault();
    const form = $(this);
    const url = form.attr('action');

    $.ajax({
      url: url,
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json'
      },
      data: form.serialize(),
      success: function (res) {
        Swal.fire({
          icon: 'success',
          title: 'Saved!',
          text: res.message || 'Category updated.',
          timer: 2000,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });
        treeContainer.jstree(true).refresh();
      },
      error: function (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: err.responseJSON?.message || 'Failed to update category.'
        });
      }
    });
  });

  function deleteCategoryAjax(id, name) {
    Swal.fire({
      title: `Delete '${name}'?`,
      text: "This category and its nested children will be removed!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-danger me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          url: `/products/categories/${id}`,
          type: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
          },
          success: function (res) {
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: res.message || 'Category removed.',
              customClass: { confirmButton: 'btn btn-success' }
            });
            $('#inspectorContent').addClass('d-none');
            $('#inspectorEmptyState').removeClass('d-none');
            treeContainer.jstree(true).refresh();
          },
          error: function (err) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: err.responseJSON?.message || 'Could not delete category.'
            });
          }
        });
      }
    });
  }

  // Inspector Delete Action
  $('#inspectorDeleteBtn').on('click', function () {
    const id = $('#inspectorId').val();
    const name = $('#inspectorTitle').text();
    if (id) deleteCategoryAjax(id, name);
  });
});
</script>
@endsection
