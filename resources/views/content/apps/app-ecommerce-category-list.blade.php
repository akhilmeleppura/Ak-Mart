@extends('layouts/layoutMaster')

@section('title', 'eCommerce Product Category - Apps')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/katex.scss',
  'resources/assets/vendor/libs/quill/editor.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/app-ecommerce.scss')
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/quill/katex.js',
  'resources/assets/vendor/libs/quill/quill.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/moment/moment.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/app-ecommerce-category-list.js'])
@endsection

@section('content')
<div class="card mb-6">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Categories</h5>
    <div class="d-flex align-items-center gap-3">
        <div class="w-px-250">
            <input type="text" class="form-control date-picker" placeholder="Filter by Date Range" id="dateRange" />
        </div>
    </div>
  </div>
</div>

<div class="app-ecommerce-category">
  <!-- Category List Tabs -->
  <div class="row g-6 mb-6">
    <div class="col-md-12">
      <div class="nav-align-top">
        <ul class="nav nav-tabs nav-fill" role="tablist">
          <li class="nav-item">
            <button type="button" class="nav-link active category-filter" data-filter="all" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-all" aria-controls="navs-justified-all" aria-selected="true"><i class="icon-base bx bx-list-ul me-2"></i> All Categories</button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link category-filter" data-filter="parent" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-parent" aria-controls="navs-justified-parent" aria-selected="false"><i class="icon-base bx bx-folder me-2"></i> Main Categories</button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link category-filter" data-filter="sub" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-sub" aria-controls="navs-justified-sub" aria-selected="false"><i class="icon-base bx bx-git-branch me-2"></i> Sub-Categories</button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Category List Table -->
  <div class="card">
    <div class="card-datatable">
      <table class="datatables-category-list table">
        <thead>
          <tr>
            <th></th>
            <th></th>
            <th>Categories</th>
            <th class="text-nowrap text-sm-end">Total Products &nbsp;</th>
            <th class="text-nowrap text-sm-end">Total Earning</th>
            <th class="text-lg-center">Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
  <!-- Offcanvas to add new customer -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCategoryList"
    aria-labelledby="offcanvasEcommerceCategoryListLabel">
    <!-- Offcanvas Header -->
    <div class="offcanvas-header py-6">
      <h5 id="offcanvasEcommerceCategoryListLabel" class="offcanvas-title">Add Category</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <!-- Offcanvas Body -->
    <div class="offcanvas-body border-top">
      <form class="pt-0" id="eCommerceCategoryListForm" method="POST" action="{{ route('app-ecommerce-category-add') }}">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" name="id" id="categoryId">
        <!-- Title -->
        <div class="mb-6 form-control-validation">
          <label class="form-label" for="ecommerce-category-title">Title</label>
          <input type="text" class="form-control" id="ecommerce-category-title" placeholder="Enter category title"
            name="categoryTitle" aria-label="category title" />
        </div>
        <!-- Slug -->
        <div class="mb-6 form-control-validation">
          <label class="form-label" for="ecommerce-category-slug">Slug</label>
          <input type="text" id="ecommerce-category-slug" class="form-control" placeholder="Enter slug"
            aria-label="slug" name="slug" />
        </div>
        <!-- Image -->
        <div class="mb-6">
          <label class="form-label" for="ecommerce-category-image">Attachment</label>
          <input class="form-control" type="file" id="ecommerce-category-image" />
        </div>
        <!-- Parent category -->
        <div class="mb-6 ecommerce-select2-dropdown">
          <label class="form-label" for="ecommerce-category-parent-category">Parent category</label>
          <select id="ecommerce-category-parent-category" name="parent_id" class="select2 form-select"
            data-placeholder="Select parent category">
            <option value="">Select parent Category</option>
            @foreach($parentCategories as $parent)
              <option value="{{ $parent->id }}">{{ $parent->name }}</option>
            @endforeach
          </select>
        </div>
        <!-- Description -->
        <div class="mb-6">
          <label class="form-label">Description</label>
          <div class="form-control p-0 py-1">
            <div class="comment-editor border-0" id="ecommerce-category-description"></div>
            <div class="comment-toolbar border-0 rounded">
              <div class="d-flex justify-content-end">
                <span class="ql-formats me-0">
                  <button class="ql-bold"></button>
                  <button class="ql-italic"></button>
                  <button class="ql-underline"></button>
                  <button class="ql-list" value="ordered"></button>
                  <button class="ql-list" value="bullet"></button>
                  <button class="ql-link"></button>
                  <button class="ql-image"></button>
                </span>
              </div>
            </div>
          </div>
        </div>
        <!-- Status -->
        <div class="mb-6 ecommerce-select2-dropdown">
          <label class="form-label">Select category status</label>
          <select id="ecommerce-category-status" name="status" class="select2 form-select" data-placeholder="Select category status">
            <option value="">Select category status</option>
            <option value="Scheduled">Scheduled</option>
            <option value="Publish">Publish</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
        <!-- Submit and reset -->
        <div class="mb-6">
          <input type="hidden" name="description" id="hiddenDescription">
          <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Add</button>
          <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Discard</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection