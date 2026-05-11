@extends('layouts/layoutMaster')



@section('vendor-style')
    @vite('resources/assets/vendor/libs/flatpickr/flatpickr.scss')
@endsection

@section('page-style')
    @vite('resources/assets/vendor/scss/pages/app-invoice.scss')
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/offcanvas-add-payment.js', 'resources/assets/js/offcanvas-send-invoice.js'])
@endsection
