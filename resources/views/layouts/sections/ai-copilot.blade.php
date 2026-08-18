@php
    $aiEnabled = \App\Models\StoreSetting::get('ai_enabled', '1') == '1';
    $aiAllPages = \App\Models\StoreSetting::get('ai_all_pages', '1') == '1';
    $assistantName = \App\Models\StoreSetting::get('assistant_name', 'Ak-Mart AI');
    
    // Check user permission
    $user = auth()->user();
    $hasPermission = true;
    if ($user) {
        if (
            $user->is_supreme_admin == 1 ||
            $user->is_super_admin == 1 ||
            $user->user_type === 'super_admin' ||
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('admin')))
        ) {
            $hasPermission = true;
        } elseif (method_exists($user, 'hasAnyPermission') && $user->hasAnyPermission(['access_ai_assistant', 'use_ai_chat'])) {
            $hasPermission = true;
        } elseif ($user->can('access_ai_assistant') || $user->can('use_ai_chat')) {
            $hasPermission = true;
        } else {
            $hasPermission = false;
        }
    }
    
    $isDashboard = request()->is('admin/dashboard') || request()->is('/') || request()->routeIs('app-ecommerce-dashboard');
    $shouldDisplay = $aiEnabled && $hasPermission && ($aiAllPages || $isDashboard);
@endphp

