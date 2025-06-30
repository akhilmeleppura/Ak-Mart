@extends('layouts/layoutMaster')

@section('title', 'Treeview - Extended UI')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite('resources/assets/vendor/libs/jstree/jstree.scss')
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite('resources/assets/vendor/libs/jstree/jstree.js')
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite('resources/assets/js/extended-ui-treeview.js')
@endsection

@section('content')
  <!-- JSTree -->
  <div class="row gy-6">
    <!-- Basic -->
    <div class="col-md-6 col-12">
      <div class="card">
        <h5 class="card-header">Basic</h5>
        <div class="card-body">
          <div id="jstree-basic">
            <ul>
              <li data-jstree='{"icon" : "icon-base bx bx-folder"}'>
                css
                <ul>
                  <li data-jstree='{"icon" : "icon-base bx bx-folder"}'>app.css</li>
                  <li data-jstree='{"icon" : "icon-base bx bx-folder"}'>style.css</li>
                </ul>
              </li>
              <li class="jstree-open" data-jstree='{"icon" : "icon-base bx bx-folder"}'>
                img
                <ul data-jstree='{"icon" : "icon-base bx bx-folder"}'>
                  <li data-jstree='{"icon" : "icon-base bx bx-folder"}'>bg.jpg</li>
                  <li data-jstree='{"icon" : "icon-base bx bx-folder"}'>logo.png</li>
                  <li data-jstree='{"icon" : "icon-base bx bx-folder"}'>avatar.png</li>
                </ul>
              </li>
              <li class="jstree-open" data-jstree='{"icon" : "icon-base bx bx-folder"}'>
                js
                <ul>
                  <li data-jstree='{"icon" : "icon-base bx bx-folder"}'>jquery.js</li>
                  <li data-jstree='{"icon" : "icon-base bx bx-folder"}'>app.js</li>
                </ul>
              </li>
              <li data-jstree='{"icon" : "icon-base bx bx-file"}'>index.html</li>
              <li data-jstree='{"icon" : "icon-base bx bx-file"}'>page-one.html</li>
              <li data-jstree='{"icon" : "icon-base bx bx-file"}'>page-two.html</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- /Basic -->
    <!-- Custom Icons -->
    <div class="col-md-6 col-12">
      <div class="card">
        <h5 class="card-header">Custom Icons</h5>
        <div class="card-body">
          <div id="jstree-custom-icons"></div>
        </div>
      </div>
    </div>
    <!-- /Custom Icons -->

    <!-- Context Menu -->
    <div class="col-md-6 col-12">
      <div class="card">
        <h5 class="card-header">Context Menu</h5>
        <div class="card-body">
          <div id="jstree-context-menu" class="overflow-auto"></div>
        </div>
      </div>
    </div>
    <!-- /Context Menu -->
    <!-- Drag & Drop -->
    <div class="col-md-6 col-12">
      <div class="card">
        <h5 class="card-header">Drag & Drop</h5>
        <div class="card-body">
          <div id="jstree-drag-drop" class="overflow-auto"></div>
        </div>
      </div>
    </div>
    <!-- /Drag & Drop -->

    <!-- Checkbox -->
    <div class="col-md-6 col-12">
      <div class="card">
        <h5 class="card-header">Checkboxes</h5>
        <div class="card-body">
          <div id="jstree-checkbox"></div>
        </div>
      </div>
    </div>
    <!-- /Checkbox -->
    <!-- Ajax -->
    <div class="col-md-6 col-12">
      <div class="card">
        <h5 class="card-header">Ajax</h5>
        <div class="card-body">
          <div id="jstree-ajax"></div>
        </div>
      </div>
    </div>
    <!-- Ajax -->
  </div>
  <!-- /JSTree -->
@endsection
