@extends($templateView)

@section('template-content')
    <div class="row invoice-preview">
        <!-- Credit Note -->
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-6">
            <div class="card invoice-preview-card p-sm-12 p-6">
                <div class="card-body invoice-preview-header rounded">
                    <div
                        class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column align-items-xl-center align-items-md-start align-items-sm-center align-items-start">
                        <div class="mb-xl-0 mb-6 text-heading">
                            <div class="d-flex svg-illustration mb-6 gap-2 align-items-center">
                                @if ($branchLogo)
                                    <img src="{{ $branchLogo }}" alt="Branch Logo" class="app-brand-logo"
                                        style="height: 40px;">
                                @else
                                    <span class="app-brand-logo demo">@include('_partials.macros')</span>
                                @endif
                                <span class="app-brand-text demo fw-bold ms-50 lh-1">
                                    {{ $creditNote->company?->name ?? config('variables.templateName') }}
                                </span>
                            </div>
                            @if ($creditNote->branch)
                                @php $branch = $creditNote->branch; @endphp
                                <p class="mb-1"><strong>{{ $branch->name ?? 'N/A' }}</strong></p>
                                <p class="mb-2">{{ $branch->address ?? 'N/A' }}</p>
                                <p class="mb-2">
                                    {{ $branch->city ?? '' }}{{ $branch->city && $branch->state ? ', ' : '' }}{{ $branch->state ?? '' }}
                                    {{ $branch->zip_code ?? '' }}</p>
                                <p class="mb-2">{{ $branch->country ?? '' }}</p>
                                <p class="mb-2">{{ $branch->email ?? 'N/A' }}</p>
                                <p class="mb-0">{{ $branch->phone ?? 'N/A' }}</p>
                            @else
                                <p class="mb-2">Office 149, 450 South Brand Brooklyn</p>
                                <p class="mb-2">San Diego County, CA 91905, USA</p>
                                <p class="mb-2">support@demo.com</p>
                                <p class="mb-0">+1 (123) 456 7891</p>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-6">Credit Note
                                #{{ $creditNote->document_prefix }}-{{ $creditNote->document_number }}</h5>
                            <div class="mb-1 text-heading">
                                <span>Date Issued:</span>
                                <span
                                    class="fw-medium">{{ $creditNote->issue_date ? $creditNote->issue_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="text-heading">
                                <span>Date Due:</span>
                                <span
                                    class="fw-medium">{{ $creditNote->due_date ? $creditNote->due_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="row">
                        <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-6 mb-sm-0 mb-6">
                            <h6>Credit Note Details:</h6>
                            <p class="mb-1"><strong>Payment Status:</strong>
                                <span
                                    class="badge bg-label-{{ $creditNote->payment_status == 'paid' ? 'success' : ($creditNote->payment_status == 'unpaid' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($creditNote->payment_status) }}
                                </span>
                            </p>
                            @if ($creditNote->invoice)
                                <p class="mb-1"><strong>Associated Invoice:</strong>
                                    <a href="{{ route('billing.invoices.show', $creditNote->invoice->id) }}">
                                        #{{ $creditNote->invoice->document_prefix }}-{{ $creditNote->invoice->document_number }}
                                    </a>
                                </p>
                            @endif
                            @if ($creditNote->note)
                                <p class="mb-0"><strong>Note:</strong> {{ $creditNote->note }}</p>
                            @endif
                        </div>
                        <div class="col-xl-6 col-md-12 col-sm-7 col-12">
                            <h6>Payment Details:</h6>
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="pe-4">Total Credit:</td>
                                        <td class="fw-medium">
                                            ${{ number_format($creditNote->total_amount, 2) }}
                                        </td>
                                    </tr>
                                    @if ($creditNote->branch && $creditNote->branch->bank_name)
                                        <tr>
                                            <td class="pe-4">Bank name:</td>
                                            <td>{{ $creditNote->branch->bank_name }}</td>
                                        </tr>
                                    @endif
                                    @if ($creditNote->branch && $creditNote->branch->bank_country)
                                        <tr>
                                            <td class="pe-4">Country:</td>
                                            <td>{{ $creditNote->branch->bank_country }}</td>
                                        </tr>
                                    @endif
                                    @if ($creditNote->branch && $creditNote->branch->iban)
                                        <tr>
                                            <td class="pe-4">IBAN:</td>
                                            <td>{{ $creditNote->branch->iban }}</td>
                                        </tr>
                                    @endif
                                    @if ($creditNote->branch && $creditNote->branch->swift_code)
                                        <tr>
                                            <td class="pe-4">SWIFT code:</td>
                                            <td>{{ $creditNote->branch->swift_code }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="table-responsive border border-bottom-0 border-top-0 rounded">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($creditNote->items ?? []) as $item)
                                <tr>
                                    <td>{{ $item->item?->name ?? 'N/A' }}</td>
                                    <td>{{ $item->item?->description ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->selling_unit_price, 2) }}</td>
                                    <td>{{ $item->discount_rate }}%</td>
                                    <td>{{ $item->tax?->percentage . '%' ?? '0%' }}</td>
                                    <td>${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No items available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table m-0 table-borderless">
                        <tbody>
                            <tr>
                                <td class="align-top pe-6 ps-0 py-6 text-body">
                                    <p class="mb-1">
                                        <span class="me-2 h6">Created By:</span>
                                        <span>{{ $creditNote->createdBy?->name ?? '-' }}</span>
                                    </p>
                                    <span>Thank you for your business</span>
                                </td>
                                <td class="px-0 py-6 w-px-100">
                                    <p class="mb-2">Subtotal:</p>
                                    @if ($creditNote->document_discount_amount > 0)
                                        <p class="mb-2">Discount
                                            @if ($creditNote->document_discount_type == 1 && $creditNote->document_discount_rate)
                                                ({{ $creditNote->document_discount_rate }}%)
                                            @endif:
                                        </p>
                                    @endif
                                    <p class="mb-2 border-bottom pb-2">Tax:</p>
                                    <p class="mb-0">Total:</p>
                                </td>
                                <td class="text-end px-0 py-6 w-px-100 fw-medium text-heading">
                                    <p class="fw-medium mb-2">${{ number_format($creditNote->sub_total, 2) }}</p>
                                    @if ($creditNote->document_discount_amount > 0)
                                        <p class="fw-medium mb-2">
                                            -${{ number_format($creditNote->document_discount_amount, 2) }}</p>
                                    @endif
                                    <p class="fw-medium mb-2 border-bottom pb-2">
                                        ${{ number_format($creditNote->tax_amount, 2) }}</p>
                                    <p class="fw-medium mb-0">
                                        ${{ number_format($creditNote->total_amount, 2) }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr class="mt-0 mb-6">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-12">
                            <span class="fw-medium text-heading">Note:</span>
                            <span>{{ $creditNote->note ?? 'Thank you for your business!' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Credit Note -->
        <!-- Credit Note Actions -->
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary d-grid w-100 mb-4" data-bs-toggle="offcanvas"
                        data-bs-target="#sendInvoiceOffcanvas">
                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                                class="icon-base bx bx-paper-plane icon-sm me-2"></i>Send Credit Note</span>
                    </button>
                    <a href="{{ route('billing.credit-notes.download', $creditNote->id) }}"
                        class="btn btn-label-secondary d-grid w-100 mb-4">
                        Download
                    </a>
                    <div class="d-flex mb-4">
                        <a class="btn btn-label-secondary d-grid w-100 me-4" target="_blank"
                            href="{{ route('billing.credit-notes.print', $creditNote->id) }}">
                            Print
                        </a>
                        <a href="{{ route('billing.credit-notes.edit', $creditNote->id) }}"
                            class="btn btn-label-secondary d-grid w-100">
                            Edit </a>
                    </div>
                    <button class="btn btn-danger d-grid w-100" onclick="confirmDelete()">
                        <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                                class="icon-base bx bx-trash icon-sm me-2"></i>Delete</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- /Credit Note Actions -->
    </div>
    <!-- Offcanvas -->
    @include('_partials/_offcanvas/offcanvas-send-invoice')
    <!-- /Offcanvas -->
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        });
    }
</script>

<form id="delete-form" action="{{ route('billing.credit-notes.destroy', $creditNote->id) }}" method="POST"
    style="display: none;">
    @csrf
    @method('DELETE')
</form>