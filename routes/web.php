<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laravel_example\UserManagement;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\Crm;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\layouts\CollapsedMenu;
use App\Http\Controllers\layouts\ContentNavbar;
use App\Http\Controllers\layouts\ContentNavSidebar;
use App\Http\Controllers\layouts\NavbarFull;
use App\Http\Controllers\layouts\NavbarFullSidebar;
use App\Http\Controllers\layouts\Horizontal;
use App\Http\Controllers\layouts\Vertical;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\front_pages\Landing;
use App\Http\Controllers\front_pages\Pricing;
use App\Http\Controllers\front_pages\Payment;
use App\Http\Controllers\front_pages\Checkout;
use App\Http\Controllers\front_pages\HelpCenter;
use App\Http\Controllers\front_pages\HelpCenterArticle;
use App\Http\Controllers\apps\Email;
use App\Http\Controllers\apps\Chat;
use App\Http\Controllers\apps\Calendar;
use App\Http\Controllers\apps\Kanban;
use App\Http\Controllers\apps\EcommerceDashboard;
use App\Http\Controllers\apps\Logistics\ShippingMethodController;
use App\Http\Controllers\apps\EcommerceProductList;
use App\Http\Controllers\apps\EcommerceProductAdd;
use App\Http\Controllers\apps\EcommerceProductCategory;
use App\Http\Controllers\apps\EcommerceOrderList;
use App\Http\Controllers\apps\EcommerceOrderDetails;
use App\Http\Controllers\apps\EcommerceCustomerAll;
use App\Http\Controllers\apps\EcommerceCustomerDetailsOverview;
use App\Http\Controllers\apps\EcommerceCustomerDetailsSecurity;
use App\Http\Controllers\apps\EcommerceCustomerDetailsBilling;
use App\Http\Controllers\apps\EcommerceCustomerDetailsNotifications;
use App\Http\Controllers\apps\EcommerceManageReviews;
use App\Http\Controllers\apps\EcommerceReferrals;
use App\Http\Controllers\apps\EcommerceBranchManagement;
use App\Http\Controllers\apps\EcommerceSettingsDetails;
use App\Http\Controllers\apps\EcommerceSettingsPayments;
use App\Http\Controllers\apps\EcommerceSettingsCheckout;
use App\Http\Controllers\apps\EcommerceSettingsShipping;
use App\Http\Controllers\apps\EcommerceSettingsLocations;
use App\Http\Controllers\apps\EcommerceSettingsNotifications;
use App\Http\Controllers\apps\EcommerceCouponController;
use App\Http\Controllers\apps\AIGeneratorController;
use App\Http\Controllers\apps\AISettingsController;
use App\Http\Controllers\apps\MapsSettingsController;
use App\Http\Controllers\apps\AcademyDashboard;
use App\Http\Controllers\apps\AcademyCourse;
use App\Http\Controllers\apps\AcademyCourseDetails;
use App\Http\Controllers\apps\LogisticsDashboard;
use App\Http\Controllers\apps\LogisticsFleet;
use App\Http\Controllers\apps\UserList;
use App\Http\Controllers\apps\UserViewAccount;
use App\Http\Controllers\apps\UserViewSecurity;
use App\Http\Controllers\apps\UserViewBilling;
use App\Http\Controllers\apps\UserViewNotifications;
use App\Http\Controllers\apps\UserViewConnections;
use App\Http\Controllers\apps\AccessRoles;
use App\Http\Controllers\apps\SystemNotificationController;
use App\Http\Controllers\pages\UserProfile;
use App\Http\Controllers\pages\UserTeams;
use App\Http\Controllers\pages\UserProjects;
use App\Http\Controllers\pages\UserConnections;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsSecurity;
use App\Http\Controllers\pages\AccountSettingsBilling;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\Faq;
use App\Http\Controllers\pages\Pricing as PagesPricing;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\pages\MiscComingSoon;
use App\Http\Controllers\pages\MiscNotAuthorized;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\LoginCover;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\RegisterCover;
use App\Http\Controllers\authentications\RegisterMultiSteps;
use App\Http\Controllers\authentications\VerifyEmailBasic;
use App\Http\Controllers\authentications\VerifyEmailCover;
use App\Http\Controllers\authentications\ResetPasswordBasic;
use App\Http\Controllers\authentications\ResetPasswordCover;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\ForgotPasswordCover;
use App\Http\Controllers\authentications\TwoStepsBasic;
use App\Http\Controllers\authentications\TwoStepsCover;
use App\Http\Controllers\wizard_example\Checkout as WizardCheckout;
use App\Http\Controllers\wizard_example\PropertyListing;
use App\Http\Controllers\wizard_example\CreateDeal;
use App\Http\Controllers\modal\ModalExample;

