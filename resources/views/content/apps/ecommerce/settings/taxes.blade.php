@extends('layouts/layoutMaster')

@section('title', __('Tax & VAT Rules Management') . ' — AK-Mart')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Header & Breadcrumbs -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bx bx-receipt text-primary fs-3"></i>
                {{ __('Tax & VAT Rules Management') }}
            </h4>
            <p class="text-muted mb-0 small">{{ __('Configure global VAT rates, individual product tax tiers, and area-based geo-zone tax matrix.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-3.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#addTaxRuleModal">
                <i class="bx bx-plus-circle me-1"></i> {{ __('Add Tax Rule') }}
            </button>
            <a href="{{ route('storefront.cart') }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bx bx-store-alt me-1"></i> {{ __('View Storefront Cart') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-xs mb-4" role="alert">
            <i class="bx bx-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. KPI Metric Highlights -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3.5 bg-primary bg-opacity-10 text-primary">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small text-uppercase letter-spacing-1">{{ __('Active Rules') }}</span>
                    <i class="bx bx-list-check fs-4"></i>
                </div>
                <h3 class="fw-bolder mb-0 text-primary">{{ $activeRulesCount }} <small class="fs-6 text-muted">/ {{ $totalRules }}</small></h3>
                <small class="text-muted mt-1 d-block">{{ __('Area & class tax rules') }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3.5 bg-success bg-opacity-10 text-success">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small text-uppercase letter-spacing-1">{{ __('Default VAT Rate') }}</span>
                    <i class="bx bx-percentage fs-4"></i>
                </div>
                <h3 class="fw-bolder mb-0 text-success">{{ number_format($settings['vat_default_rate'], 1) }}%</h3>
                <small class="text-muted mt-1 d-block">{{ ucfirst($settings['tax_calculation_mode']) }} {{ __('Mode') }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3.5 bg-warning bg-opacity-10 text-warning">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small text-uppercase letter-spacing-1">{{ __('B2B Exemption') }}</span>
                    <i class="bx bx-building fs-4"></i>
                </div>
                <h3 class="fw-bolder mb-0 text-warning">{{ $settings['b2b_vat_exemption'] ? __('Active') : __('Disabled') }}</h3>
                <small class="text-muted mt-1 d-block">{{ __('Verified VAT numbers') }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3.5 bg-info bg-opacity-10 text-info">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small text-uppercase letter-spacing-1">{{ __('Tax Engine') }}</span>
                    <i class="bx bx-cog fs-4"></i>
                </div>
                <h3 class="fw-bolder mb-0 text-info">{{ $settings['tax_enabled'] ? __('Enabled') : __('Off') }}</h3>
                <small class="text-muted mt-1 d-block">{{ __('Checkout auto-calc') }}</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- 2. Left Column: Area-Based & Geo-Zone Tax Rules Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ __('Area-Based & Geo-Zone Tax Rules') }}</h5>
                        <small class="text-muted">{{ __('Calculates automatic sales tax / VAT based on customer shipping address') }}</small>
                    </div>
                    <span class="badge rounded-pill bg-light text-muted border px-2.5 py-1">{{ $taxRules->count() }} {{ __('rules configured') }}</span>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small fw-bold text-uppercase text-muted">
                                <th>{{ __('Rule Name') }}</th>
                                <th>{{ __('Area / Zone') }}</th>
                                <th>{{ __('Tax Class') }}</th>
                                <th>{{ __('Rate %') }}</th>
                                <th>{{ __('Priority') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($taxRules as $rule)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $rule->name }}</div>
                                        <small class="text-muted font-monospace">{{ $rule->calculation_mode === 'inclusive' ? __('(Price Inclusive)') : __('(Added at checkout)') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border px-2 py-0.5 fw-bold font-monospace">
                                                {{ $rule->country_code === '*' ? __('ALL COUNTRIES') : $rule->country_code }}
                                            </span>
                                            @if($rule->state_name && $rule->state_name !== '*')
                                                <span class="badge rounded-pill bg-light text-dark border px-2 py-0.5 font-monospace">
                                                    {{ $rule->state_name }}
                                                </span>
                                            @endif
                                            @if($rule->postal_code_pattern && $rule->postal_code_pattern !== '*')
                                                <span class="badge rounded-pill bg-warning bg-opacity-20 text-dark border px-2 py-0.5 small font-monospace">
                                                    {{ $rule->postal_code_pattern }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-label-info text-capitalize px-2.5 py-1 fw-semibold">
                                            {{ str_replace('_', ' ', $rule->tax_class) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bolder fs-6 {{ $rule->rate == 0 ? 'text-success' : 'text-primary' }}">
                                            {{ number_format($rule->rate, 2) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-muted border rounded-pill">{{ $rule->priority }}</span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" onchange="toggleTaxRuleActive({{ $rule->id }})" {{ $rule->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle shadow-xs" onclick='openEditTaxRuleModal(@json($rule))' title="{{ __('Edit Rule') }}">
                                                <i class="bx bx-edit-alt text-primary"></i>
                                            </button>
                                            <form action="{{ route('app-ecommerce-settings-taxes-delete', $rule->id) }}" method="POST" onsubmit="return confirm('Delete tax rule {{ $rule->name }}?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-light rounded-circle shadow-xs" title="{{ __('Delete Rule') }}">
                                                    <i class="bx bx-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        {{ __('No tax rules created yet. Click "Add Tax Rule" to create your first area rate.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Product Tax Classes Reference -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">{{ __('Individual Product Tax Classes') }}</h5>
                    <span class="small text-muted">{{ __('Configurable per product in Catalog Management') }}</span>
                </div>
                <div class="row g-3">
                    @foreach($taxClasses as $key => $cls)
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light bg-opacity-40">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark font-monospace">{{ $cls['name'] }}</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary font-monospace">{{ $key }}</span>
                                </div>
                                <small class="text-muted d-block">{{ $cls['description'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 4. Right Column: Global Settings & Live Tax Simulator -->
        <div class="col-lg-4">
            <!-- Global VAT / Tax Configuration Form -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-slider-alt text-primary"></i> {{ __('Global VAT / Tax Engine') }}
                </h5>

                <form action="{{ route('app-ecommerce-settings-taxes-save') }}" method="POST">
                    @csrf

                    <!-- Enable Tax Engine -->
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div>
                            <h6 class="fw-bold mb-0">{{ __('Enable Tax System') }}</h6>
                            <small class="text-muted">{{ __('Calculate taxes at checkout') }}</small>
                        </div>
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input" type="checkbox" name="tax_enabled" value="1" {{ $settings['tax_enabled'] ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- Default VAT Rate % -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">{{ __('Default Fallback VAT / Tax Rate (%)') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="vat_default_rate" class="form-control fw-bold" value="{{ $settings['vat_default_rate'] }}" min="0" max="100" required>
                            <span class="input-group-text bg-light fw-bold">%</span>
                        </div>
                        <small class="text-muted">{{ __('Applied when no specific area rule matches.') }}</small>
                    </div>

                    <!-- Calculation Mode -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">{{ __('Price Calculation Mode') }}</label>
                        <select name="tax_calculation_mode" class="form-select">
                            <option value="exclusive" {{ $settings['tax_calculation_mode'] === 'exclusive' ? 'selected' : '' }}>{{ __('Exclusive (Add tax on top of prices at checkout)') }}</option>
                            <option value="inclusive" {{ $settings['tax_calculation_mode'] === 'inclusive' ? 'selected' : '' }}>{{ __('Inclusive (Prices entered already include VAT)') }}</option>
                        </select>
                    </div>

                    <!-- Calculation Basis -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">{{ __('Tax Calculation Basis') }}</label>
                        <select name="tax_calculation_basis" class="form-select">
                            <option value="shipping_address" {{ $settings['tax_calculation_basis'] === 'shipping_address' ? 'selected' : '' }}>{{ __('Customer Shipping / Destination Address') }}</option>
                            <option value="store_address" {{ $settings['tax_calculation_basis'] === 'store_address' ? 'selected' : '' }}>{{ __('Store / Branch Origin Location') }}</option>
                        </select>
                    </div>

                    <!-- Store Tax Registration Number -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">{{ __('Store VAT / Tax Registration Number') }}</label>
                        <input type="text" name="tax_number" class="form-control font-monospace" value="{{ $settings['tax_number'] }}" placeholder="e.g. VAT-123456789">
                    </div>

                    <!-- B2B Exemption Switch -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="b2b_vat_exemption" value="1" id="b2bExemptCheck" {{ $settings['b2b_vat_exemption'] ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small" for="b2bExemptCheck">
                            {{ __('Enable B2B Zero-Tax on Verified Tax IDs') }}
                        </label>
                    </div>

                    <!-- Invoice Display Switch -->
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="tax_display_on_invoice" value="1" id="invoiceTaxCheck" {{ $settings['tax_display_on_invoice'] ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small" for="invoiceTaxCheck">
                            {{ __('Show Detailed Itemized Tax on Invoices') }}
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-xs">
                        <i class="bx bx-save me-1"></i> {{ __('Save VAT Settings') }}
                    </button>
                </form>
            </div>

            <!-- Live Tax Calculation Simulator -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-calculator text-success"></i> {{ __('Tax Rule Simulator') }}
                </h5>
                <p class="small text-muted mb-3">{{ __('Test how your tax rules calculate for any customer destination in real time.') }}</p>

                <div class="mb-2.5">
                    <label class="form-label small fw-semibold text-muted">{{ __('Cart Amount ($)') }}</label>
                    <input type="number" id="simAmount" class="form-control form-control-sm" value="100.00" step="1">
                </div>
                <div class="row g-2 mb-2.5">
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted">{{ __('Country') }}</label>
                        <select id="simCountry" class="form-select form-select-sm">
                            <option value="US">US (United States)</option>
                            <option value="AE">AE (United Arab Emirates)</option>
                            <option value="GB">GB (United Kingdom)</option>
                            <option value="IN">IN (India)</option>
                            <option value="*">Other / Global (*)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted">{{ __('State / Zone') }}</label>
                        <input type="text" id="simState" class="form-control form-control-sm" value="CA" placeholder="e.g. CA, NY">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">{{ __('Postal / Zip Code') }}</label>
                    <input type="text" id="simZip" class="form-control form-control-sm" value="90210" placeholder="e.g. 90210">
                </div>

                <button type="button" class="btn btn-outline-success w-100 rounded-pill btn-sm fw-bold mb-3" onclick="runTaxSimulation()">
                    <i class="bx bx-play-circle me-1"></i> {{ __('Calculate Tax') }}
                </button>

                <div id="simResultBox" class="p-3 bg-light rounded-3 d-none">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">{{ __('Subtotal:') }}</span>
                        <strong id="simSubtotal">$100.00</strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-1 text-danger">
                        <span id="simRuleLabel">{{ __('Calculated Tax:') }}</span>
                        <strong id="simTaxAmount">$8.25</strong>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-1 mt-1 fw-bold text-dark">
                        <span>{{ __('Final Estimated Total:') }}</span>
                        <span class="text-primary" id="simFinalTotal">$108.25</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add / Edit Tax Rule -->
<div class="modal fade" id="addTaxRuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="taxRuleModalTitle">{{ __('Create New Tax & VAT Rule') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="taxRuleForm" action="{{ route('app-ecommerce-settings-taxes-store') }}" method="POST">
                @csrf
                <div id="methodOverride"></div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">{{ __('Rule Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="ruleName" class="form-control" placeholder="e.g. California State Tax, UK Standard VAT" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">{{ __('Tax Class') }} <span class="text-danger">*</span></label>
                            <select name="tax_class" id="ruleTaxClass" class="form-select" required>
                                <option value="standard">{{ __('Standard Rate') }}</option>
                                <option value="reduced">{{ __('Reduced Rate (Groceries)') }}</option>
                                <option value="zero_rate">{{ __('Zero Rate (0%)') }}</option>
                                <option value="luxury">{{ __('Luxury / Specialty') }}</option>
                                <option value="exempt">{{ __('Exempt') }}</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">{{ __('Tax Rate (%)') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.001" name="rate" id="ruleRate" class="form-control fw-bold" placeholder="5.0" required>
                                <span class="input-group-text bg-light fw-bold">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">{{ __('Country Code') }}</label>
                            <input type="text" name="country_code" id="ruleCountry" class="form-control font-monospace" placeholder="e.g. US, AE, IN or *" value="*">
                            <small class="text-muted">Use * for global</small>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">{{ __('State / Province') }}</label>
                            <input type="text" name="state_name" id="ruleState" class="form-control" placeholder="e.g. CA, NY, Dubai or *" value="*">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">{{ __('Postal Code Prefix') }}</label>
                            <input type="text" name="postal_code_pattern" id="ruleZip" class="form-control font-monospace" placeholder="e.g. 900*, 90210, *">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">{{ __('Priority (1-99)') }}</label>
                            <input type="number" name="priority" id="rulePriority" class="form-control" value="1" min="1" max="99">
                        </div>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_compound" value="1" id="ruleCompound">
                        <label class="form-check-label small fw-semibold" for="ruleCompound">{{ __('Compound Tax (Tax applied on top of other taxes)') }}</label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="ruleActive" checked>
                        <label class="form-check-label small fw-semibold" for="ruleActive">{{ __('Active Rule') }}</label>
                    </div>
                </div>

                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnSaveRule">{{ __('Save Rule') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleTaxRuleActive(id) {
    fetch(`/ecommerce/settings/taxes/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            console.log(data.message);
        }
    });
}

function openEditTaxRuleModal(rule) {
    document.getElementById('taxRuleModalTitle').textContent = '{{ __("Edit Tax Rule") }}';
    document.getElementById('taxRuleForm').action = `/ecommerce/settings/taxes/${rule.id}`;
    document.getElementById('methodOverride').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    
    document.getElementById('ruleName').value = rule.name;
    document.getElementById('ruleTaxClass').value = rule.tax_class;
    document.getElementById('ruleRate').value = rule.rate;
    document.getElementById('ruleCountry').value = rule.country_code || '*';
    document.getElementById('ruleState').value = rule.state_name || '*';
    document.getElementById('ruleZip').value = rule.postal_code_pattern || '';
    document.getElementById('rulePriority').value = rule.priority || 1;
    document.getElementById('ruleCompound').checked = Boolean(rule.is_compound);
    document.getElementById('ruleActive').checked = Boolean(rule.is_active);

    const modal = new bootstrap.Modal(document.getElementById('addTaxRuleModal'));
    modal.show();
}

function runTaxSimulation() {
    const amount = document.getElementById('simAmount').value;
    const country = document.getElementById('simCountry').value;
    const state = document.getElementById('simState').value;
    const zip = document.getElementById('simZip').value;

    fetch('{{ route("app-ecommerce-settings-taxes-simulate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ amount, country, state, zip })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('simSubtotal').textContent = '$' + parseFloat(data.amount).toFixed(2);
            document.getElementById('simTaxAmount').textContent = '$' + parseFloat(data.result.total_tax).toFixed(2);
            document.getElementById('simFinalTotal').textContent = '$' + parseFloat(data.total).toFixed(2);

            let label = 'Calculated Tax:';
            if (data.result.tax_breakdown && data.result.tax_breakdown.length > 0) {
                label = data.result.tax_breakdown.map(b => `${b.name} (${b.rate}%)`).join(', ');
            }
            document.getElementById('simRuleLabel').textContent = label;

            document.getElementById('simResultBox').classList.remove('d-none');
        }
    });
}
</script>
@endsection
