@extends('layouts/layoutMaster')

@section('title', 'eCommerce Settings Notifications - Apps')

@section('page-script')
@vite('resources/assets/js/app-ecommerce-settings.js')
@endsection

@section('content')
<div class="row g-6">
  <!-- Navigation -->
  <div class="col-12 col-lg-4">
    @include('content.apps._settings-sidebar')
  </div>
  <!-- /Navigation -->

  <!-- Options -->
  <div class="col-12 col-lg-8 pt-6 pt-lg-0">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="icon-base bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    <form method="POST" action="{{ route('app-ecommerce-settings-notifications-save') }}">
      @csrf
      <div class="tab-content p-0">
        <!-- Notification Tab -->
        <div class="tab-pane fade show active" id="notifications" role="tabpanel">
          <div class="card mb-6">
            <div class="card-body">
              <h5 class="card-title mb-4">Customer</h5>
              <div class="card shadow-none mb-6 border-0">
                <div class="table-responsive border border-top-0 rounded">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="text-nowrap w-50">Type</th>
                        <th class="text-nowrap text-center w-25">Email</th>
                        <th class="text-nowrap text-center w-25">App</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-nowrap text-heading">New customer sign up</td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_signup_email" value="1" id="notify_signup_email" {{ ($settings['notify_signup_email'] ?? '1') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_signup_app" value="1" id="notify_signup_app" {{ ($settings['notify_signup_app'] ?? '1') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-nowrap text-heading">Customer account password reset</td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_reset_email" value="1" id="notify_reset_email" {{ ($settings['notify_reset_email'] ?? '1') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_reset_app" value="1" id="notify_reset_app" {{ ($settings['notify_reset_app'] ?? '1') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                      </tr>
                      <tr class="border-transparent">
                        <td class="text-nowrap text-heading">Customer account invite</td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_invite_email" value="1" id="notify_invite_email" {{ ($settings['notify_invite_email'] ?? '0') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_invite_app" value="1" id="notify_invite_app" {{ ($settings['notify_invite_app'] ?? '0') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <h5 class="card-title mb-4">Orders</h5>
              <div class="card shadow-none mb-6 border-0">
                <div class="table-responsive border border-top-0 rounded">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="text-nowrap w-50">Type</th>
                        <th class="text-nowrap text-center w-25">Email</th>
                        <th class="text-nowrap text-center w-25">App</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-nowrap text-heading">Order purchase</td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_purchase_email" value="1" id="notify_purchase_email" {{ ($settings['notify_purchase_email'] ?? '1') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_purchase_app" value="1" id="notify_purchase_app" {{ ($settings['notify_purchase_app'] ?? '1') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-nowrap text-heading">Order cancelled</td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_cancel_email" value="1" id="notify_cancel_email" {{ ($settings['notify_cancel_email'] ?? '1') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_cancel_app" value="1" id="notify_cancel_app" {{ ($settings['notify_cancel_app'] ?? '0') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-nowrap text-heading">Order refund request</td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_refund_email" value="1" id="notify_refund_email" {{ ($settings['notify_refund_email'] ?? '0') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                        <td>
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" name="notify_refund_app" value="1" id="notify_refund_app" {{ ($settings['notify_refund_app'] ?? '1') == '1' ? 'checked' : '' }} />
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-4">
            <button type="reset" class="btn btn-label-secondary">Discard</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </div>
      </div>
    </form>
  </div>
  <!-- /Options-->
</div>
@endsection
