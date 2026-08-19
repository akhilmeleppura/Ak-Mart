<?php

namespace Database\Seeders;

use App\Models\CommunicationLog;
use App\Models\CommunicationTemplate;
use App\Models\StoreSetting;
use App\Models\WorkflowRule;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Core Default Store Settings
        $defaultSettings = [
            // Store Details
            'store_name'                            => 'AK-Mart Store',
            'business_name'                         => 'AK-Mart Global Enterprises LLC',
            'store_email'                           => 'support@ak-mart.com',
            'sender_email'                          => 'help@ak-mart.com',
            'store_phone'                           => '+1 (555) 019-2834',
            'whatsapp_number'                       => '+15550192834',
            'tax_number'                            => 'GSTIN32AAAAA0000A1Z5',
            'address'                               => '742 Broadway Ave, Suite 400',
            'city'                                  => 'New York',
            'state'                                 => 'NY',
            'pincode'                               => '10003',
            'country'                               => 'United States',
            'timezone'                              => 'America/New_York',
            'date_format'                           => 'M d, Y',
            'weight_unit'                           => 'kg',
            'footer_text'                           => '© 2026 AK-Mart. Enterprise E-Commerce & Mini-Mart Management Platform. All rights reserved.',
            'default_branch'                        => '1',

            // General Operations
            'maintenance_mode'                      => '0',
            'maintenance_notice'                    => 'Our store is undergoing scheduled upgrades. We will be back online shortly.',
            'allow_customer_registration'           => '1',
            'allow_guest_checkout'                  => '1',
            'enable_product_reviews'                => '1',
            'enable_wishlist'                       => '1',
            'enable_coupons'                        => '1',
            'enable_returns'                        => '1',

            // E-Commerce & Catalog
            'catalog_products_per_page'             => '12',
            'catalog_default_sort'                  => 'newest',
            'cart_min_order_amount'                 => '0.00',
            'cart_max_quantity_per_item'            => '50',
            'cart_expiration_hours'                 => '72',
            'show_stock_quantity'                   => '1',
            'show_sku_barcode'                      => '1',
            'reviews_require_approval'              => '1',

            // Products & Tools
            'auto_generate_sku'                     => '1',
            'sku_prefix'                            => 'AKM-',
            'auto_generate_barcode'                 => '1',
            'barcode_format'                        => 'EAN-13',
            'product_max_images'                    => '10',
            'product_max_image_size_mb'             => '5',
            'enable_ai_product_generation'          => '1',
            'enable_url_product_import'             => '1',

            // Inventory
            'inventory_tracking_enabled'            => '1',
            'allow_negative_stock'                  => '0',
            'restock_on_cancel'                     => '1',
            'inventory_low_stock_threshold'         => '5',
            'inventory_critical_stock_threshold'    => '2',
            'inventory_reservation_timeout_minutes' => '15',
            'inventory_deduct_stage'                => 'order_placed',

            // Orders
            'order_prefix'                          => 'ORD-',
            'order_number_format'                   => 'prefix_sequential',
            'auto_generate_invoice'                 => '1',
            'auto_email_order_confirmation'         => '1',
            'auto_whatsapp_order_confirmation'      => '1',
            'order_cancellation_window_hours'       => '2',
            'order_return_window_days'              => '14',

            // Checkout
            'shipping_phone_requirement'            => 'required',
            'company_name_requirement'              => 'optional',
            'address_line_2_requirement'            => 'optional',
            'customer_order_notes'                  => 'enabled',
            'require_terms_acceptance'              => '1',
            'terms_and_conditions'                  => 'By placing an order on AK-Mart, you agree to our standard terms of purchase, warranty coverage, and shipment conditions.',
            'refund_policy'                         => 'Items can be returned within 14 days of delivery in original condition with tags intact.',

            // Customers & Loyalty
            'customer_require_email_verification'   => '0',
            'customer_require_admin_approval'       => '0',
            'loyalty_program_enabled'               => '1',
            'loyalty_points_per_dollar'             => '1',
            'loyalty_point_redemption_value'        => '5.00',
            'loyalty_min_points_to_redeem'          => '100',

            // Pricing & Tax
            'currency'                              => 'USD',
            'currency_symbol'                       => '$',
            'currency_position'                     => 'left',
            'currency_decimals'                     => '2',
            'tax_inclusive_pricing'                 => 'inclusive',
            'default_tax_rate'                      => '18.00',
            'default_hsn_code'                      => '84713010',
            'enable_gst_split'                      => '1',

            // Payments
            'payment_cod_enabled'                   => '1',
            'payment_cod_max_limit'                 => '500',
            'payment_stripe_enabled'                => '1',
            'stripe_mode'                           => 'test',
            'stripe_key'                            => 'pk_test_sample_key_12345',
            'payment_paypal_enabled'                => '1',
            'paypal_mode'                           => 'sandbox',
            'payment_phonepe_enabled'               => '1',
            'phonepe_merchant_id'                   => 'M2200TEST',

            // Shipping
            'shipping_enabled'                      => '1',
            'shipping_flat_rate'                    => '5.00',
            'shipping_free_threshold'               => '50.00',
            'shipping_estimated_days'               => '2-4 Business Days',
            'shipping_express_enabled'              => '1',
            'shipping_express_fee'                  => '12.00',
            'shipping_express_days'                 => 'Same Day / Next Day',

            // Locations
            'order_routing_strategy'                => 'stock_availability',

            // Email / SMTP
            'mail_mailer'                           => 'smtp',
            'smtp_host'                             => 'smtp.mailtrap.io',
            'smtp_port'                             => '587',
            'smtp_encryption'                       => 'tls',
            'smtp_username'                         => 'akmart_mailer',
            'mail_from_address'                     => 'noreply@ak-mart.com',
            'mail_from_name'                        => 'AK-Mart Store',
            'mail_reply_to'                         => 'support@ak-mart.com',

            // Reminders
            'reminder_unpaid_order_enabled'         => '1',
            'reminder_unpaid_order_delay_minutes'   => '30',
            'reminder_unpaid_order_cooldown_hours'  => '24',
            'reminder_unpaid_order_max_attempts'    => '3',
            'reminder_abandoned_cart_enabled'       => '1',
            'reminder_abandoned_cart_delay_hours'   => '2',
            'reminder_abandoned_cart_coupon'        => 'COMEBACK5',
            'reminder_low_stock_enabled'            => '1',

            // WhatsApp
            'whatsapp_provider'                     => 'meta',
            'whatsapp_business_account_id'          => '1092837465',
            'whatsapp_phone_number_id'              => '1039485726',
            'whatsapp_verify_token'                 => 'akmart_meta_webhook_secret',
            'whatsapp_notify_order_created'         => '1',
            'whatsapp_notify_order_shipped'         => '1',
            'whatsapp_notify_order_delivered'       => '1',

            // Notifications
            'notify_order_placed_app'               => '1',
            'notify_order_placed_email'             => '1',
            'notify_order_placed_whatsapp'          => '1',
            'notify_order_paid_app'                 => '1',
            'notify_order_paid_email'               => '1',
            'notify_order_paid_whatsapp'            => '1',
            'notify_order_shipped_app'              => '1',
            'notify_order_shipped_email'            => '1',
            'notify_order_shipped_whatsapp'         => '1',
            'notify_order_delivered_app'            => '1',
            'notify_order_delivered_email'          => '1',
            'notify_order_delivered_whatsapp'       => '1',
            'notify_inventory_low_app'              => '1',
            'notify_inventory_low_email'            => '1',
            'notify_inventory_low_whatsapp'         => '1',

            // Automations
            'auto_workflow_order_placed'            => '1',
            'auto_workflow_low_stock'               => '1',
            'auto_workflow_payment_failed'          => '1',

            // Security
            'security_min_password_length'          => '8',
            'security_password_expiration_days'     => '0',
            'security_max_login_attempts'           => '5',
            'security_lockout_duration_minutes'     => '15',
            'security_session_timeout_minutes'      => '120',

            // Roles
            'default_staff_role'                    => 'Cashier',
            'default_customer_group'                => 'Retail',

            // API
            'api_enabled'                           => '1',

            // Integrations
            'google_maps_api_key'                   => 'AIzaSySampleKeyGoogleMapsPlatform',
            'default_lat'                           => '40.7128',
            'default_lng'                           => '-74.0060',
            'default_zoom'                          => '12',
            'filesystem_disk'                       => 'public',

            // AI
            'ai_enabled'                            => '1',
            'ai_provider'                           => 'gemini',
            'gemini_model'                          => 'gemini-2.5-flash',
            'ai_temperature'                        => '0.7',
            'ai_max_tokens'                         => '2048',

            // SEO
            'seo_meta_title'                        => 'AK-Mart — Premium Online Mini-Mart & E-Commerce Superstore',
            'seo_meta_description'                  => 'Shop thousands of premium groceries, daily essentials, electronics, and home supplies with lightning-fast delivery from AK-Mart.',
            'seo_meta_keywords'                     => 'ecommerce, mini-mart, grocery, online shopping, fast delivery, retail store',
            'seo_google_analytics_id'               => 'G-AKMART9999',

            // Localization
            'default_locale'                        => 'en',
            'first_day_of_week'                     => 'monday',

            // Branding
            'brand_primary_color'                   => '#696cff',
            'brand_secondary_color'                 => '#00d25b',
        ];

        foreach ($defaultSettings as $key => $val) {
            StoreSetting::set($key, $val, 1);
        }

        // 2. Default Communication Templates
        $templates = [
            [
                'code'      => 'welcome_email',
                'name'      => 'Customer Welcome Email',
                'channel'   => 'email',
                'category'  => 'transactional',
                'subject'   => 'Welcome to {{store_name}}!',
                'body'      => "Hi {{customer_name}},\n\nWelcome to {{store_name}}! Your customer account is now active. Start exploring our premium catalog today.",
                'is_active' => true,
            ],
            [
                'code'      => 'order_confirmation',
                'name'      => 'Order Placed Confirmation',
                'channel'   => 'email',
                'category'  => 'transactional',
                'subject'   => 'Order Confirmed: #{{order_number}}',
                'body'      => "Hi {{customer_name}},\n\nThank you for shopping at {{store_name}}! We have received your order #{{order_number}} for {{order_total}}. We are preparing it for dispatch.",
                'is_active' => true,
            ],
            [
                'code'      => 'order_shipped',
                'name'      => 'Order Dispatched & Tracking',
                'channel'   => 'email',
                'category'  => 'transactional',
                'subject'   => 'Your Order #{{order_number}} Has Shipped!',
                'body'      => "Hi {{customer_name}},\n\nYour order #{{order_number}} is on the way! Courier tracking: {{tracking_number}}.",
                'is_active' => true,
            ],
            [
                'code'      => 'order_payment_reminder',
                'name'      => 'Pending Payment Reminder',
                'channel'   => 'email',
                'category'  => 'reminder',
                'subject'   => 'Action Required: Complete Payment for Order #{{order_number}}',
                'body'      => "Hi {{customer_name}},\n\nYour order #{{order_number}} for {{order_total}} is pending payment. Click here to complete your checkout.",
                'is_active' => true,
            ],
            [
                'code'      => 'low_stock_alert',
                'name'      => 'Admin Low Stock Warning',
                'channel'   => 'email',
                'category'  => 'alert',
                'subject'   => 'Low Stock Warning: {{product_name}}',
                'body'      => "Attention: Product {{product_name}} has reached low stock threshold. Please initiate warehouse reorder.",
                'is_active' => true,
            ],
        ];

        foreach ($templates as $tpl) {
            CommunicationTemplate::updateOrCreate(['code' => $tpl['code']], $tpl);
        }

        // 3. Seed Sample Communication Logs
        CommunicationLog::firstOrCreate(
            ['recipient' => '+15550192834', 'template_code' => 'order_confirmation'],
            [
                'channel'      => 'whatsapp',
                'subject'      => 'Order Confirmed',
                'message_body' => 'Your order #ORD-1001 has been confirmed on AK-Mart!',
                'status'       => 'delivered',
                'provider'     => 'meta_cloud',
                'created_at'   => now()->subMinutes(15),
            ]
        );

        CommunicationLog::firstOrCreate(
            ['recipient' => 'customer@ak-mart.com', 'template_code' => 'welcome_email'],
            [
                'channel'      => 'email',
                'subject'      => 'Welcome to AK-Mart!',
                'message_body' => 'Welcome to AK-Mart! Your account is active.',
                'status'       => 'sent',
                'provider'     => 'smtp',
                'created_at'   => now()->subHours(1),
            ]
        );
    }
}
