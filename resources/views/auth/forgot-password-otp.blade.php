@php
    $pageConfigs = ['myLayout' => 'blank'];
    $step = $step ?? 'request';
    $channel = $channel ?? 'email';
@endphp

@extends('layouts/blankLayout')

@section('title',
    $step === 'request' ? __('Forgot Password') :
    ($step === 'verify' ? __('Verify Reset Code') : __('Set New Password'))
    . ' — AK-Mart'
)

@section('page-style')
<style>
    :root { --primary: #696cff; }
    body { background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .auth-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 48px rgba(105,108,255,0.12);
        padding: 48px 44px;
        width: 100%;
        max-width: 460px;
        margin: 24px auto;
    }
    .brand { text-align: center; margin-bottom: 28px; }
    .brand h1 {
        font-size: 26px; font-weight: 800;
        background: linear-gradient(135deg, #696cff, #9155fd);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .brand p { color: #888; font-size: 13px; margin-top: 4px; }

    .step-icon { width: 72px; height: 72px; background: linear-gradient(135deg, #696cff20, #9155fd20); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 32px; }
    .auth-title { font-size: 22px; font-weight: 700; color: #1a1a2e; text-align: center; margin-bottom: 6px; }
    .auth-subtitle { color: #666; font-size: 13.5px; text-align: center; margin-bottom: 22px; line-height: 1.5; }

    .channel-nav {
        display: flex;
        background: #f1f5f9;
        border-radius: 12px;
        padding: 4px;
        gap: 4px;
        margin-bottom: 20px;
    }
    .channel-btn {
        flex: 1;
        border: none;
        background: transparent;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .channel-btn.active {
        background: #fff;
        color: var(--primary);
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }

    .form-group { margin-bottom: 18px; }
    .form-label { font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 6px; }
    .form-control {
        width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px;
        font-size: 14.5px; outline: none; transition: all 0.2s; background: #fafafa; color: #333;
    }
    .form-control:focus { border-color: var(--primary); background: #f0efff; box-shadow: 0 0 0 4px rgba(105,108,255,0.1); }
    .form-control.is-invalid { border-color: #e74c3c; }

    .btn-primary-full {
        width: 100%; padding: 13px; background: linear-gradient(135deg, #696cff, #9155fd);
        color: #fff; border: none; border-radius: 12px; font-size: 15px; font-weight: 600;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-primary-full:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(105,108,255,0.4); }
    .btn-primary-full:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .otp-inputs { display: flex; gap: 10px; justify-content: center; margin-bottom: 8px; }
    .otp-input {
        width: 50px; height: 58px; text-align: center; font-size: 24px; font-weight: 700;
        border: 2px solid #e0e0e0; border-radius: 12px; outline: none; transition: all 0.2s;
        color: #333; background: #fafafa; font-family: 'Courier New', monospace;
    }
    .otp-input:focus { border-color: var(--primary); background: #f0efff; box-shadow: 0 0 0 4px rgba(105,108,255,0.12); transform: translateY(-2px); }
    .otp-input.filled { border-color: var(--primary); background: #f0efff; color: var(--primary); }

    .timer-section { text-align: center; margin: 12px 0; font-size: 13px; color: #888; }
    .timer-badge { display: inline-block; background: #f0efff; color: var(--primary); border-radius: 20px; padding: 4px 14px; font-size: 14px; font-weight: 600; }
    .timer-badge.urgent { background: #fff0f0; color: #e74c3c; }

    .alert-error { background: #fff5f5; border: 1px solid #fecaca; border-radius: 10px; padding: 10px 14px; color: #b91c1c; font-size: 13px; margin-bottom: 16px; text-align: center; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 10px 14px; color: #166534; font-size: 13px; margin-bottom: 16px; text-align: center; }

    .progress-steps { display: flex; justify-content: center; gap: 8px; margin-bottom: 24px; }
    .step-dot { width: 8px; height: 8px; border-radius: 50%; background: #ddd; transition: all 0.3s; }
    .step-dot.active { background: var(--primary); width: 24px; border-radius: 4px; }
    .step-dot.done { background: #22c55e; }

    .back-link { text-align: center; margin-top: 16px; }
    .back-link a { color: #888; font-size: 13px; text-decoration: none; }
    .back-link a:hover { color: var(--primary); }

    .resend-section { text-align: center; margin-top: 16px; }
    .btn-resend { background: none; border: none; color: var(--primary); font-size: 13.5px; font-weight: 600; cursor: pointer; padding: 6px 12px; border-radius: 8px; transition: all 0.2s; }
    .btn-resend:hover { background: #f0efff; }
    .btn-resend:disabled { color: #aaa; cursor: not-allowed; }

    .password-strength { height: 4px; border-radius: 2px; background: #eee; margin-top: 8px; overflow: hidden; }
    .password-strength-bar { height: 100%; width: 0; transition: all 0.3s; border-radius: 2px; }

    @media (max-width: 480px) {
        .auth-card { padding: 32px 20px; margin: 12px; }
        .otp-input { width: 44px; height: 52px; font-size: 20px; }
        .otp-inputs { gap: 8px; }
    }
</style>
@endsection

@section('content')
<div class="auth-card">
    {{-- Brand --}}
    <div class="brand">
        <h1>🛒 AK-Mart</h1>
        <p>{{ __('Account Recovery') }}</p>
    </div>

    {{-- Progress dots --}}
    <div class="progress-steps">
        <div class="step-dot {{ $step === 'request' ? 'active' : 'done' }}"></div>
        <div class="step-dot {{ $step === 'verify' ? 'active' : ($step === 'reset' ? 'done' : '') }}"></div>
        <div class="step-dot {{ $step === 'reset' ? 'active' : '' }}"></div>
    </div>

    {{-- Alerts --}}
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
    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- ========================================================= --}}
    {{-- STEP 1: Choose Option (Email vs Mobile Number)              --}}
    {{-- ========================================================= --}}
    @if ($step === 'request')
        <div class="step-icon" id="headerIcon">📧</div>
        <h2 class="auth-title">{{ __('Forgot Password?') }}</h2>
        <p class="auth-subtitle" id="headerSubtitle">{{ __("Select how you'd like to receive your 6-digit reset code.") }}</p>

        <!-- 2 Clear Options: Email or Mobile -->
        <div class="channel-nav">
            <button type="button" class="channel-btn {{ $channel === 'email' ? 'active' : '' }}" id="btnChanEmail" onclick="setChannel('email')">
                <span>📧 {{ __('Reset via Email') }}</span>
            </button>
            <button type="button" class="channel-btn {{ $channel === 'phone' ? 'active' : '' }}" id="btnChanPhone" onclick="setChannel('phone')">
                <span>📱 {{ __('Reset via Mobile') }}</span>
            </button>
        </div>

        <form action="{{ route('auth.forgot-password-otp.send') }}" method="POST">
            @csrf
            <input type="hidden" name="channel" id="channelInput" value="{{ $channel }}">

            <!-- Email Input Field -->
            <div class="form-group" id="emailFieldGroup" style="{{ $channel === 'phone' ? 'display:none;' : '' }}">
                <label class="form-label" for="email">{{ __('Email Address') }}</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="admin@ak-mart.com"
                    value="{{ old('email') }}"
                    {{ $channel === 'email' ? 'required autofocus' : '' }}
                >
            </div>

            <!-- Mobile Phone Input Field -->
            <div class="form-group" id="phoneFieldGroup" style="{{ $channel === 'email' ? 'display:none;' : '' }}">
                <label class="form-label" for="phone">{{ __('Registered Mobile Number') }}</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    placeholder="e.g. +91 9876543210"
                    value="{{ old('phone') }}"
                    {{ $channel === 'phone' ? 'required autofocus' : '' }}
                >
            </div>

            <button type="submit" class="btn-primary-full" id="btnSubmitReset">
                {{ $channel === 'phone' ? __('Send Code to Mobile') : __('Send Code to Email') }}
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('auth-login-basic') }}">← {{ __('Back to Login') }}</a>
        </div>

    {{-- ========================================================= --}}
    {{-- STEP 2: Verify OTP                                        --}}
    {{-- ========================================================= --}}
    @elseif ($step === 'verify')
        <div class="step-icon">🔐</div>
        <h2 class="auth-title">{{ __('Enter Reset Code') }}</h2>
        <p class="auth-subtitle">
            {{ __('We sent a 6-digit code to') }}<br>
            <strong>{{ $identifier ?? '' }}</strong>
        </p>

        <form id="resetOtpForm" action="{{ route('auth.forgot-password-otp.verify') }}" method="POST">
            @csrf
            <input type="hidden" name="otp" id="resetOtpFull">

            <div class="otp-inputs" id="resetOtpInputs">
                @for ($i = 0; $i < config('otp.length', 6); $i++)
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]"
                        maxlength="1"
                        class="otp-input @error('otp') error @enderror"
                        id="rotp{{ $i }}"
                        aria-label="{{ __('Digit') }} {{ $i + 1 }}"
                    >
                @endfor
            </div>

            @if($expiresAt ?? false)
            <div class="timer-section">
                {{ __('Expires in') }} <span class="timer-badge" id="resetTimer">--:--</span>
            </div>
            @endif

            <button type="submit" class="btn-primary-full" id="btnResetVerify" disabled>
                {{ __('Verify Reset Code') }}
            </button>
        </form>

        <div class="resend-section">
            <p style="font-size:13px; color:#888; margin-bottom:6px;">{{ __("Didn't receive it?") }}</p>
            <button class="btn-resend" id="btnPwResend" @if(!($canResend ?? true)) disabled @endif>
                @if(($secondsLeft ?? 0) > 0)
                    {{ __('Resend in') }} <span id="pwResendCountdown">{{ $secondsLeft ?? 0 }}</span>s
                @else
                    {{ __('Resend Code') }}
                @endif
            </button>
        </div>

        <div class="back-link">
            <a href="{{ route('auth.forgot-password-otp.request') }}">← {{ __('Change Reset Option') }}</a>
        </div>

    {{-- ========================================================= --}}
    {{-- STEP 3: New Password                                       --}}
    {{-- ========================================================= --}}
    @elseif ($step === 'reset')
        <div class="step-icon">🔑</div>
        <h2 class="auth-title">{{ __('Set New Password') }}</h2>
        <p class="auth-subtitle">{{ __('Choose a strong password for your account.') }}</p>

        <form action="{{ route('auth.password-reset-otp.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="password">{{ __('New Password') }}</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ __('Minimum 8 characters') }}"
                    required
                    autofocus
                    oninput="updateStrength(this.value)"
                >
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="{{ __('Repeat your password') }}"
                    required
                >
            </div>
            <button type="submit" class="btn-primary-full">{{ __('Reset Password & Sign In') }}</button>
        </form>
    @endif
</div>
@endsection

@section('page-script')
<script>
function setChannel(channel) {
    const btnEmail = document.getElementById('btnChanEmail');
    const btnPhone = document.getElementById('btnChanPhone');
    const emailGroup = document.getElementById('emailFieldGroup');
    const phoneGroup = document.getElementById('phoneFieldGroup');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const channelInput = document.getElementById('channelInput');
    const btnSubmit = document.getElementById('btnSubmitReset');
    const headerIcon = document.getElementById('headerIcon');

    if (channel === 'phone') {
        btnPhone.classList.add('active');
        btnEmail.classList.remove('active');
        emailGroup.style.display = 'none';
        phoneGroup.style.display = 'block';
        emailInput.removeAttribute('required');
        phoneInput.setAttribute('required', 'required');
        channelInput.value = 'phone';
        btnSubmit.textContent = @json(__('Send Code to Mobile'));
        if (headerIcon) headerIcon.textContent = '📱';
        phoneInput.focus();
    } else {
        btnEmail.classList.add('active');
        btnPhone.classList.remove('active');
        emailGroup.style.display = 'block';
        phoneGroup.style.display = 'none';
        phoneInput.removeAttribute('required');
        emailInput.setAttribute('required', 'required');
        channelInput.value = 'email';
        btnSubmit.textContent = @json(__('Send Code to Email'));
        if (headerIcon) headerIcon.textContent = '📧';
        emailInput.focus();
    }
}

(function() {
    const OTP_LENGTH = {{ config('otp.length', 6) }};

    // -----------------------------------------------------------------
    // OTP Inputs (step 2)
    // -----------------------------------------------------------------
    @if ($step === 'verify')
    const rotpInputs = Array.from({ length: OTP_LENGTH }, (_, i) => document.getElementById('rotp' + i));
    const rotpHidden = document.getElementById('resetOtpFull');
    const btnVerify  = document.getElementById('btnResetVerify');

    function getRotp() { return rotpInputs.map(i => i.value).join(''); }

    rotpInputs.forEach((input, idx) => {
        input.addEventListener('input', e => {
            const val = e.target.value.replace(/\D/g, '');
            if (val.length > 1) {
                val.split('').forEach((d, i) => { if (rotpInputs[idx + i]) rotpInputs[idx + i].value = d; });
                rotpInputs[Math.min(idx + val.length, OTP_LENGTH - 1)]?.focus();
            } else {
                input.value = val;
                if (val && rotpInputs[idx + 1]) rotpInputs[idx + 1].focus();
            }
            const full = getRotp();
            rotpHidden.value = full;
            btnVerify.disabled = full.length < OTP_LENGTH;
            rotpInputs.forEach(i => i.classList.toggle('filled', i.value.length === 1));
        });
        input.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !input.value && rotpInputs[idx - 1]) {
                rotpInputs[idx - 1].value = ''; rotpInputs[idx - 1].focus();
            }
        });
        input.addEventListener('paste', e => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            paste.split('').forEach((d, i) => { if (rotpInputs[i]) rotpInputs[i].value = d; });
            rotpInputs[Math.min(paste.length - 1, OTP_LENGTH - 1)]?.focus();
            rotpHidden.value = getRotp();
            btnVerify.disabled = getRotp().length < OTP_LENGTH;
        });
    });
    rotpInputs[0]?.focus();

    // Timer
    @if($expiresAt ?? false)
    const pwExpiresAt = new Date("{{ $expiresAt->toISOString() }}").getTime();
    const timerEl = document.getElementById('resetTimer');
    const timerTick = setInterval(() => {
        const diff = Math.max(0, Math.floor((pwExpiresAt - Date.now()) / 1000));
        const mm = String(Math.floor(diff / 60)).padStart(2, '0');
        const ss = String(diff % 60).padStart(2, '0');
        if (timerEl) { timerEl.textContent = mm + ':' + ss; timerEl.classList.toggle('urgent', diff <= 60); }
        if (diff <= 0) clearInterval(timerTick);
    }, 1000);
    @endif

    // Resend
    let pwResendCooldown = {{ $secondsLeft ?? 0 }};
    const btnPwResend = document.getElementById('btnPwResend');
    const pwResendText = document.getElementById('pwResendCountdown');

    if (pwResendCooldown > 0) {
        const tick = setInterval(() => {
            pwResendCooldown--;
            if (pwResendText) pwResendText.textContent = pwResendCooldown;
            if (pwResendCooldown <= 0) {
                clearInterval(tick);
                btnPwResend.innerHTML = '{{ __("Resend Code") }}';
                btnPwResend.disabled = false;
            }
        }, 1000);
    }

    btnPwResend?.addEventListener('click', () => {
        btnPwResend.disabled = true;
        fetch('{{ route("auth.forgot-password-otp.resend") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                pwResendCooldown = data.cooldown || 60;
                btnPwResend.innerHTML = '{{ __("Resend in") }} <span>' + pwResendCooldown + '</span>s';
                const t = setInterval(() => {
                    pwResendCooldown--;
                    btnPwResend.innerHTML = '{{ __("Resend in") }} <span>' + pwResendCooldown + '</span>s';
                    if (pwResendCooldown <= 0) { clearInterval(t); btnPwResend.innerHTML = '{{ __("Resend Code") }}'; btnPwResend.disabled = false; }
                }, 1000);
            } else {
                alert(data.message || '{{ __("Failed to resend.") }}');
                if (!data.seconds_left) btnPwResend.disabled = false;
            }
        })
        .catch(() => { btnPwResend.disabled = false; btnPwResend.innerHTML = '{{ __("Resend Code") }}'; });
    });
    @endif

    // -----------------------------------------------------------------
    // Password Strength (step 3)
    // -----------------------------------------------------------------
    @if ($step === 'reset')
    window.updateStrength = function(val) {
        const bar = document.getElementById('strengthBar');
        if (!bar) return;
        let score = 0;
        if (val.length >= 8) score++;
        if (val.length >= 12) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const pct = (score / 5) * 100;
        const colors = ['#e74c3c', '#f39c12', '#f39c12', '#2ecc71', '#27ae60'];
        bar.style.width = pct + '%';
        bar.style.background = colors[score - 1] || '#eee';
    };
    @endif
})();
</script>
@endsection