// Public Routes
Route::get('/', function () {
    // Simple health check endpoint
    return response()->json(['message' => 'OK'], 200);
});
Route::get('/dashboard', [EcommerceDashboard::class, 'index'])->name('dashboard');
Route::get('/front-pages/landing', [Landing::class, 'index'])->name('front-pages-landing');
Route::get('/front-pages/pricing', [Pricing::class, 'index'])->name('front-pages-pricing');
Route::get('/front-pages/payment', [Payment::class, 'index'])->name('front-pages-payment');
Route::get('/front-pages/checkout', [Checkout::class, 'index'])->name('front-pages-checkout');
Route::get('/front-pages/help-center', [HelpCenter::class, 'index'])->name('front-pages-help-center');
Route::get('/front-pages/help-center-article', [HelpCenterArticle::class, 'index'])->name('front-pages-help-center-article');
Route::get('/sitemap.xml', [\App\Http\Controllers\apps\SaaS\SeoController::class, 'sitemap'])->name('app-saas-sitemap');

// Authenticated Routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Route::get('/', [EcommerceDashboard::class, 'index'])->name('dashboard-analytics'); // removed duplicate root route
    Route::get('/dashboard/analytics', [EcommerceDashboard::class, 'index'])->name('dashboard-analytics');
    Route::get('/dashboard/crm', [Crm::class, 'index'])->name('dashboard-crm');

    // Language & Branch
    Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
    Route::get('/branch/{id}', [\App\Http\Controllers\BranchController::class, 'swap'])->name('branch-swap')->middleware('branch.access');

    // SaaS Management (Super Admin)
    Route::prefix('app/saas')->group(function () {
        Route::get('/billing', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'index'])->name('app-saas-billing');
        Route::post('/billing/subscribe', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'subscribe'])->name('app-saas-subscribe');
        Route::post('/billing/cancel', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'cancel'])->name('app-saas-cancel');
        
        Route::get('/commission', [\App\Http\Controllers\apps\SaaS\CommissionController::class, 'index'])->name('app-saas-commission');
        Route::post('/commission', [\App\Http\Controllers\apps\SaaS\CommissionController::class, 'store'])->name('app-saas-commission-store');
        Route::put('/commission/{commissionRule}', [\App\Http\Controllers\apps\SaaS\CommissionController::class, 'update'])->name('app-saas-commission-update');
        Route::delete('/commission/{commissionRule}', [\App\Http\Controllers\apps\SaaS\CommissionController::class, 'destroy'])->name('app-saas-commission-delete');
        Route::post('/commission/tier', [\App\Http\Controllers\apps\SaaS\CommissionController::class, 'storeTier'])->name('app-saas-commission-tier-store');
        Route::delete('/commission/tier/{tier}', [\App\Http\Controllers\apps\SaaS\CommissionController::class, 'deleteTier'])->name('app-saas-commission-tier-delete');

        Route::get('/analytics', [\App\Http\Controllers\apps\SaaS\PlatformAnalyticsController::class, 'index'])->name('app-saas-analytics');
        Route::get('/audit-logs', [\App\Http\Controllers\apps\SaaS\AuditLogController::class, 'index'])->name('app-saas-audit-logs');
        Route::get('/currencies', [\App\Http\Controllers\apps\SaaS\CurrencyController::class, 'index'])->name('app-saas-currencies');
        Route::post('/currencies', [\App\Http\Controllers\apps\SaaS\CurrencyController::class, 'store'])->name('app-saas-currencies-store');
        Route::post('/currencies/{currency}/toggle', [\App\Http\Controllers\apps\SaaS\CurrencyController::class, 'toggle'])->name('app-saas-currencies-toggle');
        Route::get('/languages', [\App\Http\Controllers\apps\SaaS\LanguageController::class, 'index'])->name('app-saas-languages');
        Route::post('/languages', [\App\Http\Controllers\apps\SaaS\LanguageController::class, 'store'])->name('app-saas-languages-store');
        Route::post('/languages/{language}/toggle', [\App\Http\Controllers\apps\SaaS\LanguageController::class, 'toggle'])->name('app-saas-languages-toggle');
        Route::get('/seo', [\App\Http\Controllers\apps\SaaS\SeoController::class, 'index'])->name('app-saas-seo');
        Route::get('/kyc', [\App\Http\Controllers\apps\SaaS\KycAdminController::class, 'index'])->name('app-saas-kyc-admin');
        Route::post('/kyc/{vendorKyc}/approve', [\App\Http\Controllers\apps\SaaS\KycAdminController::class, 'approve'])->name('app-saas-kyc-approve');
        Route::get('/dunning', [\App\Http\Controllers\apps\SaaS\DunningController::class, 'index'])->name('app-saas-dunning');
        Route::post('/app/saas/dunning/trigger', [\App\Http\Controllers\apps\SaaS\DunningController::class, 'trigger'])->name('app-saas-dunning-trigger');
    });

    // Vendor Management
    Route::prefix('app/vendor')->group(function () {
        Route::get('/kyc', [\App\Http\Controllers\apps\Vendor\KycController::class, 'index'])->name('app-vendor-kyc');
        Route::post('/kyc', [\App\Http\Controllers\apps\Vendor\KycController::class, 'store'])->name('app-vendor-kyc-store');
        Route::get('/inventory', [\App\Http\Controllers\apps\Vendor\InventoryController::class, 'index'])->name('app-vendor-inventory');
        Route::post('/inventory/update', [\App\Http\Controllers\apps\Vendor\InventoryController::class, 'updateStock'])->name('app-vendor-inventory-update');
        Route::get('/returns', [\App\Http\Controllers\apps\Vendor\ReturnRequestController::class, 'index'])->name('app-vendor-returns');
        Route::get('/pos', [\App\Http\Controllers\apps\Vendor\PosController::class, 'index'])->name('app-vendor-pos');
        Route::post('/pos/checkout', [\App\Http\Controllers\apps\Vendor\PosController::class, 'checkout'])->name('app-vendor-pos-checkout');
        Route::get('/wallet', [\App\Http\Controllers\apps\Vendor\WalletController::class, 'index'])->name('app-vendor-wallet');
        Route::get('/store-builder', [\App\Http\Controllers\apps\Vendor\StoreBuilderController::class, 'index'])->name('app-vendor-store-builder');
        Route::post('/store-builder', [\App\Http\Controllers\apps\Vendor\StoreBuilderController::class, 'store'])->name('app-vendor-store-builder-save');
        Route::get('/support', [\App\Http\Controllers\apps\Vendor\SupportTicketController::class, 'index'])->name('app-vendor-support');
        Route::post('/wallet/payout', [\App\Http\Controllers\apps\Vendor\WalletController::class, 'requestPayout'])->name('app-vendor-wallet-payout');
        Route::get('/payment-settings', [\App\Http\Controllers\apps\Vendor\PaymentSettingsController::class, 'index'])->name('app-vendor-payment-settings');
        Route::post('/payment-settings', [\App\Http\Controllers\apps\Vendor\PaymentSettingsController::class, 'store'])->name('app-vendor-payment-settings-save');
    });

    // Logistics
    Route::get('/app/logistics/shipping', [\App\Http\Controllers\apps\Logistics\ShippingMethodController::class, 'index'])->name('app-logistics-shipping');
        Route::post('/app/logistics/shipping', [\App\Http\Controllers\apps\Logistics\ShippingMethodController::class, 'store'])->name('app-logistics-shipping-store');
    Route::get('/laravel/user-management', [\App\Http\Controllers\laravel_example\UserManagement::class, 'UserManagement'])->name('laravel-user-management');
