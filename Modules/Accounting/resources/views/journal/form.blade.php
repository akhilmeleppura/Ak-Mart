@extends('accounting::components.layouts.master')

@section('title', isset($journalEntry) ? 'Edit Journal Entry' : 'Create Journal Entry')

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>{{ isset($journalEntry) ? 'Edit' : 'Create' }} Journal Entry</h5>
                <div>
                    <strong>Total Debit:</strong>
                    <input type="text" id="total_debit" class="form-control d-inline w-auto" value="0.00" readonly>
                    <strong>Total Credit:</strong>
                    <input type="text" id="total_credit" class="form-control d-inline w-auto" value="0.00" readonly>
                </div>
            </div>

            <div class="card-body">
                <form
                    action="{{ isset($journalEntry) ? route('accounting.journal.update', $journalEntry->id) : route('accounting.journal.store') }}"
                    method="POST" id="journal-form">
                    @csrf
                    @if (isset($journalEntry))
                        @method('PUT')
                    @endif

                    <div class="mb-3 w-25">
                        <label>Transaction Date</label>
                        <input type="date" name="transaction_date" class="form-control form-control-sm" required
                            value="{{ old('transaction_date', $today ?? ($journalEntry->transaction_date ?? now()->format('Y-m-d'))) }}">
                    </div>

                    <div class="row fw-bold text-center mb-2">
                        <div class="col-md-3">Account Name</div>
                        <div class="col-md-2">Debit</div>
                        <div class="col-md-2">Credit</div>
                        <div class="col-md-3">Description</div>
                    </div>

                    <div id="journal-entries">
                        @php
                            $entries = old('entries', $journalEntry->entries ?? [['ledger_account_id' => '', 'debit_amount' => '', 'credit_amount' => '', 'description' => '']]);
                        @endphp

                        @foreach ($entries as $index => $entry)
                            <div class="row journal-entry align-items-center mb-2">
                                <div class="col-md-3">
                                    {{-- UPDATED: Added 'account-select' class for JS targeting --}}
                                    <select name="entries[{{ $index }}][ledger_account_id]" class="form-control form-control-sm account-select" required>
                                        <option value="">Select Account</option>
                                        {{-- UPDATED: Loop through grouped options from the controller --}}
                                        @foreach ($accountOptions as $group => $options)
                                            <optgroup label="{{ $group }}">
                                                @foreach ($options as $option)
                                                    <option value="{{ $option->id }}"
                                                        @if ((string) old("entries.$index.ledger_account_id", $entry['ledger_account_id'] ?? ($entry->chart_of_account_id ?? '')) === (string) $option->id) selected @endif>
                                                        {{-- Handles both account_name and name properties --}}
                                                        {{ $option->account_name ?? $option->name }}
                                                        @if(isset($option->account_type)) [{{ $option->account_type }}] @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.01" name="entries[{{ $index }}][debit_amount]" class="form-control form-control-sm debit-amount" placeholder="0.00"
                                        value="{{ old("entries.$index.debit_amount", $entry['debit_amount'] ?? ($entry->debit_amount ?? '')) }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.01" name="entries[{{ $index }}][credit_amount]" class="form-control form-control-sm credit-amount" placeholder="0.00"
                                        value="{{ old("entries.$index.credit_amount", $entry['credit_amount'] ?? ($entry->credit_amount ?? '')) }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="entries[{{ $index }}][description]" class="form-control form-control-sm" placeholder="Description"
                                        value="{{ old("entries.$index.description", $entry['description'] ?? ($entry->description ?? '')) }}">
                                </div>
                                <div class="col-md-1 text-center">
                                    @if ($loop->index > 0)
                                        <button type="button" class="btn btn-sm btn-danger remove-entry">X</button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-entry" class="btn btn-success btn-sm mt-2">Add More</button>
                    <hr>

                    <div class="row align-items-center mt-3">
                        <div class="col-md-6">
                            <label for="summary">Summary</label>
                            <input type="text" name="summary" id="summary" class="form-control" placeholder="General remarks..." value="{{ old('summary', $journalEntry->summary ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="total-count">Total Count</label>
                            <input type="text" id="total-count" class="form-control" value="{{ count($entries) }}" readonly>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">{{ isset($journalEntry) ? 'Update' : 'Save' }}</button>
                        <a href="{{ route('accounting.journal.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Template -->
    <div id="entry-template" style="display: none;">
        <div class="row journal-entry align-items-center mb-2">
            <div class="col-md-3">
                 {{-- UPDATED: Template now uses a class and has the new optgroup structure --}}
                <select name="__name__" class="form-control form-control-sm account-select-template" required>
                    <option value="">Select Account</option>
                    @foreach ($accountOptions as $group => $options)
                        <optgroup label="{{ $group }}">
                            @foreach ($options as $option)
                                <option value="{{ $option->id }}">
                                    {{ $option->account_name ?? $option->name }}
                                    @if(isset($option->account_type)) [{{ $option->account_type }}] @endif
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="__debit_name__" class="form-control form-control-sm debit-amount" placeholder="0.00">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="__credit_name__" class="form-control form-control-sm credit-amount" placeholder="0.00">
            </div>
            <div class="col-md-3">
                <input type="text" name="__desc_name__" class="form-control form-control-sm" placeholder="Description">
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-sm btn-danger remove-entry">X</button>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    {{-- Add these if they are not in your master layout --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Function to initialize Select2
            function initializeSelect2(element) {
                $(element).select2({
                    placeholder: 'Select an Account',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Initialize Select2 on all existing account dropdowns
            $('.account-select').each(function() {
                initializeSelect2(this);
            });

            function calculateTotals() {
                let debitTotal = 0, creditTotal = 0;
                const visibleEntries = $('#journal-entries .journal-entry');

                visibleEntries.find('.debit-amount').each(function() {
                    debitTotal += parseFloat($(this).val()) || 0;
                });
                visibleEntries.find('.credit-amount').each(function() {
                    creditTotal += parseFloat($(this).val()) || 0;
                });

                $('#total_debit').val(debitTotal.toFixed(2));
                $('#total_credit').val(creditTotal.toFixed(2));
                $('#total-count').val(visibleEntries.length);
            }

            calculateTotals();

            // Lock opposite field (Debit/Credit)
            $('#journal-entries').on('input', '.debit-amount, .credit-amount', function() {
                const row = $(this).closest('.journal-entry');
                const debitInput = row.find('.debit-amount');
                const creditInput = row.find('.credit-amount');

                if ($(this).hasClass('debit-amount')) {
                    if (parseFloat(debitInput.val()) > 0) {
                        creditInput.val('').prop('disabled', true);
                    } else {
                        creditInput.prop('disabled', false);
                    }
                } else if ($(this).hasClass('credit-amount')) {
                    if (parseFloat(creditInput.val()) > 0) {
                        debitInput.val('').prop('disabled', true);
                    } else {
                        debitInput.prop('disabled', false);
                    }
                }
                calculateTotals();
            });
             
            // Pre-disable fields on page load for existing entries
             $('.journal-entry').each(function() {
                const debitInput = $(this).find('.debit-amount');
                const creditInput = $(this).find('.credit-amount');
                 if (parseFloat(debitInput.val()) > 0) creditInput.prop('disabled', true);
                 if (parseFloat(creditInput.val()) > 0) debitInput.prop('disabled', true);
            });


            $('#add-entry').on('click', function() {
                const index = $('#journal-entries .journal-entry').length;
                let template = $('#entry-template').html();

                // Replace placeholders
                template = template.replace(/__name__/g, `entries[${index}][ledger_account_id]`)
                                   .replace(/__debit_name__/g, `entries[${index}][debit_amount]`)
                                   .replace(/__credit_name__/g, `entries[${index}][credit_amount]`)
                                   .replace(/__desc_name__/g, `entries[${index}][description]`)
                                   .replace(/account-select-template/g, 'account-select');

                const newRow = $(template);

                // Append the new row to the container
                $('#journal-entries').append(newRow);

                // Find the new select dropdown within the new row and initialize Select2 on it
                newRow.find('.account-select').each(function() {
                    initializeSelect2(this);
                });

                calculateTotals();
            });

            $('#journal-entries').on('click', '.remove-entry', function() {
                if ($('#journal-entries .journal-entry').length > 1) {
                    $(this).closest('.journal-entry').remove();
                    calculateTotals();
                } else {
                     // Using a library like SweetAlert2 for modals is a good idea.
                    alert('At least one entry must be present.');
                }
            });

            $('#journal-form').on('submit', function(e) {
                const debit = parseFloat($('#total_debit').val()) || 0;
                const credit = parseFloat($('#total_credit').val()) || 0;

                // Use a small tolerance for floating point comparisons
                if (Math.abs(debit - credit) > 0.001) {
                    e.preventDefault();
                    // Replace with your preferred notification library (e.g., SweetAlert2)
                    alert('Total Debit and Credit amounts must be equal to proceed.');
                }
            });
        });
    </script>
@endsection