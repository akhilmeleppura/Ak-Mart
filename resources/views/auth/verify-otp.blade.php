@php
    $pageConfigs = ['myLayout' => 'blank'];
    $demoOtp = session('demo_plain_otp');
@endphp

@extends('layouts/blankLayout')

@section('title', __('Verify Your Identity') . ' — AK-Mart')

@section('page-style')
<style>
    :root {
        --primary: #4F46E5;
        --primary-dark: #4338CA;
        --bg: #F8FAFC;
    }

    body { background: var(--bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; }

    .otp-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 16px 48px -12px rgba(15, 23, 42, 0.15);
        padding: 42px 38px;
        width: 100%;
        max-width: 480px;
        margin: 20px auto;
        border: 1px solid #E2E8F0;
        position: relative;
    }

    .brand-logo {
        text-align: center;
        margin-bottom: 24px;
    }
    .brand-logo h1 {
        font-size: 26px;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: -0.5px;
    }
    .brand-logo p { color: #64748B; font-size: 13px; margin-top: 4px; }

    .otp-icon {
        width: 68px;
        height: 68px;
        background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 30px;
        color: var(--primary);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.15);
    }

    .otp-title { font-size: 22px; font-weight: 800; color: #0F172A; text-align: center; margin-bottom: 6px; }
    .otp-subtitle { color: #64748B; font-size: 13.5px; text-align: center; margin-bottom: 24px; line-height: 1.5; }
    .otp-subtitle strong { color: #0F172A; }

    /* Segmented OTP inputs */
    .otp-inputs {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-bottom: 12px;
    }
    .otp-input {
        width: 54px;
        height: 62px;
        text-align: center;
        font-size: 24px;
        font-weight: 800;
        border: 2px solid #E2E8F0;
        border-radius: 14px;
        outline: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        color: #0F172A;
        background: #F8FAFC;
        font-family: monospace;
    }
    .otp-input:focus {
        border-color: var(--primary);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        transform: translateY(-2px);
    }
    .otp-input.filled { 
        border-color: var(--primary); 
        background: #EEF2FF; 
        color: var(--primary); 
    }
    .otp-input.error { border-color: #EF4444; background: #FEF2F2; color: #DC2626; }

    /* SMS Notification Floating Banner */
    .sms-notification-banner {
        background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
        border: 1.5px solid #C7D2FE;
        border-radius: 16px;
        padding: 14px 18px;
        margin-bottom: 20px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 6px 18px -4px rgba(79, 70, 229, 0.15);
        animation: slideDownFade 0.5s ease-out;
    }
    .sms-notification-banner:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px -4px rgba(79, 70, 229, 0.25);
        border-color: #818CF8;
    }

    @keyframes slideDownFade {
        from { opacity: 0; transform: translateY(-12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Timer */
    .timer-section {
        text-align: center;
        margin: 12px 0;
        font-size: 13px;
        color: #64748B;
    }
    .timer-badge {
        display: inline-block;
        background: #EEF2FF;
        color: var(--primary);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 13px;
        font-weight: 700;
        font-family: monospace;
    }
    .timer-badge.urgent { background: #FEF2F2; color: #DC2626; }

    .btn-verify {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 14px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
    }
    .btn-verify:hover:not(:disabled) { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4); 
    }
    .btn-verify:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }

    .resend-section { text-align: center; margin-top: 18px; }
    .btn-resend {
        background: none;
        border: none;
        color: var(--primary);
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        padding: 6px 14px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-resend:hover:not(:disabled) { background: #EEF2FF; }
    .btn-resend:disabled { color: #94A3B8; cursor: not-allowed; }

    .back-link { text-align: center; margin-top: 16px; }
    .back-link a { color: #64748B; font-size: 13px; text-decoration: none; font-weight: 600; }
    .back-link a:hover { color: var(--primary); }

    .alert-error {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        border-radius: 12px;
        padding: 12px 16px;
        color: #DC2626;
        font-size: 13px;
        margin-bottom: 18px;
        text-align: center;
    }
    .alert-success {
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        border-radius: 12px;
        padding: 12px 16px;
        color: #065F46;
        font-size: 13px;
        margin-bottom: 18px;
        text-align: center;
    }

    @media (max-width: 480px) {
        .otp-card { padding: 32px 20px; margin: 12px; }
        .otp-input { width: 44px; height: 54px; font-size: 20px; }
        .otp-inputs { gap: 6px; }
    }
</style>
@endsection

@section('content')
<div class="otp-card">
    {{-- Brand --}}
    <div class="brand-logo">
        <h1><i class="bx bx-store-alt text-primary me-1"></i> AK-Mart</h1>
        <p>{{ __('Secure Login & OTP Verification') }}</p>
    </div>

    {{-- Icon --}}
    <div class="otp-icon">
        <i class="bx bx-mobile-alt"></i>
    </div>

    <h2 class="otp-title">{{ __('Welcome Back,') }} {{ $userName ?? 'User' }} 👋</h2>
    <p class="otp-subtitle mb-3">
        {{ __('We sent a 6-digit verification code to your registered mobile number') }}<br>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-bold font-monospace mt-1" style="font-size: 13.5px;">
            <i class="bx bx-phone me-1"></i> {{ $userPhone ?? $identifier }}
        </span>
    </p>

    {{-- Realistic SMS Floating Notification Banner --}}
    @if ($demoOtp)
        <div class="sms-notification-banner" id="smsNotificationBanner" onclick="autoFillAndSubmit('{{ $demoOtp }}')">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="fs-3">📲</span>
                    <div>
                        <strong class="d-block text-dark" style="font-size: 12.5px;">SMS Alert to {{ $userName ?? 'User' }} • Just Now</strong>
                        <span class="small text-muted">Your Login OTP is <strong class="text-primary font-monospace fs-6">{{ $demoOtp }}</strong></span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-xs" style="font-size: 11px;">
                    ⚡ Auto-Fill & Enter
                </button>
            </div>
        </div>
    @endif

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
            <span id="btnText"><i class="bx bx-check-shield me-1"></i> {{ __('Verify & Sign In') }}</span>
        </button>
    </form>

    {{-- Resend --}}
    <div class="resend-section">
        <p style="font-size:13px; color:#64748B; margin-bottom:4px;">{{ __("Didn't receive the SMS code?") }}</p>
        <button class="btn-resend" id="btnResend" @if(!$canResend) disabled @endif>
            <span id="resendText">
                @if(!$canResend && $secondsLeft > 0)
                    {{ __('Resend in') }} <span id="resendCountdown">{{ $secondsLeft }}</span>s
                @else
                    <i class="bx bx-refresh me-1"></i>{{ __('Resend Code') }}
                @endif
            </span>
        </button>
        <div class="small text-muted" style="font-size: 11.5px;">
            {{ __(':count of :max resends used', ['count' => $resendCount, 'max' => $maxResends]) }}
        </div>
    </div>

    {{-- Back --}}
    <div class="back-link">
        <a href="{{ route('auth-login-basic') }}">← {{ __('Back to Password Sign In') }}</a>
    </div>
</div>
@endsection

@section('page-script')
<script>
const OTP_LENGTH   = {{ config('otp.length', 6) }};
const inputs       = Array.from({ length: OTP_LENGTH }, (_, i) => document.getElementById('otp' + i));
const hiddenInput  = document.getElementById('otpFull');
const btnVerify    = document.getElementById('btnVerify');
const btnResend    = document.getElementById('btnResend');
const resendText   = document.getElementById('resendText');
const timerBadge   = document.getElementById('timerBadge');

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

    // Auto-enter if full 6 digits are typed
    if (full.length === OTP_LENGTH) {
        setTimeout(() => {
            document.getElementById('otpForm').submit();
        }, 300);
    }
}

// 1-Click Auto Fill & Auto Enter Function
function autoFillAndSubmit(code) {
    if (!code) return;
    const digits = code.toString().split('');
    
    digits.forEach((digit, index) => {
        setTimeout(() => {
            if (inputs[index]) {
                inputs[index].value = digit;
                inputs[index].classList.add('filled');
            }
            if (index === digits.length - 1) {
                updateSubmitButton();
            }
        }, index * 70);
    });
}

inputs.forEach((input, idx) => {
    input.addEventListener('input', e => {
        const val = e.target.value.replace(/\D/g, '');
        if (val.length > 1) {
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
        if (e.key === 'Backspace' && !input.value && inputs[idx - 1]) {
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

// Expiry Timer
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
            '<span style="color:#DC2626; font-size:13px; font-weight:700;">⚠️ {{ __("Code expired — please resend") }}</span>';
        btnVerify.disabled = true;
    }
}

const timerInterval = setInterval(updateTimer, 1000);
updateTimer();
@endif

// Resend Countdown
let resendCooldown = {{ $secondsLeft ?? 0 }};

function startResendCountdown(seconds) {
    btnResend.disabled = true;
    resendCooldown = seconds;

    const tick = setInterval(() => {
        resendCooldown--;
        resendText.innerHTML = '{{ __("Resend in") }} <span id="resendCountdown">' + resendCooldown + '</span>s';
        if (resendCooldown <= 0) {
            clearInterval(tick);
            resendText.innerHTML = '<i class="bx bx-refresh me-1"></i>{{ __("Resend Code") }}';
            btnResend.disabled = false;
        }
    }, 1000);
}

if (resendCooldown > 0) {
    startResendCountdown(resendCooldown);
}

// Resend AJAX
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
            startResendCountdown(data.cooldown || 60);
        } else {
            if (!data.seconds_left) btnResend.disabled = false;
            else startResendCountdown(data.seconds_left);
        }
    })
    .catch(() => {
        btnResend.disabled = false;
        resendText.innerHTML = '<i class="bx bx-refresh me-1"></i>{{ __("Resend Code") }}';
    });
});

document.getElementById('otpForm')?.addEventListener('submit', () => {
    btnVerify.disabled = true;
    document.getElementById('btnText').innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> {{ __("Verifying...") }}';
});
</script>
@endsection