// DataTables routes for Laravel User Management
Route::get('/user-list', [\App\Http\Controllers\laravel_example\UserManagement::class, 'index'])->name('user-list');
Route::post('/user-list', [\App\Http\Controllers\laravel_example\UserManagement::class, 'store'])->name('user-list-store');
Route::get('/user-list/{id}/edit', [\App\Http\Controllers\laravel_example\UserManagement::class, 'edit'])->name('user-list-edit');
Route::put('/user-list/{id}', [\App\Http\Controllers\laravel_example\UserManagement::class, 'update'])->name('user-list-update');
Route::delete('/user-list/{id}', [\App\Http\Controllers\laravel_example\UserManagement::class, 'destroy'])->name('user-list-destroy');

// Core E-commerce Apps
    Route::middleware(['tenant.subscription'])->group(function () {
        Route::get('/app/ecommerce/dashboard', [EcommerceDashboard::class, 'index'])->name('app-ecommerce-dashboard');
        
        // Products
        Route::get('/app/ecommerce/product/list', [EcommerceProductList::class, 'index'])->name('app-ecommerce-product-list');
        Route::get('/app/ecommerce/product/add', [EcommerceProductAdd::class, 'index'])->name('app-ecommerce-product-add');
        Route::post('/app/ecommerce/product/add', [EcommerceProductAdd::class, 'store'])->name('app-ecommerce-product-add-post');
        Route::get('/app/ecommerce/product/edit/{id}', [EcommerceProductAdd::class, 'edit'])->name('app-ecommerce-product-edit');
        Route::put('/app/ecommerce/product/edit/{id}', [EcommerceProductAdd::class, 'update'])->name('app-ecommerce-product-update');
        Route::delete('/app/ecommerce/product/{id}', [EcommerceProductList::class, 'destroy'])->name('app-ecommerce-product-delete');
        Route::get('/app/ecommerce/product/category', [EcommerceProductCategory::class, 'index'])->name('app-ecommerce-product-category');
        Route::post('/app/ecommerce/product/category', [EcommerceProductCategory::class, 'store'])->name('app-ecommerce-category-add');
        Route::put('/app/ecommerce/product/category/{id}', [EcommerceProductCategory::class, 'update'])->name('app-ecommerce-category-update');
        Route::delete('/app/ecommerce/product/category/{id}', [EcommerceProductCategory::class, 'destroy'])->name('app-ecommerce-category-delete');

        // Orders
        Route::get('/app/ecommerce/order/list', [EcommerceOrderList::class, 'index'])->name('app-ecommerce-order-list');
        Route::delete('/app/ecommerce/order/{id}', [EcommerceOrderList::class, 'destroy'])->name('app-ecommerce-order-delete');
        Route::patch('/app/ecommerce/order/{id}/status', [EcommerceOrderList::class, 'updateStatus'])->name('app-ecommerce-order-status-update');
        Route::get('/app/ecommerce/order/details/{id?}', [EcommerceOrderDetails::class, 'index'])->name('app-ecommerce-order-details');

        // Management
        Route::get('/app/ecommerce/manage/reviews', [EcommerceManageReviews::class, 'index'])->name('app-ecommerce-manage-reviews');
        Route::get('/app/ecommerce/referrals', [EcommerceReferrals::class, 'index'])->name('app-ecommerce-referrals');
        
        // Branch Management
        Route::get('/app/ecommerce/branch/list', [EcommerceBranchManagement::class, 'index'])->name('app-ecommerce-branch-list');
        Route::post('/app/ecommerce/branch', [EcommerceBranchManagement::class, 'store'])->name('app-ecommerce-branch-store');
        Route::get('/app/ecommerce/branch/{id}/edit', [EcommerceBranchManagement::class, 'edit'])->name('app-ecommerce-branch-edit');
        Route::put('/app/ecommerce/branch/{id}', [EcommerceBranchManagement::class, 'update'])->name('app-ecommerce-branch-update');
        Route::delete('/app/ecommerce/branch/{id}', [EcommerceBranchManagement::class, 'destroy'])->name('app-ecommerce-branch-delete');

        // Customers
        Route::get('/app/ecommerce/customer/all', [EcommerceCustomerAll::class, 'index'])->name('app-ecommerce-customer-all');
        Route::post('/app/ecommerce/customer', [EcommerceCustomerAll::class, 'store'])->name('app-ecommerce-customer-store');
        Route::put('/app/ecommerce/customer/{id}', [EcommerceCustomerAll::class, 'update'])->name('app-ecommerce-customer-update');
        Route::delete('/app/ecommerce/customer/{id}', [EcommerceCustomerAll::class, 'destroy'])->name('app-ecommerce-customer-delete');
        Route::get('/app/ecommerce/customer/details/overview/{id?}', [EcommerceCustomerDetailsOverview::class, 'index'])->name('app-ecommerce-customer-details-overview');
        
        // Coupons
        Route::get('/app/ecommerce/coupons', [EcommerceCouponController::class, 'index'])->name('app-ecommerce-coupon-list');
        Route::post('/app/ecommerce/coupons', [EcommerceCouponController::class, 'store'])->name('app-ecommerce-coupon-store');
        Route::get('/app/ecommerce/coupons/{id}/edit', [EcommerceCouponController::class, 'edit'])->name('app-ecommerce-coupon-edit');
        Route::put('/app/ecommerce/coupons/{id}', [EcommerceCouponController::class, 'update'])->name('app-ecommerce-coupon-update');
        Route::delete('/app/ecommerce/coupons/{id}', [EcommerceCouponController::class, 'destroy'])->name('app-ecommerce-coupon-delete');
        Route::post('/app/ecommerce/coupon/bulk-generate', [EcommerceCouponController::class, 'bulkGenerate'])->name('app-ecommerce-coupon-bulk');
        
        // Settings
        Route::get('/app/ecommerce/settings/details', [EcommerceSettingsDetails::class, 'index'])->name('app-ecommerce-settings-details');
Route::post('/app/ecommerce/settings/details/save', [EcommerceSettingsDetails::class, 'store'])->name('app-ecommerce-settings-details-save');
        Route::get('/app/ecommerce/settings/payments', [EcommerceSettingsPayments::class, 'index'])->name('app-ecommerce-settings-payments');
        Route::get('/app/ecommerce/settings/checkout', [EcommerceSettingsCheckout::class, 'index'])->name('app-ecommerce-settings-checkout');
        Route::get('/app/ecommerce/settings/shipping', [EcommerceSettingsShipping::class, 'index'])->name('app-ecommerce-settings-shipping');
        Route::post('/app/ecommerce/settings/shipping/save', [EcommerceSettingsShipping::class, 'store'])->name('app-ecommerce-settings-shipping-save');
        Route::get('/app/ecommerce/settings/notifications', [EcommerceSettingsNotifications::class, 'index'])->name('app-ecommerce-settings-notifications');
        Route::post('/app/ecommerce/settings/notifications/save', [EcommerceSettingsNotifications::class, 'store'])->name('app-ecommerce-settings-notifications-save');
        Route::post('/app/ecommerce/settings/payments/save', [EcommerceSettingsPayments::class, 'store'])->name('app-ecommerce-settings-payments-save');

        // Invoices
        Route::get('/app/invoice/list', 'App\Http\Controllers\apps\InvoiceList@index')->name('app-invoice-list');
        Route::get('/app/invoice/preview/{id?}', 'App\Http\Controllers\apps\InvoicePreview@index')->name('app-invoice-preview');
        Route::get('/app/invoice/print/{id?}', 'App\Http\Controllers\apps\InvoicePrint@index')->name('app-invoice-print');
        Route::get('/app/invoice/add', 'App\Http\Controllers\apps\InvoiceAdd@index')->name('app-invoice-add');
        Route::get('/app/invoice/edit', 'App\Http\Controllers\apps\InvoiceEdit@index')->name('app-invoice-edit');

        // User View
        Route::get('/app/user/view/account', [UserViewAccount::class, 'index'])->name('app-user-view-account');

        // AI Copilot
        Route::post('/app/ai/copilot', [\App\Http\Controllers\apps\AICopilotController::class, 'chat'])->name('app-ai-copilot-chat');
    });

        // AI Settings
        Route::get('/app/ecommerce/settings/ai', [AISettingsController::class, 'index'])->name('app-ecommerce-settings-ai');
        Route::post('/app/ecommerce/settings/ai/save', [AISettingsController::class, 'store'])->name('app-ecommerce-settings-ai-save');
        // Maps Settings
        Route::get('/app/ecommerce/settings/maps', [MapsSettingsController::class, 'index'])->name('app-ecommerce-settings-maps');
        Route::post('/app/ecommerce/settings/maps/save', [MapsSettingsController::class, 'store'])->name('app-ecommerce-settings-maps-save');
        Route::get('/app/notifications', [\App\Http\Controllers\apps\SystemNotificationController::class, 'index'])->name('app-notifications');
    Route::post('/app/notifications/mark-all', [\App\Http\Controllers\apps\SystemNotificationController::class, 'markAllAsRead'])->name('app-notifications-mark-all');

    // Access Hub & Roles Management
    Route::get('/app/access-roles', [\App\Http\Controllers\apps\RoleController::class, 'index'])->name('app-access-roles');
