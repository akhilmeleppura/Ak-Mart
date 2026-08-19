# AK-Mart Advanced Store Settings & E-Commerce Management Center Matrix

| Section | Setting | Database Key | Route | Permission | Functional | EN | ML | HI | AR | FR | DE | Test Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Store Details** | Store Public Name | `store_name` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Store Details** | Legal Business Name | `business_name` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Store Details** | Store Public Email | `store_email` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Store Details** | Support Sender Email | `sender_email` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Store Details** | Support Phone | `store_phone` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Store Details** | WhatsApp Support Number | `whatsapp_number` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Store Details** | Tax / VAT / GST Number | `tax_number` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Store Details** | Address & City & PIN | `address`, `city`, `pincode` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Store Details** | Default Active Branch | `default_branch` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Store Details** | Timezone & Date Format | `timezone`, `date_format` | `/settings/store` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **General** | Maintenance Mode | `maintenance_mode` | `/settings/general` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **General** | Customer Self-Registration | `allow_customer_registration` | `/settings/general` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **General** | Allow Guest Checkout | `allow_guest_checkout` | `/settings/general` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **General** | Enable Reviews & Ratings | `enable_product_reviews` | `/settings/general` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **General** | Enable Customer Wishlist | `enable_wishlist` | `/settings/general` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **General** | Enable Coupons & Promos | `enable_coupons` | `/settings/general` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **General** | Enable Returns & Refunds | `enable_returns` | `/settings/general` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **E-Commerce** | Products Per Page | `catalog_products_per_page` | `/settings/ecommerce` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **E-Commerce** | Default Sorting | `catalog_default_sort` | `/settings/ecommerce` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **E-Commerce** | Minimum Order Amount | `cart_min_order_amount` | `/settings/ecommerce` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **E-Commerce** | Max Quantity Per Cart Item | `cart_max_quantity_per_item` | `/settings/ecommerce` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **E-Commerce** | Show Stock Quantity | `show_stock_quantity` | `/settings/ecommerce` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **E-Commerce** | Review Moderation Requirement | `reviews_require_approval` | `/settings/ecommerce` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Products** | Auto-Generate SKU | `auto_generate_sku`, `sku_prefix` | `/settings/products` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Products** | Auto-Generate Barcodes | `auto_generate_barcode`, `barcode_format` | `/settings/products` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Products** | Max Upload Size & Images | `product_max_images`, `product_max_image_size_mb` | `/settings/products` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Products** | AI Product Description Gen | `enable_ai_product_generation` | `/settings/products` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Products** | Universal URL Importer | `enable_url_product_import` | `/settings/products` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Inventory** | Stock Tracking Enabled | `inventory_tracking_enabled` | `/settings/inventory` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Inventory** | Allow Negative Stock | `allow_negative_stock` | `/settings/inventory` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Inventory** | Low Stock Warning Threshold | `inventory_low_stock_threshold` | `/settings/inventory` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Inventory** | Critical Stock Threshold | `inventory_critical_stock_threshold` | `/settings/inventory` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Inventory** | Stock Reservation Timeout | `inventory_reservation_timeout_minutes` | `/settings/inventory` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Inventory** | Stock Deduction Timing Stage | `inventory_deduct_stage` | `/settings/inventory` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Orders** | Order Number Prefix & Format | `order_prefix`, `order_number_format` | `/settings/orders` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Orders** | Auto PDF Invoice Creation | `auto_generate_invoice` | `/settings/orders` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Orders** | Auto Confirmation Dispatch | `auto_email_order_confirmation` | `/settings/orders` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Orders** | Order Cancellation Window | `order_cancellation_window_hours` | `/settings/orders` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Orders** | Return Window (Days) | `order_return_window_days` | `/settings/orders` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Checkout** | Phone Requirement | `shipping_phone_requirement` | `/settings/checkout` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Checkout** | Company Name Requirement | `company_name_requirement` | `/settings/checkout` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Checkout** | Terms Acceptance Requirement | `require_terms_acceptance` | `/settings/checkout` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Checkout** | Legal Policy Texts | `terms_and_conditions`, `refund_policy` | `/settings/checkout` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Customers** | Email Verification | `customer_require_email_verification` | `/settings/customers` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Customers** | Loyalty Program Enabled | `loyalty_program_enabled` | `/settings/customers` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Customers** | Loyalty Points Rate & Value | `loyalty_points_per_dollar`, `loyalty_point_redemption_value` | `/settings/customers` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Pricing** | Currency & Position | `currency`, `currency_symbol`, `currency_position` | `/settings/pricing` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Pricing** | Tax Inclusive / Exclusive | `tax_inclusive_pricing` | `/settings/pricing` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Pricing** | Default Tax Rate & GST Split | `default_tax_rate`, `enable_gst_split` | `/settings/pricing` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Payments** | COD Enabled & Limit | `payment_cod_enabled`, `payment_cod_max_limit` | `/settings/payments` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Payments** | Stripe Card Processing | `payment_stripe_enabled`, `stripe_key`, `stripe_secret` | `/settings/payments` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Payments** | PayPal Express | `payment_paypal_enabled`, `paypal_client_id`, `paypal_secret` | `/settings/payments` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Payments** | PhonePe / UPI | `payment_phonepe_enabled`, `phonepe_merchant_id`, `phonepe_salt_key` | `/settings/payments` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Shipping** | Shipping Enabled | `shipping_enabled` | `/settings/shipping` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Shipping** | Flat Rate & Free Threshold | `shipping_flat_rate`, `shipping_free_threshold` | `/settings/shipping` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Shipping** | Express Shipping Surcharge | `shipping_express_enabled`, `shipping_express_fee` | `/settings/shipping` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Locations** | Default Branch & Warehouse | `default_branch`, `default_warehouse` | `/settings/locations` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Locations** | Order Routing Strategy | `order_routing_strategy` | `/settings/locations` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Email** | Mail Driver & SMTP Host | `mail_mailer`, `smtp_host`, `smtp_port` | `/settings/email` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Email** | SMTP Credentials (Encrypted) | `smtp_username`, `smtp_password` | `/settings/email` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Email** | Test SMTP Handshake | `POST /settings-action/email/test-smtp` | `/settings/email` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Email Templates** | Transactional Templates | `communication_templates` DB | `/settings/email-templates` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Email Reminders** | Unpaid Order Reminders | `reminder_unpaid_order_enabled`, `reminder_unpaid_order_max_attempts` | `/settings/email-reminders` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Email Reminders** | Abandoned Cart Reminders | `reminder_abandoned_cart_enabled`, `reminder_abandoned_cart_coupon` | `/settings/email-reminders` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Email Reminders** | Low Stock Admin Reminders | `reminder_low_stock_enabled` | `/settings/email-reminders` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **WhatsApp** | Cloud API Credentials | `whatsapp_business_account_id`, `whatsapp_phone_number_id`, `whatsapp_access_token` | `/settings/whatsapp` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **WhatsApp** | Automated Event Triggers | `whatsapp_notify_order_created`, `whatsapp_notify_order_shipped` | `/settings/whatsapp` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **WhatsApp** | Test Message Dispatch | `POST /settings-action/whatsapp/test` | `/settings/whatsapp` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Notifications** | Omnichannel Matrix | `notify_{event}_{channel}` | `/settings/notifications` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Automation** | Event-Driven Workflow Rules | `auto_workflow_order_placed`, `auto_workflow_low_stock` | `/settings/automation` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Security** | Password Strength & Expiry | `security_min_password_length`, `security_password_expiration_days` | `/settings/security` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Security** | Brute Force Lockout | `security_max_login_attempts`, `security_lockout_duration_minutes` | `/settings/security` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Security** | Session Timeout | `security_session_timeout_minutes` | `/settings/security` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Users & Roles** | Default Staff & Customer Role | `default_staff_role`, `default_customer_group` | `/settings/users-roles` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **API** | REST API Control & Keys | `api_enabled`, `api_primary_key`, `webhook_secret` | `/settings/api-webhooks` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Integrations** | Google Maps Platform | `google_maps_api_key`, `default_lat`, `default_lng` | `/settings/integrations` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **AI** | Gemini Model & Temperature | `ai_enabled`, `gemini_model`, `ai_temperature`, `gemini_api_key` | `/settings/ai` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **SEO** | Global Store Meta & Pixels | `seo_meta_title`, `seo_meta_description`, `seo_google_analytics_id` | `/settings/seo` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Localization** | Default Locale & RTL Support | `default_locale`, `first_day_of_week` | `/settings/localization` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Branding** | Logos, Favicon & Color Theme | `site_logo`, `site_logo_dark`, `site_favicon`, `brand_primary_color` | `/settings/branding` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Backup** | System Cache Purge & Rebuild | `POST /settings-action/cache/clear` | `/settings/backup` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
| **Audit Logs** | Settings Change Audit Trail | `audit_logs` DB table | `/settings/audit-logs` | `manage_settings` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Passed |
