<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Http\Request;

class CommunicationTemplatesController extends Controller
{
    /**
     * Email Templates Management
     */
    public function emailTemplates()
    {
        $templates = EmailTemplate::all();
        return view('content.apps.communication.email-templates', compact('templates'));
    }

    /**
     * Update Email Template
     */
    public function updateEmailTemplate(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $template->update([
            'subject'   => $request->subject,
            'body'      => $request->body,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Email template '{$template->name}' updated successfully.");
    }

    /**
     * WhatsApp Business API Configuration View
     */
    public function whatsappConfig()
    {
        $settings = app(\App\Services\SettingsService::class);
        $phoneId = $settings->get('whatsapp_phone_number_id', '1098234857239');
        $wabaId = $settings->get('whatsapp_business_account_id', '2948204918239');
        $token = $settings->get('whatsapp_cloud_token', 'EAAB...[Configured]');
        $webhookToken = $settings->get('whatsapp_webhook_verify_token', 'akmart_secure_verify_2026');
        $autoOrderAlerts = $settings->get('whatsapp_auto_order_alerts', '1');

        return view('content.apps.communication.whatsapp-config', compact('phoneId', 'wabaId', 'token', 'webhookToken', 'autoOrderAlerts'));
    }

    /**
     * Save WhatsApp Configuration
     */
    public function updateWhatsappConfig(Request $request)
    {
        $request->validate([
            'whatsapp_phone_number_id'      => 'required|string|max:100',
            'whatsapp_business_account_id'  => 'required|string|max:100',
            'whatsapp_webhook_verify_token' => 'required|string|max:100',
        ]);

        $settings = app(\App\Services\SettingsService::class);
        $settings->set('whatsapp_phone_number_id', $request->whatsapp_phone_number_id);
        $settings->set('whatsapp_business_account_id', $request->whatsapp_business_account_id);
        $settings->set('whatsapp_webhook_verify_token', $request->whatsapp_webhook_verify_token);
        $settings->set('whatsapp_auto_order_alerts', $request->boolean('whatsapp_auto_order_alerts') ? '1' : '0');

        if ($request->filled('whatsapp_cloud_token') && !str_starts_with($request->whatsapp_cloud_token, 'EAAB...')) {
            $settings->set('whatsapp_cloud_token', $request->whatsapp_cloud_token);
        }

        return back()->with('success', 'WhatsApp Business API configuration saved securely.');
    }
}
