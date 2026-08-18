@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Unified Communication Center - Email & WhatsApp')

@section('content')
<div class="row g-6 mb-6">
  <!-- Metrics Header -->
  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading fw-medium">Emails Dispatched</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ number_format($stats['total_emails'] ?? 0) }}</h4>
            </div>
            <small class="text-muted">SMTP & Transactional</small>
          </div>
          <span class="badge bg-label-primary rounded p-2">
            <i class="icon-base bx bx-envelope fs-4"></i>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading fw-medium">WhatsApp Cloud API</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2 text-success">{{ number_format($stats['total_whatsapp'] ?? 0) }}</h4>
            </div>
            <small class="text-muted">Official Meta Cloud API</small>
          </div>
          <span class="badge bg-label-success rounded p-2">
            <i class="icon-base bx bxl-whatsapp fs-4"></i>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading fw-medium">Delivered Messages</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2 text-primary">{{ number_format($stats['total_delivered'] ?? 0) }}</h4>
            </div>
            <small class="text-muted">High Delivery Rate</small>
          </div>
          <span class="badge bg-label-info rounded p-2">
            <i class="icon-base bx bx-check-double fs-4"></i>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading fw-medium">Failed Dispatches</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2 text-danger">{{ number_format($stats['total_failed'] ?? 0) }}</h4>
            </div>
            <small class="text-muted">Zero Order Rollback</small>
          </div>
          <span class="badge bg-label-danger rounded p-2">
            <i class="icon-base bx bx-error-circle fs-4"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="icon-base bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="icon-base bx bx-x-circle me-1"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<!-- Tabs Navigation -->
