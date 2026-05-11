@extends('HS/standard-layout')

@section('page-style')
    <style>
        /* --- MODIFIED STYLES --- */

        /* Hide header, footer, and spacers by default (for screen view) */
        .page-header,
        .page-footer,
        .page-header-space,
        .page-footer-space {
            display: none;
        }

        /* --- UNCHANGED STYLES --- */

        .page-header img {
            max-height: {{ $headerHeight ?? '60px' }};
            max-width: 100%;
            object-fit: contain;
        }

        .page-footer img {
            max-height: {{ $footerHeight ?? '60px' }};
            max-width: 100%;
            object-fit: contain;
        }

        /* Print button styling */
        #printButton {
            transition: opacity 0.3s ease;
        }

        #printButton:hover {
            opacity: 0.8;
        }


        /* --- PRINT-ONLY STYLES --- */
        @media print {
            body,
            html {
                width: 100%;
                height: auto;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            #printButton,
            .layout-navbar,
            .layout-menu,
            .layout-footer,
            .footer-light,
            .btn,
            button {
                display: none !important;
            }

            #printMe {
                width: 100% !important;
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                background: white !important;
                border: none !important;
                box-shadow: none !important;
            }

            .card,
            .card-body {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                background: white !important;
            }

            /* --- MODIFIED PRINT STYLES --- */
            
            /* Display header and footer ONLY for print */
            .page-header {
                display: flex; /* Make it visible for print */
                justify-content: center;
                align-items: flex-start;
                width: 100%;
                padding: 10px 0 15px 0;
                border-bottom: 1px solid #ccc;
                min-height: {{ $headerHeight ?? '60px' }};
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                background: white;
            }

            .page-footer {
                display: flex; /* Make it visible for print */
                justify-content: center;
                align-items: flex-end;
                width: 100%;
                padding: 15px 0 10px 0;
                border-top: 1px solid #ccc;
                min-height: {{ $footerHeight ?? '60px' }};
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                background: white;
            }
            
            /* Display spacers ONLY for print to prevent content overlap */
            .page-header-space,
            .page-footer-space {
                display: block; /* Make them take up space */
            }

            .page-header-space {
                height: {{ $headerHeight ?? '100px' }};
            }

            .page-footer-space {
                height: {{ $footerHeight ?? '90px' }};
            }
            
            /* --- END OF MODIFICATIONS --- */

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            .page {
                page-break-inside: avoid;
            }

            @page {
                margin: 0.5in;
                size: A4;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <button id="printButton" class="btn btn-primary mb-3">
                <i class="fas fa-print me-2"></i>Print Invoice
            </button>

            <div class="card" id="printMe">
                <div class="card-body">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <td>
                                    <!-- This header is now only visible on print -->
                                    <div class="page-header">
                                        @if ($template && $template->header_image)
                                            <img src="{{ asset('storage/' . $template->header_image) }}"
                                                alt="Company Header" />
                                        @endif
                                    </div>
                                    <div class="page-header-space"></div>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="page">
                                        @yield('template-content')
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <!-- This footer is now only visible on print -->
                                    <div class="page-footer-space"></div>
                                    <div class="page-footer">
                                        @if ($template && $template->footer_image)
                                            <img src="{{ asset('storage/' . $template->footer_image) }}"
                                                alt="Company Footer" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('printButton');
        if (btn) {
            btn.addEventListener('click', function () {
                window.print();
            });
        }
    });
</script>
@endsection