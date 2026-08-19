# AK-Mart Route Architecture & Endpoint Audit

## 1. Route Map Overview

All endpoints in AK-Mart follow strict RESTful conventions, authenticated middleware wrapping, and modular organization across `routes/web.php` and `routes/api.php`.

| Route URI | HTTP Method | Route Name | Controller Action | Middleware | Description |
| :--- | :---: | :--- | :--- | :--- | :--- |
| `/` / `/dashboard` | `GET` | `app-ecommerce-dashboard` | `EcommerceDashboard@index` | `auth` | Main E-Commerce analytics dashboard |
| `/profile` | `GET` | `pages-profile-user` | `ProfileController@index` | `auth` | User account profile overview |
| `/account/settings` | `GET` | `pages-account-settings-account` | `ProfileController@edit` | `auth` | Profile editing & security management |
| `/account/settings/profile` | `POST` | `account-settings-profile-update` | `ProfileController@update` | `auth` | Save profile names & contact details |
| `/account/settings/photo` | `POST` | `account-settings-photo-update` | `ProfileController@updatePhoto` | `auth` | AJAX avatar photo upload |
| `/account/settings/photo-remove` | `POST` | `account-settings-photo-remove` | `ProfileController@removePhoto` | `auth` | AJAX avatar photo removal |
| `/billing` | `GET` | `pages-account-settings-billing` | `SubscriptionController@index` | `auth` | Multi-tenant SaaS subscription hub |
| `/invoices/{id}/preview` | `GET` | `saas.invoices.preview` | `SubscriptionController@previewInvoice` | `auth` | Printable invoice viewer |
| `/pos/register` | `GET` | `pos.register` | `PosRegisterController@index` | `auth` | Cashier point of sale interface |
| `/app/ecommerce/product/list` | `GET` | `app-ecommerce-product-list` | `EcommerceProductList@index` | `auth` | Product catalog management table |
| `/app/ecommerce/product/import-url` | `GET` | `product.import.url` | `ProductImportController@index` | `auth` | Smart product URL extractor |
| `/app/ecommerce/inventory` | `GET` | `app-ecommerce-inventory` | `EcommerceProductList@inventory` | `auth` | Inventory stock control & adjustments |
| `/app/ecommerce/order/list` | `GET` | `app-ecommerce-order-list` | `EcommerceOrderList@index` | `auth` | Order fulfillment & status tracking |
| `/vendor/return-requests` | `GET` | `vendor.returns.index` | `ReturnRequestController@index` | `auth` | Customer return & refund processing |
| `/settings/{section}` | `GET` | `settings.section` | `SettingsHubController@showSection` | `auth` | Modular 30-section settings hub |
| `/settings/{section}/save` | `POST` | `settings.section.save` | `SettingsHubController@saveSection` | `auth` | Universal settings persistence |
| `/settings-action/email/test-smtp` | `POST` | `settings.email.test-smtp` | `SettingsHubController@testSmtp` | `auth` | Dynamic SMTP handshake verification |
| `/settings-action/whatsapp/test` | `POST` | `settings.whatsapp.test` | `SettingsHubController@testWhatsApp` | `auth` | WhatsApp Meta Cloud API message test |
| `/settings-action/cache/clear` | `POST` | `settings.cache.clear` | `SettingsHubController@clearCache` | `auth` | Global settings & route cache flush |
| `/api/global-search` | `GET` | `api.global.search` | `GlobalSearchController@search` | `auth` | Instant CTRL+K search index |
| `/api/ai/copilot-chat` | `POST` | `api.ai.copilot.chat` | `AICopilotController@chat` | `auth` | Multilingual Gemini Copilot assistant |
