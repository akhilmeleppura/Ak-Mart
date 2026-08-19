<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch\Branch;
use App\Models\CommunicationLog;
use App\Models\CommunicationTemplate;
use App\Models\Role;
use App\Models\StoreSetting;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WebhookSubscription;
use App\Models\WorkflowRule;
use App\Services\EmailReminderService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsHubController extends Controller
{
    protected SettingsService $settings;
    protected EmailReminderService $reminderService;

    public function __construct(SettingsService $settings, EmailReminderService $reminderService)
    {
        $this->settings = $settings;
        $this->reminderService = $reminderService;
    }

    /**
     * Dispatcher to load the appropriate settings section view.
     */
    public function showSection(Request $request, string $section = 'store')
    {
        $allSettings = $this->settings->all();
        $branches = Branch::all();
        $warehouses = Warehouse::all();
        $roles = class_exists(Role::class) ? Role::all() : [];

        // Additional section-specific data
        $templates = [];
        $communicationLogs = [];
        $workflows = [];
        $auditLogs = [];
        $webhooks = [];

        if (in_array($section, ['email-templates', 'email', 'whatsapp'])) {
            $templates = CommunicationTemplate::all();
        }

        if (in_array($section, ['whatsapp', 'email', 'notifications'])) {
            $communicationLogs = CommunicationLog::latest()->take(20)->get();
        }

        if ($section === 'automation') {
            $workflows = WorkflowRule::latest()->get();
        }

        if ($section === 'audit-logs' || $section === 'security') {
            $auditLogs = AuditLog::latest()->take(30)->get();
        }

        if ($section === 'api-webhooks') {
            $webhooks = WebhookSubscription::all();
        }

        $viewName = "content.apps.settings.{$section}";
        if (!view()->exists($viewName)) {
            // Fallback for legacy view mappings
            $legacyMap = [
                'store'         => 'content.apps.app-ecommerce-settings-details',
                'payments'      => 'content.apps.app-ecommerce-settings-payments',
                'checkout'      => 'content.apps.app-ecommerce-settings-checkout',
                'shipping'      => 'content.apps.app-ecommerce-settings-shipping',
                'locations'     => 'content.apps.app-ecommerce-settings-locations',
                'notifications' => 'content.apps.app-ecommerce-settings-notifications',
                'ai'            => 'content.apps.app-ai-settings',
                'branding'      => 'content.apps.app-ecommerce-settings-branding',
                'maps'          => 'content.apps.app-maps-settings',
            ];
            $viewName = $legacyMap[$section] ?? 'content.apps.settings.store';
        }

        return view($viewName, [
            'settings'          => $allSettings,
            'branches'          => $branches,
            'warehouses'        => $warehouses,
            'roles'             => $roles,
            'templates'         => $templates,
            'communicationLogs' => $communicationLogs,
            'workflows'         => $workflows,
            'auditLogs'         => $auditLogs,
            'webhooks'          => $webhooks,
            'currentSection'    => $section,
        ]);
    }

    /**
     * Central save handler for all settings sections.
     */
    public function saveSection(Request $request, string $section)
    {
        $user = $request->user();
        $input = $request->except(['_token', '_method']);

        // Handle File uploads if present
        $fileFields = ['site_logo_file', 'site_logo_dark_file', 'site_favicon_file', 'invoice_logo_file'];
        foreach ($fileFields as $fKey) {
            if ($request->hasFile($fKey) && $request->file($fKey)->isValid()) {
                $file = $request->file($fKey);
                $cleanKey = str_replace('_file', '', $fKey);
                $filename = $cleanKey . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/branding'), $filename);
                $this->settings->set($cleanKey, 'uploads/branding/' . $filename);
                unset($input[$fKey]);
            }
        }

        // Sensitive fields to encrypt
        $encryptedFields = [
            'smtp_password',
            'stripe_secret',
            'paypal_secret',
            'phonepe_salt_key',
            'whatsapp_access_token',
            'gemini_api_key',
            'webhook_secret',
        ];

        foreach ($input as $key => $value) {
            if (in_array($key, $encryptedFields)) {
                if (!empty($value)) {
                    $this->settings->setEncrypted($key, $value);
                }
            } else {
                $this->settings->set($key, is_array($value) ? json_encode($value) : $value);
            }
        }

        // Audit Log
        try {
            AuditLog::create([
                'user_id'        => $user ? $user->id : null,
                'event'          => "settings_updated: {$section}",
                'url'            => $request->fullUrl(),
                'ip_address'     => $request->ip(),
                'user_agent'     => $request->userAgent(),
                'new_values'     => json_encode(['section' => $section, 'fields' => array_keys($input)]),
            ]);
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __(':section settings saved successfully!', ['section' => ucfirst(str_replace('-', ' ', $section))]),
            ]);
        }

        return redirect()->back()->with('success', __(':section settings saved successfully!', ['section' => ucfirst(str_replace('-', ' ', $section))]));
    }

    /**
     * Interactive SMTP connection test.
     */
    public function testSmtp(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $config = [
            'smtp_host'         => $request->input('smtp_host', $this->settings->get('smtp_host')),
            'smtp_port'         => $request->input('smtp_port', $this->settings->get('smtp_port', 587)),
            'smtp_username'     => $request->input('smtp_username', $this->settings->get('smtp_username')),
            'smtp_password'     => $request->filled('smtp_password') ? $request->input('smtp_password') : $this->settings->getEncrypted('smtp_password'),
            'smtp_encryption'   => $request->input('smtp_encryption', $this->settings->get('smtp_encryption', 'tls')),
            'mail_from_address' => $request->input('mail_from_address', $this->settings->get('mail_from_address', 'noreply@ak-mart.com')),
            'mail_from_name'    => $request->input('mail_from_name', $this->settings->get('mail_from_name', 'AK-Mart')),
        ];

        $res = $this->settings->testSmtp($config, $request->test_email);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($res);
        }

        return redirect()->back()->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Interactive WhatsApp dispatch test.
     */
    public function testWhatsApp(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string',
            'test_message' => 'nullable|string',
        ]);

        $res = $this->settings->testWhatsApp(
            $request->all(),
            $request->test_phone,
            $request->test_message ?? 'AK-Mart WhatsApp Test Message'
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($res);
        }

        return redirect()->back()->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Save communication template.
     */
    public function saveTemplate(Request $request)
    {
        $request->validate([
            'code'    => 'required|string|max:100',
            'name'    => 'required|string|max:255',
            'channel' => 'required|in:email,whatsapp,sms,in_app',
            'body'    => 'required|string',
        ]);

        CommunicationTemplate::updateOrCreate(
            ['code' => $request->code, 'channel' => $request->channel],
            [
                'name'     => $request->name,
                'subject'  => $request->subject ?? $request->name,
                'category' => $request->category ?? 'transactional',
                'body'     => $request->body,
                'is_active'=> $request->boolean('is_active', true),
            ]
        );

        return redirect()->back()->with('success', __('Template saved successfully!'));
    }

    /**
     * Clear system and settings cache.
     */
    public function clearCache(Request $request)
    {
        $this->settings->clearCache();
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => __('All system caches cleared successfully!')]);
        }

        return redirect()->back()->with('success', __('All system caches cleared successfully!'));
    }
}