Route::get('/app/access-hub', [\App\Http\Controllers\apps\RoleController::class, 'index'])->name('app-access-hub');
    Route::post('/app/access-hub/roles', [\App\Http\Controllers\apps\RoleController::class, 'store'])->name('app-access-roles-store');
    Route::delete('/app/access-hub/roles/{id}', [\App\Http\Controllers\apps\RoleController::class, 'destroy'])->name('app-access-roles-destroy');
    Route::post('/app/access-hub/roles/sync-permissions', [\App\Http\Controllers\apps\RoleController::class, 'syncPermissions'])->name('app-access-roles-sync-permissions');
    Route::post('/app/access-hub/users/sync-roles', [\App\Http\Controllers\apps\RoleController::class, 'syncUserRoles'])->name('app-access-roles-sync-user-roles');
    Route::get('/app/access-permission/list', [\App\Http\Controllers\apps\AccessPermission::class, 'list'])->name('app-access-permission-list');
    Route::post('/app/access-permission', [\App\Http\Controllers\apps\AccessPermission::class, 'store'])->name('app-access-permission-store');
    Route::delete('/app/access-permission/{id}/delete', [\App\Http\Controllers\apps\AccessPermission::class, 'destroy'])->name('app-access-permission-destroy');
    Route::get('/app/access-permission', [\App\Http\Controllers\apps\AccessPermission::class, 'index'])->name('app-access-permission');
    Route::get('/app/access-hub/users/{id}/roles', [\App\Http\Controllers\apps\RoleController::class, 'getUserRoles']);
});

// Authentication Routes (Public)
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
