@php
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('layouts/blankLayout')

@section('title', __('Verify Your Identity') . ' — AK-Mart')

@section('page-style')
<style>
    :root {
        --primary: #696cff;
        --primary-dark: #5a5cc7;
        --bg: #f0f2f5;
    }

    body { background: var(--bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; }

    .otp-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 48px rgba(105,108,255,0.12);
        padding: 48px 44px;
        width: 100%;
        max-width: 460px;
        margin: 24px auto;
    }

    .brand-logo {
        text-align: center;
        margin-bottom: 32px;
    }
    .brand-logo h1 {
        font-size: 26px;
        font-weight: 800;
        background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.5px;
    }
    .brand-logo p { color: #888; font-size: 13px; margin-top: 4px; }

    .otp-icon {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, #696cff20 0%, #9155fd20 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        font-size: 32px;
    }

    .otp-title { font-size: 22px; font-weight: 700; color: #1a1a2e; text-align: center; margin-bottom: 8px; }
    .otp-subtitle { color: #666; font-size: 14px; text-align: center; margin-bottom: 32px; line-height: 1.6; }
    .otp-subtitle strong { color: #333; }

    /* Segmented OTP inputs */
    .otp-inputs {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-bottom: 8px;
    }
    .otp-input {
        width: 52px;
        height: 60px;
        text-align: center;
        font-size: 24px;
        font-weight: 700;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        outline: none;
        transition: all 0.2s;
        color: #333;
        background: #fafafa;
        font-family: 'Courier New', monospace;
    }
    .otp-input:focus {
        border-color: var(--primary);
        background: #f0efff;
        box-shadow: 0 0 0 4px rgba(105,108,255,0.12);
        transform: translateY(-2px);
    }
    .otp-input.filled { border-color: var(--primary); background: #f0efff; color: var(--primary); }
    .otp-input.error { border-color: #e74c3c; background: #fff5f5; }

    .otp-hidden { display: none; }

    /* Timer */
    .timer-section {
        text-align: center;
        margin: 16px 0 8px;
        font-size: 13px;
        color: #888;
    }
    .timer-badge {
        display: inline-block;
        background: #f0efff;
        color: var(--primary);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 14px;
        font-weight: 600;
        min-width: 60px;
    }
    .timer-badge.urgent { background: #fff0f0; color: #e74c3c; }

    .btn-verify {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 16px;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }
    .btn-verify:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(105,108,255,0.4); }
    .btn-verify:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .resend-section { text-align: center; margin-top: 20px; }
    .btn-resend {
        background: none;
        border: none;
        color: var(--primary);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-resend:hover { background: #f0efff; }
    .btn-resend:disabled { color: #aaa; cursor: not-allowed; background: none; }

    .back-link { text-align: center; margin-top: 16px; }
    .back-link a { color: #888; font-size: 13px; text-decoration: none; }
    .back-link a:hover { color: var(--primary); }

    .alert-error {
        background: #fff5f5;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 12px 16px;
        color: #b91c1c;
        font-size: 13px;
        margin-bottom: 20px;
        text-align: center;
    }
    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 12px 16px;
        color: #166534;
        font-size: 13px;
        margin-bottom: 20px;
        text-align: center;
    }

    .resend-info {
        text-align: center;
        font-size: 12px;
        color: #aaa;
        margin-top: 6px;
    }

    @media (max-width: 480px) {
        .otp-card { padding: 32px 20px; margin: 12px; }
        .otp-input { width: 44px; height: 52px; font-size: 20px; }
        .otp-inputs { gap: 8px; }
    }
</style>
@endsection

@section('content')
<div class="otp-card">
    {{-- Brand --}}
    <div class="brand-logo">
        <h1>🛒 AK-Mart</h1>
        <p>{{ __('Secure Login Verification') }}</p>
    </div>

    {{-- Icon --}}
    <div class="otp-icon">📱</div>

    <h2 class="otp-title">{{ __('Enter Verification Code') }}</h2>
    <p class="otp-subtitle">
        {{ __('We sent a :digits-digit code to', ['digits' => config('otp.length', 6)]) }}<br>
        <strong>{{ $identifier }}</strong>
    </p>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    {{-- OTP Form --}}
    <form id="otpForm" action="{{ route('auth.otp.verify') }}" method="POST" autocomplete="off">
        @csrf

        {{-- Hidden full OTP value (populated by JS) --}}
        <input type="hidden" name="otp" id="otpFull">

        {{-- Segmented Inputs --}}
        <div class="otp-inputs" id="otpInputs">
            @for ($i = 0; $i < config('otp.length', 6); $i++)
                <input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]"
                    maxlength="1"
                    class="otp-input @error('otp') error @enderror"
                    id="otp{{ $i }}"
                    aria-label="{{ __('Digit') }} {{ $i + 1 }}"
                    autocomplete="one-time-code"
                >
            @endfor
        </div>

        {{-- Timer --}}
        <div class="timer-section" id="timerSection">
            @if ($expiresAt)
                <span>{{ __('Code expires in') }}</span>
                <span class="timer-badge" id="timerBadge">--:--</span>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-verify" id="btnVerify" disabled>
            <span id="btnText">{{ __('Verify Code') }}</span>
        </button>
    </form>

    {{-- Resend --}}
    <div class="resend-section">
        <p style="font-size:13px; color:#888; margin-bottom:8px;">{{ __("Didn't receive the code?") }}</p>
        <button class="btn-resend" id="btnResend" @if(!$canResend) disabled @endif>
            <span id="resendText">
                @if(!$canResend && $secondsLeft > 0)
                    {{ __('Resend in') }} <span id="resendCountdown">{{ $secondsLeft }}</span>s
                @else
                    {{ __('Resend Code') }}
                @endif
            </span>
        </button>
        <div class="resend-info">
            {{ __(':count of :max resends used', ['count' => $resendCount, 'max' => $maxResends]) }}
        </div>
    </div>

    {{-- Back --}}
    <div class="back-link">
        <a href="{{ route('auth-login-basic') }}">← {{ __('Back to Login') }}</a>
    </div>
</div>
@endsection

@section('page-script')
<script>
(function () {
    'use strict';

    const OTP_LENGTH   = {{ config('otp.length', 6) }};
    const inputs       = Array.from({ length: OTP_LENGTH }, (_, i) => document.getElementById('otp' + i));
    const hiddenInput  = document.getElementById('otpFull');
    const btnVerify    = document.getElementById('btnVerify');
    const btnResend    = document.getElementById('btnResend');
    const resendText   = document.getElementById('resendText');
    const timerBadge   = document.getElementById('timerBadge');

    // -----------------------------------------------------------------
    // OTP Input Logic
    // -----------------------------------------------------------------

    function getFullOtp() {
        return inputs.map(i => i.value).join('');
    }

    function updateSubmitButton() {
        const full = getFullOtp();
        hiddenInput.value = full;
        btnVerify.disabled = full.length < OTP_LENGTH;
        inputs.forEach((inp, idx) => {
            inp.classList.toggle('filled', inp.value.length === 1);
        });
    }

    inputs.forEach((input, idx) => {
        input.addEventListener('input', e => {
            const val = e.target.value.replace(/\D/g, '');
            if (val.length > 1) {
                // Handle paste into single cell
                const digits = val.split('');
                digits.forEach((d, i) => {
                    if (inputs[idx + i]) inputs[idx + i].value = d;
                });
                const nextFocus = Math.min(idx + val.length, OTP_LENGTH - 1);
                inputs[nextFocus]?.focus();
            } else {
                input.value = val;
                if (val && inputs[idx + 1]) inputs[idx + 1].focus();
            }
            updateSubmitButton();
        });

        input.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && ! input.value && inputs[idx - 1]) {
                inputs[idx - 1].value = '';
                inputs[idx - 1].focus();
                updateSubmitButton();
            }
            if (e.key === 'ArrowLeft' && inputs[idx - 1]) inputs[idx - 1].focus();
            if (e.key === 'ArrowRight' && inputs[idx + 1]) inputs[idx + 1].focus();
            if (e.key === 'Enter') {
                e.preventDefault();
                if (getFullOtp().length === OTP_LENGTH) document.getElementById('otpForm').submit();
            }
        });

        // Handle paste anywhere in the group
        input.addEventListener('paste', e => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            paste.split('').forEach((d, i) => {
                if (inputs[i]) inputs[i].value = d;
            });
            const lastFilled = Math.min(paste.length - 1, OTP_LENGTH - 1);
            inputs[lastFilled]?.focus();
            updateSubmitButton();
        });
    });

    // Auto-focus first input
    inputs[0]?.focus();

    // -----------------------------------------------------------------
    // Expiry Countdown Timer
    // -----------------------------------------------------------------

    @if ($expiresAt)
    const expiresAt = new Date("{{ $expiresAt->toISOString() }}").getTime();

    function updateTimer() {
        const now  = Date.now();
        const diff = Math.max(0, Math.floor((expiresAt - now) / 1000));
        const mm   = String(Math.floor(diff / 60)).padStart(2, '0');
        const ss   = String(diff % 60).padStart(2, '0');

        if (timerBadge) {
            timerBadge.textContent = mm + ':' + ss;
            timerBadge.classList.toggle('urgent', diff <= 60);
        }

        if (diff <= 0) {
            clearInterval(timerInterval);
            if (timerBadge) timerBadge.textContent = '00:00';
            document.getElementById('timerSection').innerHTML =
                '<span style="color:#e74c3c; font-size:13px; font-weight:600;">⚠️ {{ __("Code expired — please resend") }}</span>';
            btnVerify.disabled = true;
        }
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
    @endif

    // -----------------------------------------------------------------
    // Resend Countdown
    // -----------------------------------------------------------------

    let resendCooldown = {{ $secondsLeft ?? 0 }};

    function startResendCountdown(seconds) {
        btnResend.disabled = true;
        resendCooldown = seconds;

        const tick = setInterval(() => {
            resendCooldown--;
            resendText.innerHTML = '{{ __("Resend in") }} <span id="resendCountdown">' + resendCooldown + '</span>s';
            if (resendCooldown <= 0) {
                clearInterval(tick);
                resendText.textContent = '{{ __("Resend Code") }}';
                btnResend.disabled = false;
            }
        }, 1000);
    }

    if (resendCooldown > 0) {
        startResendCountdown(resendCooldown);
    }

    // -----------------------------------------------------------------
    // Resend AJAX
    // -----------------------------------------------------------------

    btnResend?.addEventListener('click', () => {
        btnResend.disabled = true;
        resendText.textContent = '{{ __("Sending...") }}';

        fetch('{{ route("auth.otp.resend") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({}),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showFeedback(data.message, 'success');
                startResendCountdown(data.cooldown || 60);
            } else {
                showFeedback(data.message || '{{ __("Failed to resend. Try again.") }}', 'error');
                if (!data.seconds_left) btnResend.disabled = false;
                else startResendCountdown(data.seconds_left);
            }
        })
        .catch(() => {
            showFeedback('{{ __("Network error. Please try again.") }}', 'error');
            btnResend.disabled = false;
            resendText.textContent = '{{ __("Resend Code") }}';
        });
    });

    // -----------------------------------------------------------------
    // Form submit state
    // -----------------------------------------------------------------

    document.getElementById('otpForm')?.addEventListener('submit', () => {
        btnVerify.disabled = true;
        document.getElementById('btnText').textContent = '{{ __("Verifying...") }}';
    });

    // -----------------------------------------------------------------
    // Feedback helper
    // -----------------------------------------------------------------

    function showFeedback(message, type) {
        let el = document.getElementById('otpFeedback');
        if (! el) {
            el = document.createElement('div');
            el.id = 'otpFeedback';
            document.getElementById('otpInputs').parentNode.insertBefore(el, document.getElementById('otpInputs'));
        }
        el.className = type === 'success' ? 'alert-success' : 'alert-error';
        el.textContent = message;
        setTimeout(() => { if (el) el.remove(); }, 5000);
    }
})();
</script>
@endsection
