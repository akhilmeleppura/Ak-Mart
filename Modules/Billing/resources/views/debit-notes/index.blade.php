@extends('layouts/layoutMaster')
@section('title', 'List - Debit Notes')
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss'])
@endsection
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection
@section('page-script')
    @vite('Modules/Billing/resources/assets/js/debit-notes-list.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
@section('content')
    <!-- Debit Note List Widget -->
    <div class="card mb-6">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-center card-widget-1 border-end pb-4 pb-sm-0">
                            <div>
                                <h4 class="mb-0">24</h4>
                                <p class="mb-0">Invoices</p>
                            </div>
                            <div class="avatar me-sm-6 w-px-42 h-px-42">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="icon-base bx bx-file icon-26px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-6" />
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div
                            class="d-flex justify-content-between align-items-center card-widget-2 border-end pb-4 pb-sm-0">
                            <div>
                                <h4 class="mb-0">165</h4>
                                <p class="mb-0">Debit Notes</p>
                            </div>
                            <div class="avatar me-lg-6 w-px-42 h-px-42">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="icon-base bx bx-file icon-26px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none" />
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div
                            class="d-flex justify-content-between align-items-center border-end pb-4 pb-sm-0 card-widget-3">
                            <div>
                                <h4 class="mb-0">$2.46k</h4>
                                <p class="mb-0">Paid</p>
                            </div>
                            <div class="avatar me-sm-6 w-px-42 h-px-42">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="icon-base bx bx-check-double icon-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">$876</h4>
                                <p class="mb-0">Unpaid</p>
                            </div>
                            <div class="avatar w-px-42 h-px-42">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="icon-base bx bx-error-circle icon-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Debit Note List Table -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="debit-note-list-table table border-top">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>#</th>
                        <th>Status</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th class="text-truncate">Issued Date</th>
                        <th>Balance</th>
                        <th>Credit Note Status</th>
                        <th class="cell-fit">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
