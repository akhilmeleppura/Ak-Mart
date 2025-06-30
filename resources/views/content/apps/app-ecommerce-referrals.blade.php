@extends('layouts/layoutMaster')

@section('title', 'eCommerce Referrals - Apps')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/app-ecommerce-referral.js'])
@endsection

@section('content')
<div class="row mb-6 g-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <h5 class="mb-1">$24,983</h5>
            <small>Total Earning</small>
          </div>
          <span class="badge bg-label-primary rounded-circle p-2">
            <i class="icon-base bx bx-dollar icon-lg"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <h5 class="mb-1">$8,647</h5>
            <small>Unpaid Earning</small>
          </div>
          <span class="badge bg-label-success rounded-circle p-2">
            <i class="icon-base bx bx-gift icon-lg"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <h5 class="mb-1">2,367</h5>
            <small>Signups</small>
          </div>
          <span class="badge bg-label-danger rounded-circle p-2">
            <i class="icon-base bx bx-user icon-lg"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div class="content-left">
            <h5 class="mb-1">4.5%</h5>
            <small>Conversion Rate</small>
          </div>
          <span class="badge bg-label-info rounded-circle p-2">
            <i class="icon-base bx bx-infinite icon-lg"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-6 g-6">
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="mb-1">How to use</h5>
        <p class="mb-6 card-subtitle mt-0">Integrate your referral code in 3 easy steps.</p>
        <div class="d-flex flex-column flex-sm-row justify-content-between text-center gap-6">
          <div class="d-flex flex-column align-items-center">
            <div class="p-4 border border-primary rounded-circle border-dashed mb-0"><i
                class="icon-base bx bx-rocket text-primary icon-36px"></i></div>
            <p class="my-2 w-75">Create & validate your referral link and get</p>
            <h6 class="text-primary mb-0">$50</h6>
          </div>
          <div class="d-flex flex-column align-items-center">
            <div class="p-4 border border-primary rounded-circle border-dashed mb-0"><i
                class="icon-base bx bxs-user-badge text-primary icon-36px"></i></div>
            <p class="my-2 w-75">For every new signup you get</p>
            <h6 class="text-primary mb-0">10%</h6>
          </div>
          <div class="d-flex flex-column align-items-center">
            <div class="p-4 border border-primary rounded-circle border-dashed mb-0"><i
                class="icon-base bx bxs-paper-plane text-primary icon-36px"></i></div>
            <p class="my-2 w-75">Get other friends to generate link and get</p>
            <h6 class="text-primary mb-0">$100</h6>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-body">
        <form class="referral-form" onsubmit="return false">
          <div class="mb-6 mt-1">
            <h5 class="mb-5">Invite your friends</h5>
            <div class="d-flex gap-4 align-items-end">
              <div class="w-100">
                <label class="form-label mb-1" for="referralEmail">Enter friend’s email address and invite them</label>
                <input type="email" id="referralEmail" name="referralEmail" class="form-control w-100"
                  placeholder="Email address" />
              </div>
              <div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </div>
          <div>
            <h5 class="mb-5">Share the referral link</h5>
            <div class="d-flex gap-4 align-items-end">
              <div class="w-100">
                <label class="form-label mb-1" for="referralLink">Share referral link in social media</label>
                <input type="text" id="referralLink" name="referralLink" class="form-control w-100 h-px-40"
                  placeholder="pixinvent.com/?ref=6479" />
              </div>
              <div class="d-flex">
                <button type="button" class="btn btn-facebook btn-icon me-2"><i
                    class="icon-base bx bxl-facebook text-white"></i></button>
                <button type="button" class="btn btn-twitter btn-icon"><i
                    class="icon-base bx bxl-twitter text-white"></i></button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Referral List Table -->
<div class="card">
  <div class="card-datatable">
    <table class="datatables-referral table border-top">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th>Users</th>
          <th class="text-nowrap">Referred ID</th>
          <th>Status</th>
          <th>Value</th>
          <th class="text-nowrap">Earnings</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

@endsection
