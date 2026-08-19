@extends('layouts/layoutMaster')

@section('title', __('Workflow Automation') . ' — AK-Mart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-cog text-primary me-2"></i> {{ __('Workflow Automation Engine') }}</h4>
        <p class="text-muted small mb-0">{{ __('Create event-driven automated rules (Triggers → Conditions → Actions) for store operations') }}</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRuleModal">
        <i class="bx bx-plus me-1"></i> {{ __('New Workflow Rule') }}
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">{{ __('Active Automation Rules') }} ({{ $rules->count() }})</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Rule Name') }}</th>
                    <th>{{ __('Trigger Event') }}</th>
                    <th>{{ __('Conditions') }}</th>
                    <th>{{ __('Actions') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                    <tr>
                        <td><strong class="text-heading">{{ $rule->name }}</strong></td>
                        <td>
                            <span class="badge bg-label-info">
                                <i class="bx bx-bolt-circle me-1"></i> {{ ucfirst(str_replace('_', ' ', $rule->trigger_event)) }}
                            </span>
                        </td>
                        <td>
                            @if($rule->conditions)
                                <code>{{ $rule->conditions['field'] ?? 'param' }} {{ $rule->conditions['operator'] ?? '==' }} {{ $rule->conditions['value'] ?? '' }}</code>
                            @else
                                <span class="text-muted small">{{ __('Always Run') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-success">
                                {{ ucfirst($rule->actions['type'] ?? __('Notification')) }}
                            </span>
                            <small class="text-muted d-block">{{ \Illuminate\Support\Str::limit($rule->actions['message'] ?? '', 30) }}</small>
                        </td>
                        <td>
                            <form action="{{ route('app-automation-toggle', $rule->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="badge border-0 {{ $rule->is_active ? 'bg-success' : 'bg-secondary' }}" style="cursor: pointer;">
                                    {{ $rule->is_active ? __('Active') : __('Paused') }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('app-automation-destroy', $rule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this rule?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="{{ __('Delete') }}">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bx bx-cog fs-1 d-block mb-2 opacity-50"></i>
                            {{ __('No automation rules configured. Click "New Workflow Rule" to create one.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: New Rule -->
<div class="modal fade" id="createRuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('app-automation-store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">{{ __('Create Automation Rule') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Rule Name') }} *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., High-Value Order Notification" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('When this event happens (Trigger)') }} *</label>
                        <select name="trigger_event" class="form-select" required>
                            <option value="order_created">{{ __('Order Placed') }} (order_created)</option>
                            <option value="order_paid">{{ __('Order Paid') }} (order_paid)</option>
                            <option value="stock_low">{{ __('Product Low Stock') }} (stock_low)</option>
                            <option value="customer_vip">{{ __('Customer Reaches VIP Tier') }} (customer_vip)</option>
                            <option value="purchase_received">{{ __('Purchase Order Received') }} (purchase_received)</option>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label fw-semibold">{{ __('Field') }}</label>
                            <input type="text" name="condition_field" class="form-control form-control-sm" value="total_amount">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">{{ __('Operator') }}</label>
                            <select name="condition_operator" class="form-select form-select-sm">
                                <option value=">=">&gt;=</option>
                                <option value="<=">&lt;=</option>
                                <option value=">">&gt;</option>
                                <option value="<">&lt;</option>
                                <option value="==">==</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">{{ __('Value') }}</label>
                            <input type="text" name="condition_value" class="form-control form-control-sm" value="200">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Take this Action') }} *</label>
                        <select name="action_type" class="form-select" required>
                            <option value="notification">{{ __('Send In-App Notification') }}</option>
                            <option value="create_stock_alert">{{ __('Create High-Priority Stock Alert') }}</option>
                            <option value="tag_vip">{{ __('Tag Customer as VIP') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Action Message / Body') }}</label>
                        <input type="text" name="action_message" class="form-control" value="High priority event triggered automatically by AK-Mart Engine.">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create Rule') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
