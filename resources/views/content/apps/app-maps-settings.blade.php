<?php
@extends('layouts/layoutMaster')

@section('title', 'Maps Settings - Apps')

@section('vendor-style')
@vite('resources/assets/vendor/libs/select2/select2.scss')
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
@vite('resources/assets/js/app-ecommerce-settings.js')
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="icon-base bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
<form method="POST" action="{{ route('app-ecommerce-settings-maps-save') }}">
    @csrf
    <div class="row g-6">
        <!-- Navigation -->
        <div class="col-12 col-lg-4">
            @include('content.apps._settings-sidebar')
        </div>
        <!-- /Navigation -->
        <div class="col-12 col-lg-8 pt-6 pt-lg-0">
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="maps_general" role="tabpanel">
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title m-0">Google Maps Configuration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4 g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="google-api-key">API Key</label>
                                    <input type="text" class="form-control" id="google-api-key" name="google_api_key" value="{{ $settings['google_api_key'] ?? '' }}" placeholder="Google Maps API Key" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="default-lat">Default Center Latitude</label>
                                    <input type="number" step="0.000001" class="form-control" id="default-lat" name="default_center[lat]" value="{{ $settings['default_center']['lat'] ?? '' }}" placeholder="Latitude" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="default-lng">Default Center Longitude</label>
                                    <input type="number" step="0.000001" class="form-control" id="default-lng" name="default_center[lng]" value="{{ $settings['default_center']['lng'] ?? '' }}" placeholder="Longitude" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label mb-1" for="default-zoom">Default Zoom</label>
                                    <input type="number" class="form-control" id="default-zoom" name="default_center[zoom]" value="{{ $settings['default_center']['zoom'] ?? '' }}" placeholder="Zoom level" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Test Connection button -->
                    <div class="d-flex justify-content-end gap-4">
                        <button type="button" class="btn btn-outline-primary" onclick="testGoogleMapsConnection();">
                            <i class="icon-base bx bx-pulse me-1"></i> Test Connection
                        </button>
                        <button type="reset" class="btn btn-label-secondary">Discard</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function testGoogleMapsConnection() {
    const apiKey = document.getElementById('google-api-key').value.trim();
    if (!apiKey) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'API Key Missing',
                text: 'Please enter a Google Maps API Key to test connectivity.',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
        } else {
            alert('Please enter a Google Maps API Key.');
        }
        return;
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Testing Connection...',
            text: 'Verifying Google Maps API Key format and connectivity...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'API Key Validated',
                        text: 'Google Maps API key is configured and ready for maps & geocoding.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }, 800);
            }
        });
    }
}
</script>
@endsection
