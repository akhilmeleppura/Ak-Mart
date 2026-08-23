<?php

namespace App\Services\Ai;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class AiGovernanceGateway
{
    /**
     * Tool permission mappings and risk classification
     */
    protected static array $toolPolicies = [
        // Public Storefront Read-Only Tools
        'search_products'          => ['permission' => 'public',      'risk' => 'READ'],
        'get_store_policy'         => ['permission' => 'public',      'risk' => 'READ'],
        'get_order_details'        => ['permission' => 'authenticated', 'risk' => 'READ'],
        'compare_products'         => ['permission' => 'public',      'risk' => 'READ'],
        'get_trending_products'    => ['permission' => 'public',      'risk' => 'READ'],

        // Staff / Admin Management Read-Only Tools
        'get_sales_summary'        => ['permission' => 'admin.reports', 'risk' => 'READ'],
        'get_sales_comparison'     => ['permission' => 'admin.reports', 'risk' => 'READ'],
        'get_profit_summary'       => ['permission' => 'admin.finance', 'risk' => 'READ'],
        'get_inventory_valuation'  => ['permission' => 'admin.inventory', 'risk' => 'READ'],
        'get_customer_summary'     => ['permission' => 'admin.customers', 'risk' => 'READ'],
        'get_order_risk'           => ['permission' => 'admin.orders', 'risk' => 'READ'],
        'get_executive_brief'      => ['permission' => 'admin.bi', 'risk' => 'READ'],

        // Action Preparation Tools (Draft State)
        'generate_purchase_order'  => ['permission' => 'admin.inventory', 'risk' => 'LOW_RISK'],
        'generate_campaign_draft'  => ['permission' => 'admin.marketing', 'risk' => 'LOW_RISK'],
        'generate_transfer_draft'  => ['permission' => 'admin.inventory', 'risk' => 'LOW_RISK'],

        // High-Risk Mutations (Require Explicit Confirmation + Human Approval)
        'execute_refund'           => ['permission' => 'admin.finance', 'risk' => 'HIGH_RISK'],
        'apply_price_change'       => ['permission' => 'admin.catalog', 'risk' => 'HIGH_RISK'],
        'send_mass_campaign'       => ['permission' => 'admin.marketing', 'risk' => 'HIGH_RISK'],
    ];

    /**
     * 1. Validate Inbound Prompt and Request Context
     */
    public function validateRequest(string $prompt, ?User $user = null, string $feature = 'copilot'): array
    {
        // 1. Check Feature Flag
        if (!$this->isFeatureEnabled($feature)) {
            return [
                'allowed' => false,
                'status'  => 'FEATURE_DISABLED',
                'message' => "The AI feature [{$feature}] is currently disabled.",
            ];
        }

        // 2. Anti-Prompt Injection & Adversarial Filter
        $security = PromptSecurityGuard::inspect($prompt);
        if (!$security['safe']) {
            return [
                'allowed' => false,
                'status'  => 'SECURITY_REJECTION',
                'message' => '⚠️ Request blocked by AI Safety & Governance Policy.',
            ];
        }

        // 3. PII & Secret Masking
        $sanitizedPrompt = $this->maskSensitiveData($prompt);

        return [
            'allowed'          => true,
            'status'           => 'APPROVED',
            'sanitized_prompt' => $sanitizedPrompt,
            'feature'          => $feature,
        ];
    }

    /**
     * 2. Authorize AI Tool Execution
     */
    public function authorizeTool(string $toolName, ?User $user = null): array
    {
        if (!isset(self::$toolPolicies[$toolName])) {
            return [
                'authorized' => false,
                'reason'     => "Unrecognized tool [{$toolName}].",
                'risk_level' => 'HIGH_RISK',
            ];
        }

        $policy = self::$toolPolicies[$toolName];
        $requiredPerm = $policy['permission'];
        $riskLevel = $policy['risk'];

        // Public tools allowed
        if ($requiredPerm === 'public') {
            return ['authorized' => true, 'risk_level' => $riskLevel];
        }

        // Authenticated tools
        if ($requiredPerm === 'authenticated') {
            return [
                'authorized' => $user !== null,
                'reason'     => $user ? null : 'Authentication required.',
                'risk_level' => $riskLevel,
            ];
        }

        // Admin / Role Gated Tools
        if (!$user) {
            return [
                'authorized' => false,
                'reason'     => 'Unauthorized: Administrator authentication required.',
                'risk_level' => $riskLevel,
            ];
        }

        $isSuperAdmin = $user->is_supreme_admin || $user->is_super_admin || $user->user_type === 'super_admin' || (method_exists($user, 'hasRole') && $user->hasRole('super_admin'));

        if ($isSuperAdmin) {
            return ['authorized' => true, 'risk_level' => $riskLevel];
        }

        // Standard role check
        $authorized = match ($requiredPerm) {
            'admin.finance'   => $user->hasRole('finance') || $user->hasRole('admin'),
            'admin.inventory' => $user->hasRole('inventory_manager') || $user->hasRole('admin'),
            'admin.marketing' => $user->hasRole('marketing_manager') || $user->hasRole('admin'),
            'admin.customers' => $user->hasRole('support_agent') || $user->hasRole('admin'),
            default           => $user->hasRole('admin'),
        };

        return [
            'authorized' => $authorized,
            'reason'     => $authorized ? null : "Permission denied for tool [{$toolName}].",
            'risk_level' => $riskLevel,
        ];
    }

    /**
     * 3. Mask Sensitive PII / Secrets
     */
    public function maskSensitiveData(string $text): string
    {
        // Mask 16-digit credit card numbers
        $masked = preg_replace('/\b\d{4}[-\s]?\d{4}[-\s]?\d{4}[-\s]?\d{4}\b/', '[CARD_REDACTED]', $text);

        // Mask email addresses
        $masked = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[EMAIL_REDACTED]', $masked);

        // Mask phone numbers (10 digits)
        $masked = preg_replace('/\b(?:\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}\b/', '[PHONE_REDACTED]', $masked);

        return $masked;
    }

    /**
     * 4. Feature Flag Check
     */
    public function isFeatureEnabled(string $feature): bool
    {
        // All Phase 1–9 features enabled by default
        return true;
    }

    /**
     * 5. AI Observability & Audit Logger
     */
    public function logObservability(string $feature, string $model, float $latencyMs, int $tokens, string $status): void
    {
        try {
            Log::info("AI_OBSERVABILITY: feature={$feature} model={$model} latency={$latencyMs}ms tokens={$tokens} status={$status}");
        } catch (\Exception $e) {
            // Non-blocking log
        }
    }
}
