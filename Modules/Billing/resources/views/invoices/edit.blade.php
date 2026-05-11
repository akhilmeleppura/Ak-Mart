@extends('layouts/layoutMaster')
@section('title', 'Edit - Invoice')
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/select2/select2.scss'])
@endsection
@section('page-style')
    @vite('resources/assets/vendor/scss/pages/app-invoice.scss')
@endsection
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js', 'resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
    {{-- We removed the generic app-invoice-add.js to avoid conflicts --}}
    @vite(['resources/assets/js/offcanvas-send-invoice.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Flatpickr for Issued Date
            const issueDatePicker = flatpickr(".invoice-date", {
                dateFormat: "m/d/Y",
                defaultDate: "{{ $invoice->issue_date ? $invoice->issue_date->format('m/d/Y') : '' }}",
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        let dueDate = new Date(selectedDates[0]);
                        dueDate.setDate(dueDate.getDate() + 7);
                        dueDatePicker.setDate(dueDate, true);
                    }
                }
            });

            // Flatpickr for Due Date
            const dueDatePicker = flatpickr(".due-date", {
                dateFormat: "m/d/Y",
                defaultDate: "{{ $invoice->due_date ? $invoice->due_date->format('m/d/Y') : '' }}",
                minDate: "today"
            });

            // =========================================================================================
            //  SELECT2 & REPEATER INITIALIZATION (FIXED)
            // =========================================================================================
            $(document).ready(function() {
                // Initialize Select2 for customer dropdown
                const clientSelect = $('#clientSelect');
                if (clientSelect.length) {
                    clientSelect.select2({
                        placeholder: '-- Select Customer --',
                        allowClear: true,
                        width: '100%'
                    });

                    clientSelect.on('select2:select', function(e) {
                        handleClientSelection(this);
                    });

                    clientSelect.on('select2:clear', function(e) {
                        clearClientDetails();
                    });
                }

                // Function to handle client selection
                function handleClientSelection(selectElement) {
                    let selected = selectElement.options[selectElement.selectedIndex];

                    if (selected && selected.value && selected.value !== '') {
                        let company = selected.getAttribute('data-company') || '';
                        let address = selected.getAttribute('data-address') || '';
                        let city = selected.getAttribute('data-city') || '';
                        let state = selected.getAttribute('data-state') || '';
                        let zip = selected.getAttribute('data-zip') || '';
                        let phone = selected.getAttribute('data-phone') || '';
                        let email = selected.getAttribute('data-email') || '';

                        document.getElementById('clientCompany').textContent = company || selected.textContent;

                        let fullAddress = [address, city, state, zip].filter(Boolean).join(', ');
                        document.getElementById('clientAddress').textContent = fullAddress;
                        document.getElementById('clientPhone').textContent = phone;
                        document.getElementById('clientEmail').textContent = email;
                        document.getElementById('client_id').value = selected.value;
                    } else {
                        clearClientDetails();
                    }
                }

                function clearClientDetails() {
                    document.getElementById('clientCompany').textContent = 'Select a customer to view details';
                    document.getElementById('clientAddress').textContent = '';
                    document.getElementById('clientPhone').textContent = '';
                    document.getElementById('clientEmail').textContent = '';
                    document.getElementById('client_id').value = '';
                }

                // Function to initialize Select2 on item dropdowns
                function initializeItemSelect(element) {
                    const $element = $(element);
                    if ($element.hasClass('select2-hidden-accessible')) {
                        $element.select2('destroy');
                    }
                    $element.select2({
                        placeholder: '-- Select Item --',
                        allowClear: true,
                        width: '100%'
                    });
                    $element.on('select2:select', function(e) {
                        handleItemSelection(this);
                    });
                    $element.on('select2:clear', function(e) {
                        clearItemDetails(this);
                    });
                }

                // Function to handle item selection
                function handleItemSelection(selectElement) {
                    let selected = selectElement.options[selectElement.selectedIndex];
                    let wrapper = selectElement.closest('.repeater-wrapper');
                    if (selected && selected.value && selected.value !== '') {
                        let price = parseFloat(selected.getAttribute('data-price')) || 0;
                        wrapper.querySelector('.selling-unit-price').value = price.toFixed(2);
                        calculateItemTotal(wrapper);
                    } else {
                        clearItemDetails(selectElement);
                    }
                    calculateSubtotal();
                }

                function clearItemDetails(selectElement) {
                    let wrapper = selectElement.closest('.repeater-wrapper');
                    wrapper.querySelector('.selling-unit-price').value = '0.00';
                    wrapper.querySelector('.total-price').value = '0.00';
                    wrapper.querySelector('.item-discount-amount').textContent = '$0.00';
                    wrapper.querySelector('.item-tax-amount').textContent = '$0.00';
                }

                // --- START: JQUERY REPEATER FIX ---
                const invoiceForm = $('.source-item');
                if (invoiceForm.length) {
                    invoiceForm.repeater({
                        show: function() {
                            const $item = $(this);
                            const itemSelect = $item.find('.item-details');
                            initializeItemSelect(itemSelect);

                            // Reset values and displays for the new row
                            const taxDisplay = $item.find('.tax-1')[0];
                            if (taxDisplay) taxDisplay.textContent = '0%';
                            const taxIdField = $item.find('.item-tax-id')[0];
                            if (taxIdField) taxIdField.value = '0';
                            const discountDisplay = $item.find('.discount')[0];
                            if (discountDisplay) discountDisplay.textContent = '0%';
                             // Clear other input values
                            $item.find('.selling-unit-price').val('');
                            $item.find('.quantity').val('1');
                            $item.find('.item-discount-input').val('0');
                            $item.find('.item-tax-select').val('0');

                            $(this).slideDown();
                        },
                        hide: function(deleteElement) {
                            const $item = $(this);
                            const itemSelect = $item.find('.item-details');
                            if (itemSelect.hasClass('select2-hidden-accessible')) {
                                itemSelect.select2('destroy');
                            }
                            if (confirm('Are you sure you want to delete this element?')) {
                                $(this).slideUp(deleteElement, function() {
                                    $(this).remove();
                                    calculateSubtotal(); // Recalculate after removal
                                });
                            }
                        }
                    });

                    // Initialize Select2 for all pre-existing items
                    $('.item-details').each(function() {
                        initializeItemSelect(this);
                    });
                }
                // --- END: JQUERY REPEATER FIX ---


                // Set initial client details after Select2 is initialized
                setTimeout(() => {
                    if (clientSelect.val()) {
                        handleClientSelection(clientSelect[0]);
                    }
                }, 100);
            });
            // =========================================================================================
            // END OF SELECT2 INITIALIZATION
            // =========================================================================================

            // Initialize calculations after a delay to ensure everything is loaded
            setTimeout(() => {
                // Set document-level tax
                if (document.getElementById('tax')) {
                    document.getElementById('tax').value = "{{ $invoice->document_tax_id ?? 0 }}";
                }

                // Set document-level discount
                document.getElementById('discount-type').value = "{{ $invoice->document_discount_type == 1 ? '%' : 'Amount' }}";
                document.getElementById('discount').value = "{{ $invoice->document_discount_rate > 0 ? $invoice->document_discount_rate : $invoice->document_discount_amount }}";

                // Initialize each existing item's calculations
                document.querySelectorAll('.repeater-wrapper').forEach(wrapper => {
                    calculateItemTotal(wrapper);
                });

                calculateSubtotal();
            }, 300);

            // =========================================================================================
            // APPLY CHANGES EVENT LISTENER
            // =========================================================================================
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-apply-changes')) {
                    e.preventDefault();
                    e.stopPropagation();

                    const dropdown = e.target.closest('.dropdown-menu');
                    const repeaterWrapper = dropdown.closest('.repeater-wrapper');

                    const discountValue = parseFloat(dropdown.querySelector('.item-discount-input').value) || 0;

                    const taxSelect = dropdown.querySelector('.item-tax-select');
                    const selectedTaxOption = taxSelect.options[taxSelect.selectedIndex];
                    const taxId = taxSelect.value;
                    const taxPercentage = selectedTaxOption.getAttribute('data-percentage');

                    const discountDisplay = repeaterWrapper.querySelector('.discount');
                    discountDisplay.textContent = discountValue + '%';

                    repeaterWrapper.querySelector('.item-tax-id').value = taxId;

                    const taxDisplay = repeaterWrapper.querySelector('.tax-1');
                    taxDisplay.textContent = taxPercentage + '%';

                    calculateItemTotal(repeaterWrapper);
                    calculateSubtotal();

                    const dropdownToggle = repeaterWrapper.querySelector('[data-bs-toggle="dropdown"]');
                    const bsDropdown = bootstrap.Dropdown.getInstance(dropdownToggle);
                    if (bsDropdown) {
                        bsDropdown.hide();
                    }

                    const priceDisplays = [
                        repeaterWrapper.querySelector('.item-discount-amount'),
                        repeaterWrapper.querySelector('.item-tax-amount'),
                        repeaterWrapper.querySelector('.total-price')
                    ];
                    priceDisplays.forEach(el => el.classList.add('fw-bold'));
                    setTimeout(() => {
                        priceDisplays.forEach(el => el.classList.remove('fw-bold'));
                    }, 600);
                }
            });
        });

        // Calculate item total with discount and tax
        function calculateItemTotal(row) {
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.selling-unit-price').value) || 0;
            const discountPercent = parseFloat(row.querySelector('.discount').textContent) || 0;

            const taxText = row.querySelector('.tax-1').textContent.replace('%', '').trim();
            const taxPercent = parseFloat(taxText) || 0;

            const baseAmount = qty * unitPrice;
            const discountAmount = baseAmount * (discountPercent / 100);
            const taxableAmount = baseAmount - discountAmount;
            const taxAmount = taxableAmount * (taxPercent / 100);
            const totalAmount = taxableAmount + taxAmount;

            row.querySelector('.item-discount-amount').textContent = '$' + discountAmount.toFixed(2);
            row.querySelector('.item-tax-amount').textContent = '$' + taxAmount.toFixed(2);
            row.querySelector('.total-price').value = totalAmount.toFixed(2);
        }

        // Native change event listener for other dropdowns (discount-type, tax)
        document.addEventListener('change', function(e) {
            if (e.target.id === "discount-type" || e.target.id === "tax") {
                calculateSubtotal();
            }
        });

        // Input event listener for quantity and price changes
        document.addEventListener("input", function(e) {
            if (e.target.classList.contains('quantity') || e.target.classList.contains('selling-unit-price')) {
                let wrapper = e.target.closest('.repeater-wrapper');
                calculateItemTotal(wrapper);
                calculateSubtotal();
            }
            if (e.target.id === "discount") {
                calculateSubtotal();
            }
        });

        function calculateSubtotal() {
            let rows = document.querySelectorAll('.repeater-wrapper');
            let subtotal = 0;
            let itemLevelDiscountTotal = 0;
            let itemLevelTaxTotal = 0;

            rows.forEach(row => {
                let basePrice = (parseFloat(row.querySelector('.selling-unit-price')?.value) || 0) * (
                    parseFloat(row.querySelector('.quantity')?.value) || 0);
                subtotal += basePrice;

                let discountAmountText = row.querySelector('.item-discount-amount')?.textContent || '$0.00';
                let taxAmountText = row.querySelector('.item-tax-amount')?.textContent || '$0.00';

                itemLevelDiscountTotal += parseFloat(discountAmountText.replace('$', ''));
                itemLevelTaxTotal += parseFloat(taxAmountText.replace('$', ''));
            });

            document.getElementById("subtotal-display").textContent = '$' + subtotal.toFixed(2);
            document.getElementById("item-level-discount-total-display").textContent = '-$' +
                itemLevelDiscountTotal.toFixed(2);
            document.getElementById("item-level-tax-total-display").textContent = '+$' + itemLevelTaxTotal.toFixed(2);

            let subtotalAfterItemAdjustments = subtotal - itemLevelDiscountTotal + itemLevelTaxTotal;
            document.getElementById("subtotal-after-item-adjustments-display").textContent = '$' +
                subtotalAfterItemAdjustments.toFixed(2);

            let discountValue = parseFloat(document.getElementById("discount")?.value || 0);
            let discountType = document.getElementById("discount-type")?.value || "%";
            let discountAmount = 0;
            if (discountType === "%") {
                discountAmount = (subtotalAfterItemAdjustments * discountValue) / 100;
            } else {
                discountAmount = discountValue;
            }
            document.getElementById("discount-display").textContent = '-$' + discountAmount.toFixed(2);

            let taxableAmount = subtotalAfterItemAdjustments - discountAmount;
            if (taxableAmount < 0) taxableAmount = 0;
            document.getElementById("taxable-amount-display").textContent = '$' + taxableAmount.toFixed(2);

            let taxSelect = document.getElementById("tax");
            let taxId = taxSelect.value;
            let taxRate = 0;
            if (taxSelect.selectedIndex >= 0) {
                let selectedOption = taxSelect.options[taxSelect.selectedIndex];
                taxRate = parseFloat(selectedOption.getAttribute('data-rate') || 0);
            }

            let taxAmount = (taxableAmount * taxRate) / 100;
            document.getElementById("tax-amount-display").textContent = '+$' + taxAmount.toFixed(2);

            let grandTotal = taxableAmount + taxAmount;
            document.getElementById("grand-total").textContent = '$' + grandTotal.toFixed(2);

            document.getElementById("sub_total").value = subtotal.toFixed(2);
            document.getElementById("document_discount_amount").value = discountAmount.toFixed(2);
            document.getElementById("tax_amount").value = taxAmount.toFixed(2);
            document.getElementById("total_amount").value = grandTotal.toFixed(2);
            document.getElementById("tax_id").value = taxId;
            let discountTypeInt = discountType === "%" ? 1 : 2;
            document.getElementById("document_discount_type").value = discountTypeInt;
            document.getElementById("document_discount_rate").value = discountValue.toFixed(2);
        }

        function saveInvoice() {
            const form = document.getElementById('invoiceForm');
            const clientSelect = document.getElementById('clientSelect');
            const saveButton = document.getElementById('saveInvoiceBtn');

            if (!clientSelect.value) {
                Swal.fire({ icon: 'warning', title: 'Validation Error', text: 'Please select a client', confirmButtonColor: '#3085d6' });
                clientSelect.focus();
                return;
            }

            const items = document.querySelectorAll('.item-details');
            if (!Array.from(items).some(item => item.value)) {
                Swal.fire({ icon: 'warning', title: 'Validation Error', text: 'Please add at least one item', confirmButtonColor: '#3085d6' });
                return;
            }

            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

            let formData = new FormData(form);
            formData.append('_method', 'PUT');

            // *** ADD THIS: Get the selected payment method ID and append it to the form data ***
            const paymentMethodId = document.getElementById('acceptPaymentsVia').value;
            if(paymentMethodId) {
                formData.append('payment_method_id', paymentMethodId);
            }

            let issueDate = document.querySelector('.invoice-date').value;
            if (issueDate) {
                let parts = issueDate.split('/');
                formData.set('issue_date', parts[2] + '-' + parts[0] + '-' + parts[1]);
            }

            let dueDate = document.querySelector('.due-date').value;
            if (dueDate) {
                let parts = dueDate.split('/');
                formData.set('due_date', parts[2] + '-' + parts[0] + '-' + parts[1]);
            }

            formData.set('invoice_number', document.getElementById('invoiceId').value);

            // Clear previous items to avoid duplication before appending new list
            formData.delete('items[]');
            formData.delete('existing_items[]');

            document.querySelectorAll('.repeater-wrapper').forEach((row, index) => {
                const itemId = row.querySelector('.item-details')?.value;
                if (itemId) {
                    const itemDbId = row.querySelector('input[name*="[id]"]')?.value; // More reliable selector
                    const prefix = itemDbId ? `existing_items[${index}]` : `items[${index}]`;

                    if (itemDbId) formData.append(`${prefix}[id]`, itemDbId);

                    formData.append(`${prefix}[item_id]`, itemId);
                    formData.append(`${prefix}[quantity]`, parseFloat(row.querySelector('.quantity')?.value || 0));
                    formData.append(`${prefix}[unit_price]`, parseFloat(row.querySelector('.selling-unit-price')?.value || 0));
                    formData.append(`${prefix}[total_price]`, parseFloat(row.querySelector('.total-price')?.value || 0));
                    formData.append(`${prefix}[discount_percent]`, parseFloat(row.querySelector('.discount').textContent) || 0);
                    formData.append(`${prefix}[tax_id]`, row.querySelector('.item-tax-id').value);
                }
            });

            fetch("{{ route('billing.invoices.update', $invoice->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: `HTTP error! Status: ${response.status}` }));
                    throw new Error(errorData.message || `HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                saveButton.disabled = false;
                saveButton.innerHTML = 'Update';
                const type = data.success ? "success" : "error";
                if (data.success) {
                    Swal.fire({ icon: type, title: 'Success!', text: data.message, confirmButtonColor: '#3085d6' })
                        .then(() => { window.location.href = "{{ route('accounting.billings.index') }}"; });
                } else {
                    Swal.fire({ icon: type, title: 'Error!', text: data.message, confirmButtonColor: '#3085d6' });
                }
            })
            .catch(error => {
                saveButton.disabled = false;
                saveButton.innerHTML = 'Update';
                console.error('AJAX error:', error);
                Swal.fire({ icon: 'error', title: 'Error!', html: `<div>An error occurred while updating.</div><div class="text-muted small mt-2">${error.message}</div>`, confirmButtonColor: '#3085d6' });
            });
        }

        function previewInvoice() {
            Swal.fire({ icon: 'info', title: 'Preview', text: 'Preview functionality is not available yet.', confirmButtonColor: '#3085d6' });
        }
    </script>
@endsection

@section('content')
    @php
        $branchLogo = null;
        if (isset($branch) && !empty($branch)) {
            $extensions = ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp'];
            foreach ($extensions as $ext) {
                $path = public_path('storage/branch_logos/' . $branch->id . '.' . $ext);
                if (file_exists($path)) {
                    $branchLogo = asset('storage/branch_logos/' . $branch->id . '.' . $ext);
                    break;
                }
            }
        }
        $company = $company ?? null;
        $branch = $branch ?? null;
        $clients = $clients ?? [];
        $items = $items ?? [];
        $taxes = $taxes ?? [];
        $invoiceNumber = $invoice->document_prefix . '-' . $invoice->document_number;
    @endphp
    <div class="row invoice-add">
        <!-- Invoice Edit-->
        <div class="col-lg-9 col-12 mb-lg-0 mb-6">
            <div class="card invoice-preview-card p-sm-12 p-6">
                <div class="card-body invoice-preview-header rounded">
                    <div class="d-flex flex-wrap flex-column flex-sm-row justify-content-between text-heading">
                        <div class="mb-md-0 mb-6">
                            <div class="d-flex svg-illustration mb-6 gap-2 align-items-center">
                                <span class="app-brand-logo demo">
                                    @if ($branchLogo)
                                        <img src="{{ $branchLogo }}" alt="Branch Logo" height="40">
                                    @else
                                        @include('_partials.macros')
                                        <span class="app-brand-text demo fw-bold ms-50">{{ config('variables.templateName') }}</span>
                                    @endif
                                </span>
                            </div>
                            @if ($company && $branch)
                                <p class="mb-2">{{ $company->name ?? 'Company Name' }}</p>
                                <p class="mb-2"> {{ $company->city ?? '' }}@if ($company->city && ($company->state || $company->zip)), @endif{{ $company->state ?? '' }} {{ $company->zip ?? '' }} </p>
                                <p class="mb-2">{{ $branch->name ?? 'Branch Name' }}</p>
                                <p class="mb-3">{{ $branch->address ?? 'Branch Address' }}</p>
                            @else
                                <p class="text-muted">No company details available</p>
                            @endif
                        </div>
                        <div class="col-md-5 col-8 pe-0 ps-0 ps-md-2">
                            <dl class="row mb-0 gx-4">
                                <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                    <span class="h5 text-capitalize mb-0 text-nowrap">Invoice</span>
                                </dt>
                                <dd class="col-sm-7">
                                    <input type="text" class="form-control" disabled value="{{ $invoiceNumber }}" id="invoiceId" name="invoice_number" />
                                </dd>
                                <dt class="col-sm-5 mb-1 d-md-flex align-items-center justify-content-end">
                                    <span class="fw-normal">Date Issued:</span>
                                </dt>
                                <dd class="col-sm-7">
                                    <input type="text" class="form-control invoice-date" placeholder="MM/DD/YYYY" />
                                </dd>
                                <dt class="col-sm-5 d-md-flex align-items-center justify-content-end">
                                    <span class="fw-normal">Due Date:</span>
                                </dt>
                                <dd class="col-sm-7 mb-0">
                                    <input type="text" class="form-control due-date" placeholder="MM/DD/YYYY" />
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="row">
                        <div class="col-md-6 col-sm-5 col-12 mb-sm-0 mb-6">
                            <h6>Invoice To:</h6>
                            <select class="form-select mb-4" id="clientSelect" name="client_id">
                                <option value=""></option>
                                @if (count($clients) > 0)
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}"
                                            data-company="{{ $client->company_name ?? '' }}"
                                            data-address="{{ $client->address ?? '' }}"
                                            data-city="{{ $client->city ?? '' }}" data-state="{{ $client->state ?? '' }}"
                                            data-zip="{{ $client->zip ?? '' }}" data-phone="{{ $client->phone ?? '' }}"
                                            data-email="{{ $client->email ?? '' }}"
                                            {{ $invoice->customer_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->name ?? 'Unknown Client' }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No clients available</option>
                                @endif
                            </select>
                            <div id="clientDetails">
                                <p class="mb-1 text-nowrap text-truncate" id="clientCompany">Select a customer to view details</p>
                                <p class="mb-1 text-nowrap text-truncate" id="clientAddress"></p>
                                <p class="mb-1" id="clientPhone"></p>
                                <p class="mb-0 text-nowrap text-truncate" id="clientEmail"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mt-0 mb-6" />
                <div class="card-body pt-0 px-0">
                    <form class="source-item" id="invoiceForm" method="POST" action="{{ route('billing.invoices.update', $invoice->id) }}">
                        @csrf
                        <input type="hidden" name="client_id" id="client_id" value="{{ $invoice->customer_id }}">
                        <input type="hidden" name="sub_total" id="sub_total" value="0">
                        <input type="hidden" name="document_discount_type" id="document_discount_type" value="1">
                        <input type="hidden" name="document_discount_rate" id="document_discount_rate" value="0">
                        <input type="hidden" name="document_discount_amount" id="document_discount_amount" value="0">
                        <input type="hidden" name="tax_id" id="tax_id" value="0">
                        <input type="hidden" name="tax_amount" id="tax_amount" value="0">
                        <input type="hidden" name="total_amount" id="total_amount" value="0">
                        <input type="hidden" name="payment_status" value="{{ $invoice->payment_status }}">

                        <div class="invoice-form-container">
                            <div class="mb-4" data-repeater-list="items">
                                @forelse($invoice->items as $invoiceItem)
                                    <div class="repeater-wrapper pt-0 pt-md-9" data-repeater-item>
                                        <div class="d-flex border rounded position-relative pe-0">
                                            <div class="row w-100 p-6 g-6">
                                                {{-- Important: Name attribute for repeater must be indexed for correct data handling --}}
                                                <input type="hidden" name="id" value="{{ $invoiceItem->id }}" />
                                                <div class="col-md-4 col-12 mb-md-0 mb-4">
                                                    <p class="h6 repeater-title">Item</p>
                                                    <select class="form-select item-details" name="item_id">
                                                        <option value=""></option>
                                                        @if (count($items) > 0)
                                                            @foreach ($items as $item)
                                                                <option value="{{ $item->id }}" data-price="{{ number_format($item->selling_unit_price ?? 0, 2, '.', '') }}" {{ $invoiceItem->item_id == $item->id ? 'selected' : '' }}>
                                                                    {{ $item->name ?? 'Unknown Item' }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="col-md-3 col-12 mb-md-0 mb-4">
                                                    <p class="h6 repeater-title">Selling Unit Price</p>
                                                    <input type="number" class="form-control selling-unit-price" name="unit_price" value="{{ number_format($invoiceItem->selling_unit_price, 2, '.', '') }}" step="0.01" min="0">
                                                    <div class="text-heading mt-2">
                                                        <div class="mb-1"><small class="text-muted">Discount:</small><span class="discount me-2">{{ $invoiceItem->discount_rate ?? 0 }}%</span><small class="text-muted">Amt:</small><span class="item-discount-amount text-success fw-medium">$0.00</span></div>
                                                        <div class="mb-1"><small class="text-muted">Tax:</small><span class="tax-1 me-2">@if($invoiceItem->tax_id){{ optional($invoiceItem->tax)->percentage ?? 0 }}%@else 0% @endif</span><input type="hidden" class="item-tax-id" name="tax_id" value="{{ $invoiceItem->tax_id ?? 0 }}"><small class="text-muted">Amt:</small><span class="item-tax-amount text-primary fw-medium">$0.00</span></div>
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                    <p class="h6 repeater-title">Qty</p>
                                                    <input type="number" class="form-control quantity" name="quantity" value="{{ $invoiceItem->quantity }}" min="1" step="1">
                                                </div>

                                                <div class="col-md-3 col-12 pe-0">
                                                    <p class="h6 repeater-title">Price</p>
                                                    <input type="number" class="form-control total-price" name="total_price" readonly>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                                <i class="icon-base bx bx-x icon-lg cursor-pointer" data-repeater-delete></i>
                                                <div class="dropdown">
                                                    <i class="icon-base bx bx-cog icon-lg cursor-pointer more-options-dropdown" role="button" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false"></i>
                                                    <div class="dropdown-menu dropdown-menu-end w-px-300 p-4">
                                                        <div class="row g-3">
                                                            <div class="col-12"><label class="form-label">Item Level Discount (%)</label><input type="number" class="form-control item-discount-input" value="{{ $invoiceItem->discount_rate ?? 0 }}" min="0" max="100" step="0.01" /><small class="text-muted">0-100%</small></div>
                                                            <div class="col-12"><label class="form-label">Item Level Tax</label><select name="tax_1_input" class="form-select item-tax-select"><option value="0" data-percentage="0" {{ is_null($invoiceItem->tax_id) ? 'selected' : '' }}>0%</option>@foreach($taxes as $tax)<option value="{{ $tax->id }}" data-percentage="{{ $tax->percentage }}" {{ $invoiceItem->tax_id == $tax->id ? 'selected' : '' }}>{{ $tax->name }} ({{ $tax->percentage }}%)</option>@endforeach</select></div>
                                                        </div>
                                                        <div class="dropdown-divider my-4"></div>
                                                        <button type="button" class="btn btn-label-primary btn-apply-changes">Apply Changes</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    {{-- Display a blank item row if the invoice has no items --}}
                                    <div class="repeater-wrapper pt-0 pt-md-9" data-repeater-item>
                                        <div class="d-flex border rounded position-relative pe-0">
                                            <div class="row w-100 p-6 g-6">
                                                <div class="col-md-4 col-12 mb-md-0 mb-4">
                                                    <p class="h6 repeater-title">Item</p>
                                                    <select class="form-select item-details" name="item_id"><option value=""></option>@if(count($items) > 0) @foreach ($items as $item)<option value="{{ $item->id }}" data-price="{{ number_format($item->selling_unit_price ?? 0, 2, '.', '') }}">{{ $item->name ?? 'Unknown Item' }}</option>@endforeach @else<option value="" disabled>No items available</option>@endif</select>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-4">
                                                    <p class="h6 repeater-title">Selling Unit Price</p><input type="number" class="form-control selling-unit-price" name="unit_price" step="0.01" min="0">
                                                    <div class="text-heading mt-2"><div class="mb-1"><small class="text-muted">Discount:</small><span class="discount me-2">0%</span><small class="text-muted">Amt:</small><span class="item-discount-amount text-success fw-medium">$0.00</span></div><div class="mb-1"><small class="text-muted">Tax:</small><span class="tax-1 me-2">0%</span><input type="hidden" class="item-tax-id" name="tax_id" value="0"><small class="text-muted">Amt:</small><span class="item-tax-amount text-primary fw-medium">$0.00</span></div></div>
                                                </div>
                                                <div class="col-md-2 col-12 mb-md-0 mb-4">
                                                    <p class="h6 repeater-title">Qty</p><input type="number" class="form-control quantity" name="quantity" value="1" min="1" step="1">
                                                </div>
                                                <div class="col-md-3 col-12 pe-0">
                                                    <p class="h6 repeater-title">Price</p><input type="number" class="form-control total-price" name="total_price" readonly>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column align-items-center justify-content-between border-start p-2"><i class="icon-base bx bx-x icon-lg cursor-pointer" data-repeater-delete></i><div class="dropdown"><i class="icon-base bx bx-cog icon-lg cursor-pointer more-options-dropdown" role="button" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false"></i><div class="dropdown-menu dropdown-menu-end w-px-300 p-4"><div class="row g-3"><div class="col-12"><label class="form-label">Discount (%)</label><input type="number" class="form-control item-discount-input" min="0" max="100" step="0.01" /><small class="text-muted">0-100%</small></div><div class="col-12"><label class="form-label">Tax</label><select name="tax-1-input" class="form-select item-tax-select"><option value="0" data-percentage="0" selected>0%</option>@if(count($taxes)>0) @foreach ($taxes as $tax)<option value="{{ $tax->id }}" data-percentage="{{ $tax->percentage }}">{{ $tax->name }} ({{ $tax->percentage }}%)</option>@endforeach @endif</select></div></div><div class="dropdown-divider my-4"></div><button type="button" class="btn btn-label-primary btn-apply-changes">Apply</button></div></div></div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-sm btn-primary" data-repeater-create><i class="icon-base bx bx-plus icon-xs me-1_5"></i>Add Item</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- Calculations Section --}}
                <hr class="my-0" />
                <div class="card-body px-0">
                    <div class="row row-gap-4">
                        <div class="col-md-6 mb-md-0 mb-4"></div>
                        <div class="col-md-6 d-flex justify-content-end">
                            <div class="invoice-calculations">
                                <div class="d-flex justify-content-between mb-2"><span class="w-px-100 text-nowrap">Subtotal:</span><span class="fw-medium text-heading" id="subtotal-display">$0.00</span></div><hr class="my-2" />
                                <div class="d-flex justify-content-between mb-2"><span class="w-px-100 text-danger text-nowrap">Item Level Discount:</span><span class="fw-medium text-heading text-danger" id="item-level-discount-total-display">-$0.00</span></div>
                                <div class="d-flex justify-content-between mb-2"><span class="w-px-100 text-primary text-nowrap">Item Level Tax:</span><span class="fw-medium text-heading text-primary" id="item-level-tax-total-display">+$0.00</span></div><hr class="my-2" />
                                <div class="d-flex justify-content-between mb-2"><span class="w-px-100 fw-medium text-nowrap">Document Net Amount:</span><span class="fw-medium text-heading" id="subtotal-after-item-adjustments-display">$0.00</span></div>
                                <div class="d-flex justify-content-between align-items-center mb-2"><span class="text-nowrap me-2">Document Discount:</span><div class="d-flex align-items-center"><input type="number" id="discount" class="form-control form-control-sm" value="0" style="width: 80px;" min="0"><select class="form-select form-select-sm ms-2" style="width: 70px;" id="discount-type"><option value="%" selected>%</option><option value="Amount">$</option></select><span class="fw-medium text-heading ms-2" id="discount-display" style="width: 60px;">-$0.00</span></div></div>
                                <div class="d-flex justify-content-between mb-2"><span class="w-px-100 text-nowrap">Document Taxable Amount:</span><span class="fw-medium text-heading" id="taxable-amount-display">$0.00</span></div>
                                <div class="d-flex justify-content-between align-items-center mb-2"><span class="text-nowrap me-2">Document Tax:</span><div class="d-flex align-items-center"><select id="tax" class="form-select form-control-sm" style="width: 120px;"><option value="0" data-rate="0" selected>No tax (0%)</option>@foreach ($taxes as $tax)<option value="{{ $tax->id }}" data-rate="{{ $tax->percentage }}">{{ $tax->name }} ({{ $tax->percentage }}%)</option>@endforeach</select><span class="fw-medium text-heading ms-2" id="tax-amount-display" style="width: 60px;">+$0.00</span></div></div><hr class="my-2" />
                                <div class="d-flex justify-content-between"><span class="w-px-100 fw-bold text-nowrap">Total:</span><span class="fw-bold text-heading" id="grand-total">$0.00</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
            </div>
        </div>
        <!-- /Invoice Edit-->

        <!-- Invoice Actions -->
        <div class="col-lg-3 col-12 invoice-actions">
            <div class="card mb-6">
                <div class="card-body">
                    <button class="btn btn-primary d-grid w-100 mb-4" data-bs-toggle="offcanvas" data-bs-target="#sendInvoiceOffcanvas"><span class="d-flex align-items-center justify-content-center text-nowrap"><i class="icon-base bx bx-paper-plane icon-xs me-2"></i>Send Invoice</span></button>
                    <button type="button" class="btn btn-label-secondary d-grid w-100 mb-4" onclick="previewInvoice()">Preview</button>
                    <button type="button" id="saveInvoiceBtn" class="btn btn-label-secondary d-grid w-100" onclick="saveInvoice()">Update</button>
                </div>
            </div>
            <div>
                {{-- *** CHANGE: ADD DROPDOWN WITH PRE-SELECTED VALUE *** --}}
                <label for="acceptPaymentsVia" class="form-label">Accept payments via</label>
                <select class="form-select mb-6" id="acceptPaymentsVia" name="payment_method_id">
                    @if(isset($personalizedPaymentOptions) && $personalizedPaymentOptions->isNotEmpty())
                        @foreach($personalizedPaymentOptions as $option)
                            <option value="{{ $option->id }}" {{ $invoice->payment_method_id == $option->id ? 'selected' : '' }}>
                                {{ $option->name }}
                            </option>
                        @endforeach
                    @else
                        <option value="" disabled>No payment options configured</option>
                    @endif
                </select>
                {{-- *** (You can add your other checkboxes for Payment Terms etc. back here if needed) *** --}}
            </div>
        </div>
        <!-- /Invoice Actions -->
    </div>
    @include('_partials/_offcanvas/offcanvas-send-invoice')
@endsection