<div class="nav-align-top">
  <ul class="nav nav-pills mb-4 gap-2" role="tablist">
    <li class="nav-item">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-logs">
        <i class="icon-base bx bx-list-ul me-1"></i> Live Logs & Activity
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-compose">
        <i class="icon-base bx bx-paper-plane me-1"></i> Quick Dispatch
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-templates">
        <i class="icon-base bx bx-layout me-1"></i> Message Templates
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-campaigns">
        <i class="icon-base bx bx-broadcast me-1"></i> Marketing Campaigns
      </button>
    </li>
  </ul>

  <div class="tab-content p-0 bg-transparent shadow-none">
    <!-- Live Logs -->
    <div class="tab-pane fade show active" id="tab-logs">
      <div class="card shadow-sm border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
          <h5 class="card-title mb-0">Unified Communication Logs</h5>
          <span class="badge bg-label-primary">Real-Time</span>
        </div>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Channel</th>
                <th>Recipient</th>
                <th>Template / Subject</th>
                <th>Status</th>
                <th>Provider</th>
                <th>Sent At</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentLogs as $log)
                <tr>
                  <td>
                    @if($log->channel === 'whatsapp')
                      <span class="badge bg-label-success"><i class="icon-base bxl-whatsapp me-1"></i> WhatsApp</span>
                    @elseif($log->channel === 'email')
                      <span class="badge bg-label-primary"><i class="icon-base bx bx-envelope me-1"></i> Email</span>
                    @else
                      <span class="badge bg-label-info"><i class="icon-base bx bx-bell me-1"></i> In-App</span>
                    @endif
                  </td>
                  <td>
                    <strong>{{ $log->recipient_name }}</strong>
                    <small class="d-block text-muted">{{ $log->recipient }}</small>
                  </td>
                  <td>
                    <span class="fw-semibold">{{ $log->subject ?? $log->template_code }}</span>
                    <small class="d-block text-truncate text-muted" style="max-width: 280px;">{{ $log->message_body }}</small>
                  </td>
                  <td>
                    @if($log->status === 'delivered' || $log->status === 'sent')
                      <span class="badge bg-success">Sent</span>
                    @elseif($log->status === 'skipped')
                      <span class="badge bg-secondary">Opted Out</span>
                    @else
                      <span class="badge bg-danger">Failed</span>
                    @endif
                  </td>
                  <td><code>{{ $log->provider }}</code></td>
                  <td><small>{{ $log->created_at->diffForHumans() }}</small></td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">No communication logs recorded yet. Send your first message below!</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Quick Dispatch -->
    <div class="tab-pane fade" id="tab-compose">
      <div class="card shadow-sm border">
        <div class="card-header border-bottom py-3">
          <h5 class="card-title mb-0">Dispatch Notification Message</h5>
        </div>
        <div class="card-body p-4">
          <form action="{{ route('app-communication-send') }}" method="POST">
            @csrf
            <div class="row g-4">
              <div class="col-md-4">
                <label class="form-label">Communication Channel</label>
                <select name="channel" class="form-select" required>
                  <option value="email">Email (SMTP / SES / Resend)</option>
                  <option value="whatsapp">WhatsApp (Official Meta Cloud API)</option>
                  <option value="in_app">In-App Notification Hub</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Recipient (Email / +91 Phone)</label>
                <input type="text" name="recipient" class="form-control" placeholder="user@example.com or 9876543210" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Template Event</label>
                <select name="template_code" class="form-select" required>
                  <option value="order_confirmation">Order Confirmed</option>
                  <option value="order_shipped">Order Shipped</option>
                  <option value="abandoned_cart">Abandoned Cart Recovery</option>
                  <option value="return_approved">Return Approved</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Custom Message Body (Optional Fallback)</label>
                <textarea name="custom_message" class="form-control" rows="3" placeholder="Leave empty to use default template text..."></textarea>
              </div>
              <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                  <i class="icon-base bx bx-send me-1"></i> Send Dispatch
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Templates -->
    <div class="tab-pane fade" id="tab-templates">
      <div class="card shadow-sm border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
          <h5 class="card-title mb-0">Pre-Approved Notification Templates</h5>
          <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#newTemplateForm">
            <i class="icon-base bx bx-plus me-1"></i> New Template
          </button>
        </div>
        <div class="collapse p-4 border-bottom bg-light" id="newTemplateForm">
          <form action="{{ route('app-communication-template-save') }}" method="POST">
            @csrf
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Template Code</label>
                <input type="text" name="code" class="form-control" placeholder="e.g. flash_sale_alert" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Display Name</label>
                <input type="text" name="name" class="form-control" placeholder="Flash Sale Alert" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Channel</label>
                <select name="channel" class="form-select" required>
                  <option value="email">Email</option>
                  <option value="whatsapp">WhatsApp</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="Subject line">
              </div>
              <div class="col-12">
                <label class="form-label">Body with Variables (e.g. <code>&#123;&#123;customer_name&#125;&#125;</code>, <code>&#123;&#123;order_number&#125;&#125;</code>)</label>
                <textarea name="body" class="form-control" rows="3" required></textarea>
              </div>
              <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Save Template</button>
              </div>
            </div>
          </form>
        </div>
        <div class="card-body">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="card border p-3">
                <div class="d-flex justify-content-between mb-2">
                  <span class="badge bg-label-success">WhatsApp</span>
                  <strong>order_confirmation</strong>
                </div>
                <p class="small text-muted mb-0">🛒 <strong>{{ config('app.name') }} Order Confirmed!</strong><br>Hi &#123;&#123;customer_name&#125;&#125;, your order #&#123;&#123;order_number&#125;&#125; for ₹&#123;&#123;order_total&#125;&#125; has been placed successfully.</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border p-3">
                <div class="d-flex justify-content-between mb-2">
                  <span class="badge bg-label-primary">Email</span>
                  <strong>order_shipped</strong>
                </div>
                <p class="small text-muted mb-0">Dear &#123;&#123;customer_name&#125;&#125;,<br>Your order #&#123;&#123;order_number&#125;&#125; has shipped via &#123;&#123;carrier&#125;&#125;. Tracking: &#123;&#123;tracking_number&#125;&#125;.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Campaigns -->
    <div class="tab-pane fade" id="tab-campaigns">
      <div class="card shadow-sm border">
        <div class="card-header border-bottom py-3">
          <h5 class="card-title mb-0">Broadcast Marketing Campaign</h5>
        </div>
        <div class="card-body p-4">
          <form action="{{ route('app-communication-campaign-launch') }}" method="POST">
            @csrf
            <div class="row g-4">
              <div class="col-md-4">
                <label class="form-label">Campaign Name</label>
                <input type="text" name="name" class="form-control" placeholder="Summer Mega Sale" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Target Audience</label>
                <select name="audience_type" class="form-select" required>
                  <option value="all">All Registered Customers</option>
                  <option value="vip">VIP Customers (5+ Orders)</option>
                  <option value="inactive">Inactive Customers (30+ Days)</option>
                  <option value="abandoned">Abandoned Cart Users</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Channel</label>
                <select name="channel" class="form-select" required>
                  <option value="email">Email Broadcast</option>
                  <option value="whatsapp">WhatsApp Business Broadcast</option>
                  <option value="omnichannel">Omnichannel (Email + WhatsApp)</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Message Content</label>
                <textarea name="message_content" class="form-control" rows="3" placeholder="Get 20% off all catalog items this weekend only! Use code SUMMER20" required></textarea>
              </div>
              <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary" onclick="return confirm('Launch this marketing campaign to targeted recipients?');">
                  <i class="icon-base bx bx-rocket me-1"></i> Launch Campaign
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
