@php
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Login') . ' — AK-Mart')

@section('page-style')
  @vite(['resources/css/app.css'])
  <style>
    /* Animated Gradient Mesh & Background */
    .ak-login-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(-45deg, #0F172A, #1E293B, #1E3A8A, #0F172A);
      background-size: 400% 400%;
      animation: akGradientMove 15s ease infinite;
      position: relative;
      overflow: hidden;
    }

    @keyframes akGradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Ambient Background Orbs */
    .ak-bg-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.35;
      pointer-events: none;
      animation: akOrbFloat 12s ease-in-out infinite alternate;
    }
    .ak-bg-orb-1 {
      top: -10%;
      left: -10%;
      width: 450px;
      height: 450px;
      background: #2563EB;
    }
    .ak-bg-orb-2 {
      bottom: -15%;
      right: -10%;
      width: 500px;
      height: 500px;
      background: #14B8A6;
      animation-delay: -6s;
    }

    @keyframes akOrbFloat {
      0% { transform: translate(0, 0) scale(1); }
      100% { transform: translate(40px, 30px) scale(1.1); }
    }

    /* Card Container with Glass Border */
    .ak-login-card {
      width: 100%;
      max-width: 1050px;
      border-radius: 1.5rem;
      overflow: hidden;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.15);
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(20px);
      position: relative;
      z-index: 2;
    }

    /* Left Showcase Column */
    .ak-login-left {
      background: linear-gradient(135deg, #1E4ED8 0%, #2563EB 50%, #0D9488 100%);
      color: #ffffff;
      padding: 3.5rem;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
    }

    /* Floating Animated Stat Cards */
    .ak-stat-card {
      background: rgba(255, 255, 255, 0.16);
      backdrop-filter: blur(14px);
      border: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 1rem;
      padding: 1.1rem 1.35rem;
      margin-top: 0.85rem;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    .ak-stat-card:hover {
      transform: translateY(-4px) scale(1.02);
      background: rgba(255, 255, 255, 0.22);
      border-color: rgba(255, 255, 255, 0.4);
    }

    /* Keyframe Floating Animations */
    .ak-float-1 { animation: floatSlow 5s ease-in-out infinite; }
    .ak-float-2 { animation: floatSlow 6s ease-in-out infinite 1.5s; }
    .ak-float-3 { animation: floatSlow 5.5s ease-in-out infinite 0.75s; }

    @keyframes floatSlow {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-9px); }
    }

    /* Pulse Dot */
    .ak-pulse-dot {
      width: 10px;
      height: 10px;
      background-color: #22C55E;
      border-radius: 50%;
      display: inline-block;
      box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
      animation: akPulse 2s infinite;
    }

    @keyframes akPulse {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
      70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    /* Form Column Right */
    .ak-login-right {
      padding: 3.5rem;
      background: #FFFFFF;
    }

    /* Input Focus Glow & Styling */
    .form-control:focus, .input-group-text:focus {
      border-color: #2563EB;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }

    /* Demo Quick Role Pill Badge */
    .demo-role-pill {
      cursor: pointer;
      transition: all 0.2s ease;
      user-select: none;
    }
    .demo-role-pill:hover {
      transform: translateY(-1px);
      background-color: var(--ak-primary-light);
      color: var(--ak-primary);
    }

    /* Submit Button Shine Effect */
    .btn-shine {
      position: relative;
      overflow: hidden;
    }
    .btn-shine::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(
        60deg,
        transparent,
        rgba(255, 255, 255, 0.25),
        transparent
      );
      transform: rotate(30deg);
      transition: all 0.5s ease;
    }
    .btn-shine:hover::after {
      left: 100%;
    }
  </style>
@endsection

@section('content')
<div class="ak-login-wrapper py-5 px-3">
  <!-- Glowing Background Orbs -->
  <div class="ak-bg-orb ak-bg-orb-1"></div>
  <div class="ak-bg-orb ak-bg-orb-2"></div>

  <div class="ak-login-card">
    <div class="row g-0">
      <!-- Left Column: Brand & Animated Dashboard Showcase -->
      <div class="col-lg-6 ak-login-left d-none d-lg-flex">
        <div>
          <div class="d-flex align-items-center mb-4">
            @include('_partials.macros', ['height' => 52])
            <span class="app-brand-text fs-3 text-white fw-bold ms-3">AK-Mart</span>
          </div>
          <h2 class="text-white fw-bold display-6 mb-2">{{ __('Smart Management for Modern Stores') }}</h2>
          <p class="text-white-50 fs-6">{{ __('Manage inventory, automate POS checkouts, issue purchase orders, and track real-time revenue.') }}</p>
        </div>

        <div class="my-4">
          <!-- Animated Stat Card 1: Today's Revenue -->
          <div class="ak-stat-card ak-float-1">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-sm bg-white bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                  <i class="bx bx-dollar-circle text-white fs-4"></i>
                </div>
                <div>
                  <span class="text-white-50 small d-block">{{ __('Today\'s Revenue') }}</span>
                  <span class="fw-bold fs-5 text-white">$4,850.00</span>
                </div>
              </div>
              <span class="badge bg-success text-white px-2.5 py-1.5"><i class="bx bx-trending-up me-1"></i>+18.4%</span>
            </div>
          </div>

          <!-- Animated Stat Card 2: Active POS Orders -->
          <div class="ak-stat-card ak-float-2">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-sm bg-white bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                  <i class="bx bx-cart text-white fs-4"></i>
                </div>
                <div>
                  <span class="text-white-50 small d-block">{{ __('Active POS Sales') }}</span>
                  <span class="fw-bold fs-5 text-white">128 {{ __('Transactions') }}</span>
                </div>
              </div>
              <span class="badge bg-white bg-opacity-25 text-white px-2.5 py-1.5 d-flex align-items-center gap-1.5">
                <span class="ak-pulse-dot"></span> {{ __('Live Terminal') }}
              </span>
            </div>
          </div>

          <!-- Animated Stat Card 3: Stock Alert Monitor -->
          <div class="ak-stat-card ak-float-3">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-sm bg-white bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                  <i class="bx bx-package text-white fs-4"></i>
                </div>
                <div>
                  <span class="text-white-50 small d-block">{{ __('Inventory Monitor') }}</span>
                  <span class="fw-bold fs-6 text-white">{{ __('All Stock Synced') }}</span>
                </div>
              </div>
              <span class="badge bg-info text-white">{{ __('Automated') }}</span>
            </div>
          </div>
        </div>

        <div class="pt-3 border-top border-white-10 text-white-50 small d-flex justify-content-between align-items-center">
          <span>&copy; {{ date('Y') }} AK-Mart.</span>
          <span class="badge bg-white bg-opacity-15 text-white-50">v1.0.0</span>
        </div>
      </div>

      <!-- Right Column: Login Form & Interactive Demo Quick Fill -->
      <div class="col-lg-6 ak-login-right d-flex flex-column justify-content-between">
        <div>
          <div class="d-lg-none text-center mb-4">
            @include('_partials.macros', ['height' => 48])
            <div class="app-brand-text fs-3 text-heading fw-bold mt-2">AK-Mart</div>
          </div>

          <div class="mb-4">
            <h3 class="fw-bold mb-1 text-heading">{{ __('Welcome Back') }} 👋</h3>
            <p class="text-muted">{{ __('Sign in to your AK-Mart store management console') }}</p>
          </div>

          <!-- Quick Fill Demo Accounts with Supreme Admin Access -->
          <div class="mb-4 p-3 bg-light rounded-3 border">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <small class="fw-bold text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">{{ __('Quick 1-Click Access:') }}</small>
              <span class="badge bg-warning text-dark fw-bold px-2 py-1" style="font-size: 0.7rem;"><i class="bx bxs-crown me-1"></i>{{ __('Supreme Access') }}</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
              <span class="badge bg-white border text-dark demo-role-pill shadow-xs d-inline-flex align-items-center py-2 px-2.5" onclick="fillDemo('supreme@ak-mart.com', 'supreme123')" style="border-color: #F59E0B !important; background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%) !important;">
                <i class="bx bxs-crown text-warning me-1.5 fs-6"></i> <strong class="text-dark">{{ __('Supreme Admin') }}</strong>
              </span>
              <span class="badge bg-white border text-dark demo-role-pill shadow-xs d-inline-flex align-items-center py-2 px-2.5" onclick="fillDemo('admin@ak-mart.com', 'password')">
                <i class="bx bx-shield-quarter text-primary me-1.5 fs-6"></i> {{ __('AK-Mart Admin') }}
              </span>
              <span class="badge bg-white border text-dark demo-role-pill shadow-xs d-inline-flex align-items-center py-2 px-2.5" onclick="fillDemo('manager@ak-mart.com', 'password')">
                <i class="bx bx-briefcase text-info me-1.5 fs-6"></i> {{ __('Manager') }}
              </span>
              <span class="badge bg-white border text-dark demo-role-pill shadow-xs d-inline-flex align-items-center py-2 px-2.5" onclick="fillDemo('cashier@ak-mart.com', 'password')">
                <i class="bx bx-terminal text-success me-1.5 fs-6"></i> {{ __('Cashier') }}
              </span>
            </div>
          </div>

          @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 mb-3 small rounded-3">
              <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form id="formAuthentication" action="{{ route('auth-login-basic-store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">{{ __('Email or Username') }}</label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bx bx-envelope fs-5"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="admin@ak-mart.com" required autofocus value="admin@ak-mart.com" />
              </div>
            </div>

            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-semibold mb-0">{{ __('Password') }}</label>
                <a href="{{ url('auth/forgot-password-basic') }}" class="small text-primary text-decoration-none">{{ __('Forgot password?') }}</a>
              </div>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bx bx-lock-alt fs-5"></i></span>
                <input type="password" id="password" class="form-control border-start-0 border-end-0 ps-0" name="password" placeholder="••••••••" required value="password" />
                <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword">
                  <i class="bx bx-hide" id="toggleIcon"></i>
                </button>
              </div>
            </div>

            <div class="mb-4 d-flex justify-content-between align-items-center">
              <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember" checked />
                <label class="form-check-label text-muted" for="remember-me">{{ __('Keep me signed in') }}</label>
              </div>
            </div>

            <button class="btn btn-ak-primary btn-shine w-100 py-3 fs-6 shadow-sm mb-3" type="submit" id="submitBtn">
              <span>{{ __('Sign In to AK-Mart Console') }}</span>
              <i class="bx bx-right-arrow-alt ms-1 fs-5 align-middle"></i>
            </button>
          </form>
        </div>

        <div class="text-center text-muted small pt-3 border-top">
          {{ __('Need assistance? Contact system administrator or') }} <a href="#" class="text-primary fw-semibold">{{ __('AK-Mart Support') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    const form = document.getElementById('formAuthentication');
    const submitBtn = document.getElementById('submitBtn');

    if (toggleBtn && passwordInput && toggleIcon) {
      toggleBtn.addEventListener('click', function() {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        toggleIcon.className = isPassword ? 'bx bx-show fs-5' : 'bx bx-hide fs-5';
      });
    }

    if (form && submitBtn) {
      form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ' + @json(__('Authenticating...'));
      });
    }
  });

  function fillDemo(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
    
    // Quick highlight effect
    const emailInput = document.getElementById('email');
    emailInput.classList.add('is-valid');
    setTimeout(() => emailInput.classList.remove('is-valid'), 1200);
  }
</script>
@endsection
