{{-- Extends the main application layout --}}
@extends('layouts.layoutMaster')

@section('title', 'Accounting') {{-- Default title, can be overridden --}}

{{-- Accounting-specific vendor styles --}}
@section('vendor-style')
    @parent
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/select2/select2.scss',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
    ])
@endsection

{{-- Accounting-specific vendor scripts --}}
@section('vendor-script')
    @parent
    @vite([
        'resources/assets/vendor/libs/moment/moment.js',
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'resources/assets/vendor/libs/select2/select2.js',
        'resources/assets/vendor/libs/@form-validation/popular.js',
        'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'resources/assets/vendor/libs/@form-validation/auto-focus.js',
        'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
    ])
@endsection


{{-- Accounting-specific scripts --}}
@section('page-script')
    @vite([
        'resources/js/accounting/accounting-core.js',
        'resources/js/HS/sweet-alerts.js',
        'resources/assets/js/app-ecommerce-settings.js',
    ])

    @stack('accounting-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection