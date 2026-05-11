@extends('layouts/layoutMaster')

@section('title', 'eCommerce Add Product - Apps')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/katex.scss',
'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/dropzone/dropzone.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/tagify/tagify.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/quill/quill.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/dropzone/dropzone.js',
'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/tagify/tagify.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/app-ecommerce-product-add.js'])
@endsection

@php
$isEdit = isset($product);
@endphp

@section('content')
<form action="{{ $isEdit ? route('app-ecommerce-product-update', $product->id) : route('app-ecommerce-product-add-post') }}" method="POST">
@csrf
@if($isEdit)
  @method('PUT')
@endif
<div class="app-ecommerce">
  <!-- Add Product -->
  <div
    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
    <div class="d-flex flex-column justify-content-center">
      <h4 class="mb-1">{{ $isEdit ? 'Edit Product' : 'Add a new Product' }}</h4>
      <p class="mb-0">Orders placed across your store</p>
    </div>
    <div class="d-flex align-content-center flex-wrap gap-4">
      <div class="d-flex gap-4">
        <a href="{{ route('app-ecommerce-product-list') }}" class="btn btn-label-secondary">Discard</a>
      </div>
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update product' : 'Publish product' }}</button>
    </div>
  </div>

  <div class="row">
    <!-- First column-->
    <div class="col-12 col-lg-8">
      <!-- Product Information -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-tile mb-0">Product information</h5>
        </div>
        <div class="card-body">
          <div class="mb-6">
            <label class="form-label" for="ecommerce-product-name">Name</label>
            <div class="input-group">
              <input type="text" class="form-control" id="ecommerce-product-name" placeholder="Product title (e.g., Wireless Bluetooth Headphones)"
                name="productTitle" value="{{ $product->name ?? '' }}" aria-label="Product title" />
              <button class="btn btn-outline-primary" type="button" id="btn-generate-ai">
                <i class="bx bx-bot me-1"></i> Generate Content
              </button>
            </div>
            <div class="form-text">Enter a title and click generate to auto-fill description and SEO tags using AI.</div>
          </div>
          <div class="row mb-6">
            <div class="col"><label class="form-label" for="ecommerce-product-sku">SKU</label> <input type="text"
                class="form-control" id="ecommerce-product-sku" placeholder="SKU" name="productSku"
                value="{{ $product->sku ?? '' }}" aria-label="Product SKU" /></div>
            <div class="col"><label class="form-label" for="ecommerce-product-barcode">Barcode</label> <input
                type="text" class="form-control" id="ecommerce-product-barcode" placeholder="0123-4567"
                name="productBarcode" value="{{ $product->barcode ?? '' }}" aria-label="Product barcode" /></div>
          </div>
          <!-- Description -->
          <div>
            <label class="mb-1">Description (Optional)</label>
            <textarea class="form-control" name="description" rows="5">{{ $product->description ?? '' }}</textarea>
          </div>
        </div>
      </div>
      <!-- /Product Information -->
      <!-- Media -->
      <div class="card mb-6">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0 card-title">Product Image</h5>
          <a href="javascript:void(0);" class="fw-medium">Add media from URL</a>
        </div>
        <div class="card-body">
          <div class="dropzone needsclick p-0" id="dropzone-basic">
            <input type="hidden" name="productImage" id="productImage" value="{{ $product->image ?? '' }}">
            <div class="dz-message needsclick">
              <p class="h4 needsclick pt-4 mb-2">Drag and drop your image here</p>
              <p class="h6 text-body-secondary d-block fw-normal mb-3">or</p>
              <span class="needsclick btn btn-sm btn-label-primary" id="btnBrowse">Browse image</span>
            </div>
          </div>
        </div>
      </div>
      <!-- /Media -->
      <!-- Variants -->
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="card-title mb-0">Variants</h5>
      </div>
      <div class="card-body">
        <div id="variants-container">
          @if(isset($product) && $product->variants->count() > 0)
            @foreach($product->variants as $index => $variant)
            <div class="variant-row row mb-4 border-bottom pb-4">
              <div class="col-md-3">
                <label class="form-label">Attribute</label>
                <input type="text" name="variants[{{$index}}][name]" class="form-control" value="{{$variant->attribute_name}}" placeholder="Size">
              </div>
              <div class="col-md-3">
                <label class="form-label">Value</label>
                <input type="text" name="variants[{{$index}}][value]" class="form-control" value="{{$variant->attribute_value}}" placeholder="XL">
              </div>
              <div class="col-md-2">
                <label class="form-label">Price</label>
                <input type="number" name="variants[{{$index}}][price]" class="form-control" value="{{$variant->price}}">
              </div>
              <div class="col-md-2">
                <label class="form-label">Qty</label>
                <input type="number" name="variants[{{$index}}][qty]" class="form-control" value="{{$variant->qty}}">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-label-danger remove-variant">Remove</button>
              </div>
            </div>
            @endforeach
          @endif
        </div>
        <button type="button" class="btn btn-primary" id="add-variant">Add Variant</button>
      </div>
    </div>
    <!-- /Variants -->

    <!-- Inventory -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Inventory</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- Navigation -->
            <div class="col-12 col-md-4 col-xl-5 col-xxl-4 mx-auto card-separator">
              <div class="d-flex justify-content-between flex-column mb-4 mb-md-0 pe-md-4">
                <div class="nav-align-left">
                  <ul class="nav nav-pills flex-column w-100">
                    <li class="nav-item">
                      <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#restock">
                        <i class="icon-base bx bx-cube icon-18px me-1_5"></i>
                        <span class="align-middle">Restock</span>
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- /Navigation -->
            <!-- Options -->
            <div class="col-12 col-md-8 col-xl-7 col-xxl-8 pt-6 pt-md-0">
              <div class="tab-content p-0 ps-md-4">
                <!-- Restock Tab -->
                <div class="tab-pane fade show active" id="restock" role="tabpanel">
                  <h6 class="text-body">Options</h6>
                  <label class="form-label" for="ecommerce-product-stock">Total Quantity</label>
                  <div class="row mb-4 g-4 pe-md-4">
                    <div class="col-12">
                      <input type="number" class="form-control" id="ecommerce-product-stock" placeholder="Quantity"
                        name="quantity" value="{{ $product->qty ?? '' }}" aria-label="Quantity" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /Options-->
          </div>
        </div>
      </div>
      <!-- /Inventory -->
    </div>
    <!-- /Second column -->

    <!-- Second column -->
    <div class="col-12 col-lg-4">
      <!-- Pricing Card -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Pricing</h5>
        </div>
        <div class="card-body">
          <!-- Base Price -->
          <div class="mb-6">
            <label class="form-label" for="ecommerce-product-price">Base Price</label>
            <input type="number" class="form-control" id="ecommerce-product-price" placeholder="Price"
              name="productPrice" value="{{ $product->price ?? '' }}" aria-label="Product price" />
          </div>
          <!-- Discounted Price -->
          <div class="mb-6">
            <label class="form-label" for="ecommerce-product-discount-price">Discounted Price</label>
            <input type="number" class="form-control" id="ecommerce-product-discount-price"
              placeholder="Discounted Price" name="productDiscountedPrice" value="{{ $product->compare_at_price ?? '' }}" aria-label="Product discounted price" />
          </div>
        </div>
      </div>
      <!-- /Pricing Card -->
      <!-- Organize Card -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Organize</h5>
        </div>
        <div class="card-body">
          <!-- Category -->
          <div class="d-flex justify-content-between align-items-center">
            <div class="mb-6 col ecommerce-select2-dropdown">
              <label class="form-label mb-1" for="category-org">
                <span>Category</span>
              </label>
              <select id="category-org" name="category_id" class="select2 form-select" data-placeholder="Select Category">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $isEdit && $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- Status -->
          <div class="mb-6 col ecommerce-select2-dropdown">
            <label class="form-label mb-1" for="status-org">Status </label>
            <select id="status-org" name="status" class="select2 form-select" data-placeholder="Published">
              <option value="Published" {{ $isEdit && $product->is_active ? 'selected' : '' }}>Published</option>
              <option value="Inactive" {{ $isEdit && !$product->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
        </div>
      </div>
      <!-- /Organize Card -->
      <!-- SEO Card -->
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Search Engine Optimization</h5>
        </div>
        <div class="card-body">
          <div class="mb-6">
            <label class="form-label" for="meta-title">Meta Title</label>
            <input type="text" class="form-control" id="meta-title" placeholder="Meta Title" name="meta_title" value="{{ $product->meta_title ?? '' }}">
          </div>
          <div class="mb-6">
            <label class="form-label">Meta Description</label>
            <textarea class="form-control" rows="3" name="meta_description" placeholder="Meta Description">{{ $product->meta_description ?? '' }}</textarea>
          </div>
        </div>
      </div>
    </div>
    <!-- /Second column -->
  </div>
</div>
</form>


@endsection
