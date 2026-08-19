@extends('layouts/layoutMaster')

@section('title', __('Email Templates Management') . ' — AK-Mart')

@section('content')
<div class="row g-6">
  <div class="col-12 col-lg-4 col-xl-3">
    @include('content.apps._settings-sidebar')
  </div>

  <div class="col-12 col-lg-8 col-xl-9">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card shadow-sm border-0 mb-6">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h5 class="card-title mb-1 d-flex align-items-center gap-2">
            <i class="bx bx-layout text-info fs-4"></i>
            <span>{{ __('Transactional Email Templates') }}</span>
          </h5>
          <p class="text-muted small mb-0">{{ __('Customize customer transactional notifications with dynamic placeholder tags.') }}</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditTemplate" onclick="resetTemplateModal()">
          <i class="bx bx-plus me-1"></i>{{ __('New Template') }}
        </button>
      </div>

      <!-- Variable Tokens Helper -->
      <div class="card-body bg-light-subtle border-bottom py-3 px-4">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <span class="fw-bold small text-heading">{{ __('Available Dynamic Tags:') }}</span>
          <code class="cursor-pointer bg-white px-2 py-0.5 border rounded" onclick="copyTag('{{customer_name}}')">&#123;&#123;customer_name&#125;&#125;</code>
          <code class="cursor-pointer bg-white px-2 py-0.5 border rounded" onclick="copyTag('{{order_number}}')">&#123;&#123;order_number&#125;&#125;</code>
          <code class="cursor-pointer bg-white px-2 py-0.5 border rounded" onclick="copyTag('{{order_total}}')">&#123;&#123;order_total&#125;&#125;</code>
          <code class="cursor-pointer bg-white px-2 py-0.5 border rounded" onclick="copyTag('{{tracking_number}}')">&#123;&#123;tracking_number&#125;&#125;</code>
          <code class="cursor-pointer bg-white px-2 py-0.5 border rounded" onclick="copyTag('{{store_name}}')">&#123;&#123;store_name&#125;&#125;</code>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>{{ __('Template Name') }}</th>
              <th>{{ __('Code') }}</th>
              <th>{{ __('Subject') }}</th>
              <th>{{ __('Status') }}</th>
              <th class="text-center">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @php
              $defaultTemplates = [
                ['code' => 'welcome_email', 'name' => 'Customer Welcome Email', 'subject' => 'Welcome to {{store_name}}!', 'category' => 'transactional', 'body' => "Hi {{customer_name}},\n\nWelcome to {{store_name}}! Your account has been successfully created."],
                ['code' => 'order_confirmation', 'name' => 'Order Placed Confirmation', 'subject' => 'Order Confirmed: #{{order_number}}', 'category' => 'transactional', 'body' => "Hi {{customer_name}},\n\nThank you for your order #{{order_number}} of {{order_total}}. We are now preparing your items."],
                ['code' => 'order_shipped', 'name' => 'Order Dispatched & Tracking', 'subject' => 'Your Order #{{order_number}} Has Shipped!', 'category' => 'transactional', 'body' => "Hi {{customer_name}},\n\nYour order #{{order_number}} is on the way! Tracking number: {{tracking_number}}."],
                ['code' => 'order_payment_reminder', 'name' => 'Pending Payment Reminder', 'subject' => 'Action Required: Complete Payment for Order #{{order_number}}', 'category' => 'reminder', 'body' => "Hi {{customer_name}},\n\nYour order #{{order_number}} for {{order_total}} is waiting for payment. Complete your checkout today."],
                ['code' => 'low_stock_alert', 'name' => 'Admin Low Stock Warning', 'subject' => 'Low Stock Warning: {{product_name}}', 'category' => 'alert', 'body' => "Warning: Product {{product_name}} stock is running low. Current stock is below safety threshold."],
              ];
            @endphp
            @foreach($defaultTemplates as $dt)
              @php
                $dbTpl = collect($templates)->firstWhere('code', $dt['code']);
                $name = $dbTpl ? $dbTpl->name : $dt['name'];
                $subject = $dbTpl ? $dbTpl->subject : $dt['subject'];
                $body = $dbTpl ? $dbTpl->body : $dt['body'];
                $isActive = $dbTpl ? $dbTpl->is_active : true;
              @endphp
              <tr>
                <td>
                  <span class="fw-semibold text-heading">{{ $name }}</span>
                </td>
                <td>
                  <span class="badge bg-label-primary font-monospace">{{ $dt['code'] }}</span>
                </td>
                <td>
                  <small class="text-muted">{{ $subject }}</small>
                </td>
                <td>
                  <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                    {{ $isActive ? __('Active') : __('Disabled') }}
                  </span>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-label-primary" onclick="openTemplateEditor('{{ $dt['code'] }}', '{{ addslashes($name) }}', '{{ addslashes($subject) }}', `{{ addslashes($body) }}`, 'email')">
                    <i class="bx bx-edit me-1"></i>{{ __('Edit Template') }}
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Template Editor -->
<div class="modal fade" id="modalEditTemplate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-bottom">
        <h5 class="modal-title d-flex align-items-center gap-2" id="modalTemplateTitle">
          <i class="bx bx-edit text-primary fs-4"></i>
          <span>{{ __('Edit Email Template') }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('settings.templates.save') }}">
        @csrf
        <input type="hidden" name="channel" value="email" />
        <div class="modal-body p-4">
          <div class="row g-4 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Template Code') }} <span class="text-danger">*</span></label>
              <input type="text" name="code" id="tpl_code" class="form-control" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ __('Template Display Name') }} <span class="text-danger">*</span></label>
              <input type="text" name="name" id="tpl_name" class="form-control" required />
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Email Subject Line') }} <span class="text-danger">*</span></label>
              <input type="text" name="subject" id="tpl_subject" class="form-control" required />
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">{{ __('Email Body Content / HTML') }} <span class="text-danger">*</span></label>
              <textarea name="body" id="tpl_body" class="form-control" rows="8" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-check me-1"></i>{{ __('Save Template') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function copyTag(tag) {
    navigator.clipboard.writeText(tag);
  }

  function openTemplateEditor(code, name, subject, body, channel) {
    document.getElementById('tpl_code').value = code;
    document.getElementById('tpl_name').value = name;
    document.getElementById('tpl_subject').value = subject;
    document.getElementById('tpl_body').value = body;
    new bootstrap.Modal(document.getElementById('modalEditTemplate')).show();
  }

  function resetTemplateModal() {
    document.getElementById('tpl_code').value = '';
    document.getElementById('tpl_name').value = '';
    document.getElementById('tpl_subject').value = '';
    document.getElementById('tpl_body').value = '';
  }
</script>
@endsection