@if($shouldDisplay)
<!-- AI Copilot Floating Launcher Button -->
<button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center position-fixed shadow-lg" id="aiCopilotBtn" style="bottom: 24px; right: 24px; width: 60px; height: 60px; z-index: 1050; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); background: linear-gradient(135deg, #696cff, #875ef5); border: none; box-shadow: 0 8px 24px rgba(105, 108, 255, 0.4) !important;">
    <i class="bx bx-bot text-white fs-2" id="aiCopilotIcon"></i>
</button>

<!-- AI Assistant Copilot Panel -->
<div class="card position-fixed shadow-2xl d-none flex-column overflow-hidden" id="aiCopilotPanel" style="bottom: 96px; right: 24px; width: 400px; height: 580px; z-index: 1050; border-radius: 16px; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border: 1px solid rgba(105, 108, 255, 0.2); background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px);">
    <div class="card-header border-0 d-flex align-items-center justify-content-between p-4" style="background: linear-gradient(135deg, #696cff, #875ef5);">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-sm bg-white rounded-circle d-flex align-items-center justify-content-center p-1">
                <img src="{{ asset('images/brand/ak-mart-cartoon-logo.png') }}" alt="AI Logo" class="w-100 h-100 object-fit-contain">
            </div>
            <div>
                <h6 class="m-0 text-white fw-bold">{{ $assistantName }}</h6>
                <small class="text-white-50 d-flex align-items-center gap-1 fs-tiny">
                    <span class="badge badge-dot bg-success"></span> {{ __('Online Assistant') }}
                </small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" id="closeAiCopilot"></button>
    </div>
    
    <div class="card-body p-4 flex-grow-1 overflow-y-auto d-flex flex-column gap-3" id="aiCopilotChatBody" style="background: rgba(248, 249, 250, 0.5);">
        <!-- Welcome Chat Message -->
        <div class="d-flex align-items-start gap-2 max-w-85">
            <div class="avatar avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;">
                <i class="bx bx-bot"></i>
            </div>
            <div class="p-3 rounded-3 bg-white shadow-sm border border-light text-dark" style="font-size: 0.875rem;">
                {{ __('Hello! I am') }} <strong>{{ $assistantName }}</strong>{{ __(', your advanced eCommerce Business Copilot. How can I help you manage, analyze, and optimize your business today?') }}
            </div>
        </div>
    </div>
    
    <div class="card-footer p-3 bg-white border-top d-flex align-items-center gap-2">
        <textarea class="form-control border-0 p-2 scrollbar-hidden" id="aiCopilotInput" rows="1" placeholder="{{ __('Ask your copilot anything...') }}" style="resize: none; background: #f8f9fa; border-radius: 8px; font-size: 0.9rem;"></textarea>
        <button class="btn btn-icon btn-primary d-flex align-items-center justify-content-center rounded-circle" id="aiCopilotSend" style="width: 40px; height: 40px; background: #696cff; border: none; transition: transform 0.2s;">
            <i class="bx bx-paper-plane fs-5 text-white"></i>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const panel = document.getElementById('aiCopilotPanel');
    const btn = document.getElementById('aiCopilotBtn');
    const closeBtn = document.getElementById('closeAiCopilot');
    const chatBody = document.getElementById('aiCopilotChatBody');
    const input = document.getElementById('aiCopilotInput');
    const sendBtn = document.getElementById('aiCopilotSend');

    if (!btn || !panel) return;

    btn.addEventListener('click', () => {
        panel.classList.toggle('d-none');
        panel.classList.toggle('d-flex');
        if (!panel.classList.contains('d-none')) {
            input.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        panel.classList.add('d-none');
        panel.classList.remove('d-flex');
    });

    function appendMessage(sender, text, isError = false) {
        const msgDiv = document.createElement('div');
        msgDiv.className = sender === 'user' ? 'd-flex align-items-end justify-content-end gap-2 ms-auto max-w-85' : 'd-flex align-items-start gap-2 max-w-85';
        
        const avatarHtml = sender === 'user' 
            ? `<div class="avatar avatar-xs rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;"><i class="bx bx-user"></i></div>`
            : `<div class="avatar avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;"><i class="bx bx-bot"></i></div>`;
            
        const bgClass = sender === 'user' ? 'bg-primary text-white' : (isError ? 'bg-danger-subtle text-danger border border-danger' : 'bg-white shadow-sm border border-light text-dark');
        
        let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');

        msgDiv.innerHTML = sender === 'user' 
            ? `<div class="p-3 rounded-3 ${bgClass}" style="font-size: 0.875rem;">${formattedText}</div>${avatarHtml}`
            : `${avatarHtml}<div class="p-3 rounded-3 ${bgClass}" style="font-size: 0.875rem;">${formattedText}</div>`;
            
        chatBody.appendChild(msgDiv);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    async function handleSend() {
        const query = input.value.trim();
        if (!query) return;

        appendMessage('user', query);
        input.value = '';

        // Show typing indicator
        const typingDiv = document.createElement('div');
        typingDiv.id = 'aiTypingIndicator';
        typingDiv.className = 'd-flex align-items-start gap-2 max-w-85';
        typingDiv.innerHTML = `
            <div class="avatar avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;"><i class="bx bx-bot"></i></div>
            <div class="p-3 rounded-3 bg-white shadow-sm border border-light text-muted" style="font-size: 0.875rem;">
                <span class="spinner-border spinner-border-sm text-primary me-2" role="status"></span> {{ __('Analyzing business metrics...') }}
            </div>
        `;
        chatBody.appendChild(typingDiv);
        chatBody.scrollTop = chatBody.scrollHeight;

        try {
            const response = await fetch('{{ route("app-ai-copilot-chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    prompt: query,
                    locale: '{{ app()->getLocale() }}'
                })
            });

            const data = await response.json();
            const indicator = document.getElementById('aiTypingIndicator');
            if (indicator) indicator.remove();

            if (response.ok && data.response) {
                appendMessage('bot', data.response);
            } else {
                appendMessage('bot', data.message || "Failed to communicate with AI Copilot.", true);
            }
        } catch (err) {
            const indicator = document.getElementById('aiTypingIndicator');
            if (indicator) indicator.remove();
            appendMessage('bot', "Connection Failure: Could not connect to the Copilot service.", true);
        }
    }

    sendBtn.addEventListener('click', handleSend);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    });
});
</script>
@endif
