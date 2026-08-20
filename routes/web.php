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
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('auth-login-basic');
})->name('root');

Route::get('/login', [LoginBasic::class, 'index'])->name('login');
Route::post('/login', [LoginBasic::class, 'store'])->name('login.store');

Route::get('/dashboard', [EcommerceDashboard::class, 'index'])->name('dashboard');

// -----------------------------------------------------------------------
// Customer Storefront & Catalog Routes (Public)
// -----------------------------------------------------------------------
Route::prefix('store')->name('storefront.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Storefront\StorefrontController::class, 'index'])->name('home');
    Route::get('/shop', [\App\Http\Controllers\Storefront\StorefrontController::class, 'shop'])->name('shop');
    Route::get('/search-suggestions', [\App\Http\Controllers\Storefront\StorefrontController::class, 'searchSuggestions'])->name('search.suggestions');
    Route::get('/search_suggestions', [\App\Http\Controllers\Storefront\StorefrontController::class, 'searchSuggestions'])->name('search_suggestions');
    Route::get('/product/{id}', [\App\Http\Controllers\Storefront\StorefrontController::class, 'product'])->name('product');
    Route::get('/cart', [\App\Http\Controllers\Storefront\StorefrontController::class, 'cart'])->name('cart');
    Route::post('/cart/add', [\App\Http\Controllers\Storefront\StorefrontController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [\App\Http\Controllers\Storefront\StorefrontController::class, 'updateCart'])->name('cart.update');
    Route::get('/checkout', [\App\Http\Controllers\Storefront\StorefrontController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [\App\Http\Controllers\Storefront\StorefrontController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/order/confirmed/{orderNumber}', [\App\Http\Controllers\Storefront\StorefrontController::class, 'orderConfirmation'])->name('order.confirmation');
    Route::get('/track', [\App\Http\Controllers\Storefront\StorefrontController::class, 'trackOrder'])->name('track');
    Route::post('/product/{id}/review', [\App\Http\Controllers\Storefront\StorefrontController::class, 'submitReview'])->name('review.submit');
    Route::get('/wishlist', [\App\Http\Controllers\Storefront\StorefrontController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\Storefront\StorefrontController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::post('/coupon/apply', [\App\Http\Controllers\Storefront\StorefrontController::class, 'applyCoupon'])->name('coupon.apply');
    Route::post('/coupon/remove', [\App\Http\Controllers\Storefront\StorefrontController::class, 'removeCoupon'])->name('coupon.remove');
    Route::post('/cart/save-for-later', [\App\Http\Controllers\Storefront\StorefrontController::class, 'saveForLater'])->name('cart.save_for_later');
    Route::post('/cart/move-to-cart', [\App\Http\Controllers\Storefront\StorefrontController::class, 'moveToCartFromSaved'])->name('cart.move_to_cart');
    Route::post('/cart/remove-saved', [\App\Http\Controllers\Storefront\StorefrontController::class, 'removeSaved'])->name('cart.remove_saved');
    Route::get('/buy-again', [\App\Http\Controllers\Storefront\StorefrontController::class, 'buyAgain'])->name('buy_again');
    Route::post('/product/{id}/notify-stock', [\App\Http\Controllers\Storefront\StorefrontController::class, 'subscribeStockNotification'])->name('product.notify_stock');
    Route::get('/compare', [\App\Http\Controllers\Storefront\StorefrontController::class, 'compare'])->name('compare');
    Route::post('/compare/toggle', [\App\Http\Controllers\Storefront\StorefrontController::class, 'toggleCompare'])->name('compare.toggle');
    Route::post('/compare/clear', [\App\Http\Controllers\Storefront\StorefrontController::class, 'clearCompare'])->name('compare.clear');
    Route::post('/product/{id}/question', [\App\Http\Controllers\Storefront\StorefrontController::class, 'askQuestion'])->name('product.question');
    Route::get('/returns', [\App\Http\Controllers\Storefront\StorefrontController::class, 'returns'])->name('returns');
    Route::post('/returns/submit', [\App\Http\Controllers\Storefront\StorefrontController::class, 'submitReturn'])->name('returns.submit');
    Route::post('/product/{id}/price-drop', [\App\Http\Controllers\Storefront\StorefrontController::class, 'setPriceAlert'])->name('product.price_drop');
    Route::get('/referral', [\App\Http\Controllers\Storefront\StorefrontController::class, 'referralProgram'])->name('referral');
    Route::post('/newsletter/subscribe', [\App\Http\Controllers\Storefront\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
});

// Meta WhatsApp Cloud API Public Webhook Endpoints
Route::get('/webhook/whatsapp', [\App\Http\Controllers\apps\CommunicationCenterController::class, 'verifyWhatsAppWebhook'])->name('webhook.whatsapp.verify');
Route::post('/webhook/whatsapp', [\App\Http\Controllers\apps\CommunicationCenterController::class, 'handleWhatsAppWebhook'])->name('webhook.whatsapp.receive');

// -----------------------------------------------------------------------
// Driver Portal (Authenticated Driver)
// -----------------------------------------------------------------------
Route::middleware(['auth:sanctum', 'verified'])
    ->prefix('driver')
    ->name('driver.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Driver\DriverDashboardController::class, 'index'])->name('dashboard');
        Route::post('/orders/assign/{order}', [\App\Http\Controllers\Driver\DriverOrderController::class, 'assign'])->name('orders.assign');
        Route::post('/orders/status', [\App\Http\Controllers\Driver\DriverOrderController::class, 'updateStatus'])->name('orders.status');
    });

// -----------------------------------------------------------------------
// Customer Account Portal (Authenticated)
// -----------------------------------------------------------------------
Route::middleware(['auth:sanctum', 'verified'])->prefix('customer/portal')->name('customer.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Customer\CustomerAccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [\App\Http\Controllers\Customer\CustomerAccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderNumber}', [\App\Http\Controllers\Customer\CustomerAccountController::class, 'orderDetails'])->name('order.details');
    Route::get('/profile', [\App\Http\Controllers\Customer\CustomerAccountController::class, 'profile'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Customer\CustomerAccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/wishlist', [\App\Http\Controllers\Customer\CustomerAccountController::class, 'wishlist'])->name('wishlist');
    Route::get('/wallet', [\App\Http\Controllers\Customer\CustomerAccountController::class, 'wallet'])->name('wallet');
    Route::get('/loyalty', [\App\Http\Controllers\Customer\CustomerAccountController::class, 'loyalty'])->name('loyalty');
});

// -----------------------------------------------------------------------
// Product Attributes (EAV), Store Management & Accounting Exports (Admin)
// -----------------------------------------------------------------------
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/products/attributes', [\App\Http\Controllers\apps\ProductAttributeController::class, 'index'])->name('app-attributes');
    Route::post('/products/attributes', [\App\Http\Controllers\apps\ProductAttributeController::class, 'store'])->name('app-attributes-store');
    Route::post('/products/attributes/{attribute}/value', [\App\Http\Controllers\apps\ProductAttributeController::class, 'storeValue'])->name('app-attributes-value-store');
    Route::delete('/products/attributes/{id}', [\App\Http\Controllers\apps\ProductAttributeController::class, 'destroy'])->name('app-attributes-destroy');

    // Hero Sliders & Product Merchandising Board
    Route::get('/store-management/sliders', [\App\Http\Controllers\apps\StoreBuilderController::class, 'sliders'])->name('app-sliders');
    Route::post('/store-management/sliders', [\App\Http\Controllers\apps\StoreBuilderController::class, 'storeSlider'])->name('app-sliders-store');
    Route::put('/store-management/sliders/{id}', [\App\Http\Controllers\apps\StoreBuilderController::class, 'updateSlider'])->name('app-sliders-update');
    Route::post('/store-management/sliders/{id}/toggle', [\App\Http\Controllers\apps\StoreBuilderController::class, 'toggleSliderStatus'])->name('app-sliders-toggle');
    Route::delete('/store-management/sliders/{id}', [\App\Http\Controllers\apps\StoreBuilderController::class, 'destroySlider'])->name('app-sliders-destroy');
    Route::get('/store-management/merchandising', [\App\Http\Controllers\apps\StoreBuilderController::class, 'merchandising'])->name('app-merchandising');
    Route::post('/store-management/merchandising/{id}/toggle', [\App\Http\Controllers\apps\StoreBuilderController::class, 'toggleMerchandising'])->name('app-merchandising-toggle');
    Route::get('/store-management/filters', [\App\Http\Controllers\apps\StoreBuilderController::class, 'filters'])->name('app-store-filters');
    Route::post('/store-management/filters', [\App\Http\Controllers\apps\StoreBuilderController::class, 'updateFilters'])->name('app-store-filters-save');

    // Product Relations & Suggestions
    Route::get('/products/{id}/relations', [\App\Http\Controllers\apps\StoreBuilderController::class, 'productRelations'])->name('app-product-relations');
    Route::post('/products/{id}/relations', [\App\Http\Controllers\apps\StoreBuilderController::class, 'storeProductRelation'])->name('app-product-relations-store');
    Route::delete('/products/{id}/relations/{relatedId}/{type}', [\App\Http\Controllers\apps\StoreBuilderController::class, 'destroyProductRelation'])->name('app-product-relations-destroy');

    // Communication Templates & WhatsApp API Config
    Route::get('/communication/email-templates', [\App\Http\Controllers\apps\CommunicationTemplatesController::class, 'emailTemplates'])->name('app-email-templates');
    Route::put('/communication/email-templates/{id}', [\App\Http\Controllers\apps\CommunicationTemplatesController::class, 'updateEmailTemplate'])->name('app-email-templates-update');
    Route::get('/communication/whatsapp-config', [\App\Http\Controllers\apps\CommunicationTemplatesController::class, 'whatsappConfig'])->name('app-whatsapp-config');
    Route::post('/communication/whatsapp-config', [\App\Http\Controllers\apps\CommunicationTemplatesController::class, 'updateWhatsappConfig'])->name('app-whatsapp-config-save');

    Route::get('/finance/accounting-export', [\App\Http\Controllers\apps\AccountingExportController::class, 'index'])->name('app-accounting-export');
    Route::get('/finance/accounting-export/sales', [\App\Http\Controllers\apps\AccountingExportController::class, 'exportSales'])->name('app-accounting-export-sales');
    Route::get('/finance/accounting-export/expenses', [\App\Http\Controllers\apps\AccountingExportController::class, 'exportExpenses'])->name('app-accounting-export-expenses');
    Route::get('/finance/accounting-export/gst', [\App\Http\Controllers\apps\AccountingExportController::class, 'exportGst'])->name('app-accounting-export-gst');

});


Route::get('/front-pages/landing', [Landing::class, 'index'])->name('front-pages-landing');
Route::get('/front-pages/pricing', [Pricing::class, 'index'])->name('front-pages-pricing');
Route::get('/front-pages/payment', [Payment::class, 'index'])->name('front-pages-payment');
Route::get('/front-pages/checkout', [Checkout::class, 'index'])->name('front-pages-checkout');
Route::get('/front-pages/help-center', [HelpCenter::class, 'index'])->name('front-pages-help-center');
Route::get('/front-pages/help-center-article', [HelpCenterArticle::class, 'index'])->name('front-pages-help-center-article');
Route::get('/sitemap.xml', [\App\Http\Controllers\apps\SaaS\SeoController::class, 'sitemap'])->name('app-saas-sitemap');
Route::get('/lang/{locale}', [LanguageController::class, 'swap'])->name('lang-swap');


// -----------------------------------------------------------------------
// OTP Authentication Routes (session-based, no auth guard needed)
// -----------------------------------------------------------------------
Route::prefix('auth')->name('auth.')->group(function () {
    // Login OTP
    Route::get('/otp',         [\App\Http\Controllers\Auth\OtpController::class, 'show'])   ->name('otp.show');
    Route::post('/otp',        [\App\Http\Controllers\Auth\OtpController::class, 'verify']) ->name('otp.verify');
    Route::post('/otp/resend', [\App\Http\Controllers\Auth\OtpController::class, 'resend'])->name('otp.resend');

    // Forgot Password via OTP
    Route::get('/forgot-password/otp',         [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'showRequestForm'])->name('forgot-password-otp.request');
    Route::post('/forgot-password/otp/send',   [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'sendOtp'])       ->name('forgot-password-otp.send');
    Route::get('/forgot-password/otp/verify',  [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'showVerifyForm'])->name('forgot-password-otp.verify-form');
    Route::post('/forgot-password/otp/verify', [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'verifyOtp'])     ->name('forgot-password-otp.verify');
    Route::post('/forgot-password/otp/resend', [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'resendOtp'])     ->name('forgot-password-otp.resend');

    // Password Reset (after OTP verified)
    Route::get('/password/reset-otp',  [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'showResetForm'])  ->name('password-reset-otp.form');
    Route::post('/password/reset-otp', [\App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'resetPassword'])  ->name('password-reset-otp.submit');
});


Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // -------------------------------------------------------------------
    // Searchable Select AJAX Endpoints (Select2 Server-Side Search)
    // -------------------------------------------------------------------
    Route::prefix('api/select')->name('api.select.')->group(function () {
        Route::get('/products',   [\App\Http\Controllers\api\SelectSearchController::class, 'products'])  ->name('products');
        Route::get('/customers',  [\App\Http\Controllers\api\SelectSearchController::class, 'customers']) ->name('customers');
        Route::get('/branches',   [\App\Http\Controllers\api\SelectSearchController::class, 'branches'])  ->name('branches');
        Route::get('/suppliers',  [\App\Http\Controllers\api\SelectSearchController::class, 'suppliers']) ->name('suppliers');
        Route::get('/categories', [\App\Http\Controllers\api\SelectSearchController::class, 'categories'])->name('categories');
        Route::get('/users',      [\App\Http\Controllers\api\SelectSearchController::class, 'users'])     ->name('users');
        Route::get('/roles',      [\App\Http\Controllers\api\SelectSearchController::class, 'roles'])     ->name('roles');
    });

    // Route::get('/', [EcommerceDashboard::class, 'index'])->name('dashboard-analytics'); // removed duplicate root route
    Route::get('/dashboard/analytics', [EcommerceDashboard::class, 'index'])->name('dashboard-analytics');
    Route::get('/dashboard/crm', [Crm::class, 'index'])->name('dashboard-crm');

    // Branch
    Route::get('/branch/{id}', [\App\Http\Controllers\BranchController::class, 'swap'])->name('branch-swap')->middleware('branch.access');

    // SaaS Management (Super Admin)
    Route::prefix('saas')->group(function () {
        Route::get('/billing', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'index'])->name('app-saas-billing');
        Route::post('/billing/subscribe', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'subscribe'])->name('app-saas-subscribe');
        Route::post('/billing/cancel', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'cancel'])->name('app-saas-cancel');
        Route::post('/billing/resume', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'resume'])->name('app-saas-resume');
        
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
        Route::get('/kyc-admin', [\App\Http\Controllers\apps\SaaS\KycAdminController::class, 'index']);
        Route::get('/kyc/{vendorKyc}', [\App\Http\Controllers\apps\SaaS\KycAdminController::class, 'show'])->name('app-saas-kyc-show');
        Route::post('/kyc/{vendorKyc}/approve', [\App\Http\Controllers\apps\SaaS\KycAdminController::class, 'approve'])->name('app-saas-kyc-approve');
        Route::post('/kyc/{vendorKyc}/reject', [\App\Http\Controllers\apps\SaaS\KycAdminController::class, 'reject'])->name('app-saas-kyc-reject');
        Route::post('/kyc/{vendorKyc}/review', [\App\Http\Controllers\apps\SaaS\KycAdminController::class, 'markUnderReview'])->name('app-saas-kyc-review');
        Route::get('/dunning', [\App\Http\Controllers\apps\SaaS\DunningController::class, 'index'])->name('app-saas-dunning');
        Route::post('/dunning/trigger', [\App\Http\Controllers\apps\SaaS\DunningController::class, 'trigger'])->name('app-saas-dunning-trigger');
    });

    // Vendor Management
    Route::prefix('vendor')->group(function () {
        Route::get('/kyc', [\App\Http\Controllers\apps\Vendor\KycController::class, 'index'])->name('app-vendor-kyc');
        Route::post('/kyc', [\App\Http\Controllers\apps\Vendor\KycController::class, 'store'])->name('app-vendor-kyc-store');
        Route::get('/inventory', [\App\Http\Controllers\apps\Vendor\InventoryController::class, 'index'])->name('app-vendor-inventory');
        Route::post('/inventory/update', [\App\Http\Controllers\apps\Vendor\InventoryController::class, 'updateStock'])->name('app-vendor-inventory-update');
        Route::post('/inventory/transfer', [\App\Http\Controllers\apps\Vendor\InventoryController::class, 'storeTransfer'])->name('app-inventory-transfer-store');
        Route::post('/inventory/transfer/{id}/receive', [\App\Http\Controllers\apps\Vendor\InventoryController::class, 'receiveTransfer'])->name('app-inventory-transfer-receive');
        Route::get('/returns', [\App\Http\Controllers\apps\Vendor\ReturnRequestController::class, 'index'])->name('app-vendor-returns');
        Route::post('/returns/{returnRequest}', [\App\Http\Controllers\apps\Vendor\ReturnRequestController::class, 'updateStatus'])->name('app-vendor-returns-update');
        Route::get('/pos', [\App\Http\Controllers\apps\Vendor\PosController::class, 'index'])->name('app-vendor-pos');
        Route::get('/pos/search', [\App\Http\Controllers\apps\Vendor\PosController::class, 'search'])->name('app-vendor-pos-search');
        Route::post('/pos/checkout', [\App\Http\Controllers\apps\Vendor\PosController::class, 'checkout'])->name('app-vendor-pos-checkout');
        Route::get('/wallet', [\App\Http\Controllers\apps\Vendor\WalletController::class, 'index'])->name('app-vendor-wallet');
        Route::get('/store-builder', [\App\Http\Controllers\apps\Vendor\StoreBuilderController::class, 'index'])->name('app-vendor-store-builder');
        Route::post('/store-builder', [\App\Http\Controllers\apps\Vendor\StoreBuilderController::class, 'store'])->name('app-vendor-store-builder-save');
        Route::get('/support', [\App\Http\Controllers\apps\Vendor\SupportTicketController::class, 'index'])->name('app-vendor-support');
        Route::get('/support-tickets', [\App\Http\Controllers\apps\Vendor\SupportTicketController::class, 'index']);
        Route::get('/support/{ticket}', [\App\Http\Controllers\apps\Vendor\SupportTicketController::class, 'show'])->name('app-vendor-support-show');
        Route::post('/support/{ticket}/reply', [\App\Http\Controllers\apps\Vendor\SupportTicketController::class, 'reply'])->name('app-vendor-support-reply');
        Route::post('/support/{ticket}/status', [\App\Http\Controllers\apps\Vendor\SupportTicketController::class, 'updateStatus'])->name('app-vendor-support-status');
        Route::post('/wallet/payout', [\App\Http\Controllers\apps\Vendor\WalletController::class, 'requestPayout'])->name('app-vendor-wallet-payout');
        Route::get('/payment-settings', [\App\Http\Controllers\apps\Vendor\PaymentSettingsController::class, 'index'])->name('app-vendor-payment-settings');
        Route::post('/payment-settings', [\App\Http\Controllers\apps\Vendor\PaymentSettingsController::class, 'store'])->name('app-vendor-payment-settings-save');
    });

    Route::post('/app/vendor/pos/checkout', [\App\Http\Controllers\apps\Vendor\PosController::class, 'checkout']);

    // Catalog Scanner & Duplicate Detection
    Route::get('/catalog/scanner', [\App\Http\Controllers\apps\CatalogScannerController::class, 'index'])->name('app-catalog-scanner');
    Route::get('/catalog/duplicates', [\App\Http\Controllers\apps\CatalogScannerController::class, 'duplicateScanner'])->name('app-catalog-duplicates');
    Route::post('/catalog/scanner/autofix', [\App\Http\Controllers\apps\CatalogScannerController::class, 'autoFix'])->name('app-catalog-scanner-autofix');

    // Smart Product Importer & URL Scraper
    Route::get('/catalog/importer', [\App\Http\Controllers\apps\ProductImportController::class, 'index'])->name('app-product-importer');
    Route::post('/catalog/importer/url', [\App\Http\Controllers\apps\ProductImportController::class, 'parseUrl'])->name('app-product-import-url');
    Route::post('/catalog/importer/file', [\App\Http\Controllers\apps\ProductImportController::class, 'parseFile'])->name('app-product-import-file');
    Route::get('/catalog/importer/review/{id}', [\App\Http\Controllers\apps\ProductImportController::class, 'review'])->name('app-product-import-review');
    Route::post('/catalog/importer/review/{id}/publish', [\App\Http\Controllers\apps\ProductImportController::class, 'publish'])->name('app-product-import-publish');
    Route::delete('/catalog/importer/draft/{id}', [\App\Http\Controllers\apps\ProductImportController::class, 'destroy'])->name('app-product-import-destroy');

    // AI Product Tools & Optimizer
    Route::post('/ai/product/generate', [\App\Http\Controllers\apps\AIProductToolsController::class, 'generateContent'])->name('app-ai-product-content');
    Route::post('/ai/product/optimize', [\App\Http\Controllers\apps\AIProductToolsController::class, 'optimizeProduct'])->name('app-ai-product-optimize');
    Route::post('/ai/product/extract-attributes', [\App\Http\Controllers\apps\AIProductToolsController::class, 'extractAttributes'])->name('app-ai-extract-attributes');
    Route::post('/ai/product/suggest-category', [\App\Http\Controllers\apps\AIProductToolsController::class, 'suggestCategory'])->name('app-ai-suggest-category');

    // Expenses Management
    Route::get('/expenses', [\App\Http\Controllers\apps\ExpenseController::class, 'index'])->name('app-expenses');
    Route::post('/expenses', [\App\Http\Controllers\apps\ExpenseController::class, 'store'])->name('app-expenses-store');
    Route::post('/expenses/categories', [\App\Http\Controllers\apps\ExpenseController::class, 'storeCategory'])->name('app-expenses-category-store');
    Route::delete('/expenses/{id}', [\App\Http\Controllers\apps\ExpenseController::class, 'destroy'])->name('app-expenses-destroy');

    // Workflow Automation Engine
    Route::get('/automation', [\App\Http\Controllers\apps\WorkflowAutomationController::class, 'index'])->name('app-automation');
    Route::post('/automation', [\App\Http\Controllers\apps\WorkflowAutomationController::class, 'store'])->name('app-automation-store');
    Route::post('/automation/{id}/toggle', [\App\Http\Controllers\apps\WorkflowAutomationController::class, 'toggle'])->name('app-automation-toggle');
    Route::delete('/automation/{id}', [\App\Http\Controllers\apps\WorkflowAutomationController::class, 'destroy'])->name('app-automation-destroy');

    // Multi-Warehouse Management
    Route::get('/inventory/warehouses', [\App\Http\Controllers\apps\WarehouseController::class, 'index'])->name('app-warehouses');
    Route::post('/inventory/warehouses', [\App\Http\Controllers\apps\WarehouseController::class, 'store'])->name('app-warehouses-store');
    Route::get('/inventory/warehouses/{warehouse}', [\App\Http\Controllers\apps\WarehouseController::class, 'show'])->name('app-warehouses-show');
    Route::post('/inventory/warehouses/{warehouse}/stock', [\App\Http\Controllers\apps\WarehouseController::class, 'updateStock'])->name('app-warehouses-stock-update');

    // Cycle Counting & Stock Audits
    Route::get('/inventory/stock-counts', [\App\Http\Controllers\apps\StockCountController::class, 'index'])->name('app-stock-counts');
    Route::post('/inventory/stock-counts', [\App\Http\Controllers\apps\StockCountController::class, 'store'])->name('app-stock-counts-store');
    Route::get('/inventory/stock-counts/{stockCount}', [\App\Http\Controllers\apps\StockCountController::class, 'show'])->name('app-stock-counts-show');
    Route::post('/inventory/stock-counts/{stockCount}/item/{item}', [\App\Http\Controllers\apps\StockCountController::class, 'updateItem'])->name('app-stock-counts-item-update');
    Route::post('/inventory/stock-counts/{stockCount}/reconcile', [\App\Http\Controllers\apps\StockCountController::class, 'reconcile'])->name('app-stock-counts-reconcile');

    // ABC Inventory Analysis & Dead Stock
    Route::get('/inventory/abc-analysis', [\App\Http\Controllers\apps\AbcAnalysisController::class, 'index'])->name('app-inventory-abc');

    // B2B & Wholesale Accounts
    Route::get('/b2b/companies', [\App\Http\Controllers\apps\B2bCompanyController::class, 'index'])->name('app-b2b-companies');
    Route::post('/b2b/companies', [\App\Http\Controllers\apps\B2bCompanyController::class, 'store'])->name('app-b2b-companies-store');
    Route::get('/b2b/companies/{company}', [\App\Http\Controllers\apps\B2bCompanyController::class, 'show'])->name('app-b2b-companies-show');
    Route::post('/b2b/companies/{company}/buyer', [\App\Http\Controllers\apps\B2bCompanyController::class, 'addBuyer'])->name('app-b2b-companies-buyer');
    Route::post('/b2b/companies/{company}/tier-price', [\App\Http\Controllers\apps\B2bCompanyController::class, 'addTierPrice'])->name('app-b2b-companies-tier-price');

    // B2B Quotes
    Route::get('/b2b/quotes', [\App\Http\Controllers\apps\B2bQuoteController::class, 'index'])->name('app-b2b-quotes');
    Route::post('/b2b/quotes', [\App\Http\Controllers\apps\B2bQuoteController::class, 'store'])->name('app-b2b-quotes-store');
    Route::post('/b2b/quotes/{quote}/status', [\App\Http\Controllers\apps\B2bQuoteController::class, 'updateStatus'])->name('app-b2b-quotes-status');

    // Advanced Fulfillment
    Route::get('/fulfillment', [\App\Http\Controllers\apps\FulfillmentController::class, 'index'])->name('app-fulfillment');
    Route::post('/fulfillment', [\App\Http\Controllers\apps\FulfillmentController::class, 'store'])->name('app-fulfillment-store');
    Route::post('/fulfillment/{fulfillment}/status', [\App\Http\Controllers\apps\FulfillmentController::class, 'updateStatus'])->name('app-fulfillment-status');
    Route::get('/fulfillment/{fulfillment}/pickpack', [\App\Http\Controllers\apps\FulfillmentController::class, 'pickPackList'])->name('app-fulfillment-pickpack');

    // Customer Experience Portal
    Route::get('/customer/portal', [\App\Http\Controllers\apps\CustomerPortalController::class, 'index'])->name('app-customer-portal');
    Route::post('/customer/wishlist/toggle', [\App\Http\Controllers\apps\CustomerPortalController::class, 'toggleWishlist'])->name('app-customer-wishlist-toggle');
    Route::post('/customer/saved-cart', [\App\Http\Controllers\apps\CustomerPortalController::class, 'saveCart'])->name('app-customer-saved-cart');

    // Gift Cards & Vouchers
    Route::get('/gift-cards', [\App\Http\Controllers\apps\GiftCardController::class, 'index'])->name('app-gift-cards');
    Route::post('/gift-cards', [\App\Http\Controllers\apps\GiftCardController::class, 'store'])->name('app-gift-cards-store');
    Route::post('/gift-cards/lookup', [\App\Http\Controllers\apps\GiftCardController::class, 'lookup'])->name('app-gift-cards-lookup');

    // Finance POS Register & Reconciliation
    Route::get('/finance/pos-register', [\App\Http\Controllers\apps\PosRegisterController::class, 'index'])->name('app-pos-register');
    Route::post('/finance/pos-register/open', [\App\Http\Controllers\apps\PosRegisterController::class, 'open'])->name('app-pos-register-open');
    Route::post('/finance/pos-register/close', [\App\Http\Controllers\apps\PosRegisterController::class, 'close'])->name('app-pos-register-close');

    // Marketing & Abandoned Carts
    Route::get('/marketing/abandoned-carts', [\App\Http\Controllers\apps\AbandonedCartController::class, 'index'])->name('app-abandoned-carts');
    Route::post('/marketing/abandoned-carts/{cart}/send', [\App\Http\Controllers\apps\AbandonedCartController::class, 'sendRecovery'])->name('app-abandoned-carts-send');

    // Omnichannel Feeds
    Route::get('/marketing/feeds', [\App\Http\Controllers\apps\ProductFeedController::class, 'index'])->name('app-feeds');
    Route::get('/feeds/google.xml', [\App\Http\Controllers\apps\ProductFeedController::class, 'googleXml'])->name('app-feeds-google');
    Route::get('/feeds/meta.csv', [\App\Http\Controllers\apps\ProductFeedController::class, 'metaCsv'])->name('app-feeds-meta');
    Route::get('/feeds/tiktok.json', [\App\Http\Controllers\apps\ProductFeedController::class, 'tikTokJson'])->name('app-feeds-tiktok');

    // Developer Webhooks Hub
    Route::get('/developer/webhooks', [\App\Http\Controllers\apps\DeveloperWebhookController::class, 'index'])->name('app-developer-webhooks');
    Route::post('/developer/webhooks', [\App\Http\Controllers\apps\DeveloperWebhookController::class, 'store'])->name('app-developer-webhooks-store');
    Route::post('/developer/webhooks/{subscription}/ping', [\App\Http\Controllers\apps\DeveloperWebhookController::class, 'testPing'])->name('app-developer-webhooks-ping');
    Route::post('/developer/webhooks/{subscription}/toggle', [\App\Http\Controllers\apps\DeveloperWebhookController::class, 'toggle'])->name('app-developer-webhooks-toggle');

    // Unified Communication Center (Email & WhatsApp)
    Route::get('/communication', [\App\Http\Controllers\apps\CommunicationCenterController::class, 'index'])->name('app-communication-center');
    Route::get('/marketing/communication', [\App\Http\Controllers\apps\CommunicationCenterController::class, 'index']);
    Route::post('/communication/send', [\App\Http\Controllers\apps\CommunicationCenterController::class, 'send'])->name('app-communication-send');
    Route::post('/communication/templates', [\App\Http\Controllers\apps\CommunicationCenterController::class, 'saveTemplate'])->name('app-communication-template-save');
    Route::post('/communication/campaigns', [\App\Http\Controllers\apps\CommunicationCenterController::class, 'launchCampaign'])->name('app-communication-campaign-launch');

    // Workflow Automation Engine
    Route::get('/automation/rules', [\App\Http\Controllers\apps\WorkflowAutomationController::class, 'index'])->name('app-automation-rules');
    Route::post('/automation/rules', [\App\Http\Controllers\apps\WorkflowAutomationController::class, 'store'])->name('app-automation-rules-store');
    Route::post('/automation/rules/{id}/toggle', [\App\Http\Controllers\apps\WorkflowAutomationController::class, 'toggle'])->name('app-automation-rules-toggle');
    Route::delete('/automation/rules/{id}', [\App\Http\Controllers\apps\WorkflowAutomationController::class, 'destroy'])->name('app-automation-rules-destroy');

    // System Health, Backups & Security Center
    Route::get('/system/health', [\App\Http\Controllers\apps\SystemHealthController::class, 'index'])->name('app-system-health');
    Route::get('/system/backups', [\App\Http\Controllers\apps\BackupController::class, 'index'])->name('app-backups');
    Route::post('/system/backups/create', [\App\Http\Controllers\apps\BackupController::class, 'createSnapshot'])->name('app-backups-create');
    Route::get('/system/security-center', [\App\Http\Controllers\apps\SecurityCenterController::class, 'index'])->name('app-security-center');

    // Suppliers Management
    Route::get('/suppliers', [\App\Http\Controllers\apps\SupplierController::class, 'index'])->name('app-suppliers');
    Route::post('/suppliers', [\App\Http\Controllers\apps\SupplierController::class, 'store'])->name('app-suppliers-store');

    // Purchase Orders Management
    Route::get('/purchases', [\App\Http\Controllers\apps\PurchaseOrderController::class, 'index'])->name('app-purchases');
    Route::post('/purchases', [\App\Http\Controllers\apps\PurchaseOrderController::class, 'store'])->name('app-purchases-store');
    Route::post('/purchases/{id}/receive', [\App\Http\Controllers\apps\PurchaseOrderController::class, 'markReceived'])->name('app-purchases-receive');

    // POS Direct Endpoint
    Route::get('/pos', [\App\Http\Controllers\apps\Vendor\PosController::class, 'index'])->name('pos-direct');

    // Logistics & Shipping Methods
    Route::get('/logistics/shipping', [\App\Http\Controllers\apps\Logistics\ShippingMethodController::class, 'index'])->name('app-logistics-shipping');
    Route::post('/logistics/shipping', [\App\Http\Controllers\apps\Logistics\ShippingMethodController::class, 'store'])->name('app-logistics-shipping-store');
    Route::post('/logistics/shipping/{method}/toggle', [\App\Http\Controllers\apps\Logistics\ShippingMethodController::class, 'toggle'])->name('app-logistics-shipping-toggle');
    Route::delete('/logistics/shipping/{method}', [\App\Http\Controllers\apps\Logistics\ShippingMethodController::class, 'destroy'])->name('app-logistics-shipping-destroy');
    
    // User Management
    Route::get('/admin/users', [\App\Http\Controllers\laravel_example\UserManagement::class, 'UserManagement'])->name('laravel-user-management');
    Route::get('/admin/users/list', [\App\Http\Controllers\laravel_example\UserManagement::class, 'index'])->name('user-list');
    Route::post('/admin/users/list', [\App\Http\Controllers\laravel_example\UserManagement::class, 'store'])->name('user-list-store');
    Route::get('/admin/users/{id}/edit', [\App\Http\Controllers\laravel_example\UserManagement::class, 'edit'])->name('user-list-edit');
    Route::put('/admin/users/{id}', [\App\Http\Controllers\laravel_example\UserManagement::class, 'update'])->name('user-list-update');
    Route::delete('/admin/users/{id}', [\App\Http\Controllers\laravel_example\UserManagement::class, 'destroy'])->name('user-list-destroy');

    // Core E-commerce Apps
    Route::middleware(['tenant.subscription'])->group(function () {
        Route::get('/admin/dashboard', [EcommerceDashboard::class, 'index'])->name('app-ecommerce-dashboard');
        
        // Products & Inventory
        Route::get('/products', [EcommerceProductList::class, 'index'])->name('app-ecommerce-product-list');
        Route::get('/app/ecommerce/product/list', [EcommerceProductList::class, 'index']);
        Route::get('/inventory', [\App\Http\Controllers\apps\Vendor\InventoryController::class, 'index'])->name('app-ecommerce-inventory');
        Route::get('/app/ecommerce/inventory', [\App\Http\Controllers\apps\Vendor\InventoryController::class, 'index']);
        Route::get('/products/create', [EcommerceProductAdd::class, 'index'])->name('app-ecommerce-product-add');
        Route::get('/app/ecommerce/product/add', [EcommerceProductAdd::class, 'index']);
        Route::post('/products/create', [EcommerceProductAdd::class, 'store'])->name('app-ecommerce-product-add-post');
        Route::post('/app/ecommerce/product/add', [EcommerceProductAdd::class, 'store']);
        Route::get('/products/{id}/edit', [EcommerceProductAdd::class, 'edit'])->name('app-ecommerce-product-edit');
        Route::get('/app/ecommerce/product/edit/{id}', [EcommerceProductAdd::class, 'edit']);
        Route::put('/products/{id}/edit', [EcommerceProductAdd::class, 'update'])->name('app-ecommerce-product-update');
        Route::put('/app/ecommerce/product/edit/{id}', [EcommerceProductAdd::class, 'update']);
        Route::delete('/products/{id}', [EcommerceProductList::class, 'destroy'])->name('app-ecommerce-product-delete');
        Route::delete('/app/ecommerce/product/{id}', [EcommerceProductList::class, 'destroy']);
        Route::post('/products/bulk-status', [EcommerceProductList::class, 'bulkStatus'])->name('app-ecommerce-product-bulk-status');
        Route::post('/products/bulk-category', [EcommerceProductList::class, 'bulkCategory'])->name('app-ecommerce-product-bulk-category');
        Route::post('/products/bulk-pricing', [EcommerceProductList::class, 'bulkPricing'])->name('app-ecommerce-product-bulk-pricing');
        Route::post('/products/{id}/duplicate', [EcommerceProductList::class, 'duplicate'])->name('app-ecommerce-product-duplicate');
        Route::get('/products/categories', [EcommerceProductCategory::class, 'index'])->name('app-ecommerce-product-category');
        Route::get('/app/ecommerce/product/category', [EcommerceProductCategory::class, 'index']);
        Route::post('/products/categories', [EcommerceProductCategory::class, 'store'])->name('app-ecommerce-category-add');
        Route::post('/app/ecommerce/product/category', [EcommerceProductCategory::class, 'store']);
        Route::put('/products/categories/{id}', [EcommerceProductCategory::class, 'update'])->name('app-ecommerce-category-update');
        Route::put('/app/ecommerce/product/category/{id}', [EcommerceProductCategory::class, 'update']);
        Route::delete('/products/categories/{id}', [EcommerceProductCategory::class, 'destroy'])->name('app-ecommerce-category-delete');
        Route::delete('/app/ecommerce/product/category/{id}', [EcommerceProductCategory::class, 'destroy']);

        // Orders
        Route::get('/orders', [EcommerceOrderList::class, 'index'])->name('app-ecommerce-order-list');
        Route::get('/app/ecommerce/order/list', [EcommerceOrderList::class, 'index']);
        Route::delete('/orders/{id}', [EcommerceOrderList::class, 'destroy'])->name('app-ecommerce-order-delete');
        Route::delete('/app/ecommerce/order/{id}', [EcommerceOrderList::class, 'destroy']);
        Route::patch('/orders/{id}/status', [EcommerceOrderList::class, 'updateStatus'])->name('app-ecommerce-order-status-update');
        Route::patch('/app/ecommerce/order/{id}/status', [EcommerceOrderList::class, 'updateStatus']);
        Route::get('/orders/{id?}', [EcommerceOrderDetails::class, 'index'])->name('app-ecommerce-order-details');
        Route::get('/app/ecommerce/order/details/{id?}', [EcommerceOrderDetails::class, 'index']);

        // Management
        Route::get('/reviews', [EcommerceManageReviews::class, 'index'])->name('app-ecommerce-manage-reviews');
        Route::get('/app/ecommerce/manage/reviews', [EcommerceManageReviews::class, 'index']);
        Route::get('/referrals', [EcommerceReferrals::class, 'index'])->name('app-ecommerce-referrals');
        Route::get('/app/ecommerce/referrals', [EcommerceReferrals::class, 'index']);
        
        // Branch Management
        Route::get('/branches', [EcommerceBranchManagement::class, 'index'])->name('app-ecommerce-branch-list');
        Route::get('/app/ecommerce/branch/list', [EcommerceBranchManagement::class, 'index']);
        Route::post('/branches', [EcommerceBranchManagement::class, 'store'])->name('app-ecommerce-branch-store');
        Route::post('/app/ecommerce/branch', [EcommerceBranchManagement::class, 'store']);
        Route::get('/branches/{id}/edit', [EcommerceBranchManagement::class, 'edit'])->name('app-ecommerce-branch-edit');
        Route::put('/branches/{id}', [EcommerceBranchManagement::class, 'update'])->name('app-ecommerce-branch-update');
        Route::delete('/branches/{id}', [EcommerceBranchManagement::class, 'destroy'])->name('app-ecommerce-branch-delete');

        // Customers (E-Commerce Store Customers)
        Route::get('/customers', [EcommerceCustomerAll::class, 'index'])->name('app-ecommerce-customer-all');
        Route::get('/app/ecommerce/customer/all', [EcommerceCustomerAll::class, 'index']);
        Route::post('/customers', [EcommerceCustomerAll::class, 'store'])->name('app-ecommerce-customer-store');
        Route::post('/app/ecommerce/customer', [EcommerceCustomerAll::class, 'store']);
        Route::put('/customers/{id}', [EcommerceCustomerAll::class, 'update'])->name('app-ecommerce-customer-update');
        Route::put('/app/ecommerce/customer/{id}', [EcommerceCustomerAll::class, 'update']);
        Route::delete('/customers/{id}', [EcommerceCustomerAll::class, 'destroy'])->name('app-ecommerce-customer-delete');
        Route::delete('/app/ecommerce/customer/{id}', [EcommerceCustomerAll::class, 'destroy']);
        Route::get('/customers/{id?}/overview', [EcommerceCustomerDetailsOverview::class, 'index'])->name('app-ecommerce-customer-details-overview');
        Route::get('/app/ecommerce/customer/details/overview/{id?}', [EcommerceCustomerDetailsOverview::class, 'index']);
        Route::get('/customers/{id?}/security', [EcommerceCustomerDetailsSecurity::class, 'index'])->name('app-ecommerce-customer-details-security');
        Route::get('/customers/{id?}/billing', [EcommerceCustomerDetailsBilling::class, 'index'])->name('app-ecommerce-customer-details-billing');
        Route::get('/customers/{id?}/notifications', [EcommerceCustomerDetailsNotifications::class, 'index'])->name('app-ecommerce-customer-details-notifications');
        
        // Coupons
        Route::get('/coupons', [EcommerceCouponController::class, 'index'])->name('app-ecommerce-coupon-list');
        Route::get('/app/ecommerce/coupons', [EcommerceCouponController::class, 'index']);
        Route::post('/coupons', [EcommerceCouponController::class, 'store'])->name('app-ecommerce-coupon-store');
        Route::get('/coupons/{id}/edit', [EcommerceCouponController::class, 'edit'])->name('app-ecommerce-coupon-edit');
        Route::put('/coupons/{id}', [EcommerceCouponController::class, 'update'])->name('app-ecommerce-coupon-update');
        Route::delete('/coupons/{id}', [EcommerceCouponController::class, 'destroy'])->name('app-ecommerce-coupon-delete');
        Route::post('/coupons/bulk-generate', [EcommerceCouponController::class, 'bulkGenerate'])->name('app-ecommerce-coupon-bulk');
        
        // Centralized Store Settings & E-Commerce Management Center
        Route::get('/settings', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->name('app-ecommerce-settings');
        Route::get('/settings/store', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->name('app-ecommerce-settings-details');
        Route::get('/app/ecommerce/settings/details', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection']);
        Route::post('/settings/store/save', [\App\Http\Controllers\apps\SettingsHubController::class, 'saveSection'])->defaults('section', 'store')->name('app-ecommerce-settings-details-save');

        Route::get('/settings/payments', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'payments')->name('app-ecommerce-settings-payments');
        Route::get('/app/ecommerce/settings/payments', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'payments');
        Route::post('/settings/payments/save', [\App\Http\Controllers\apps\SettingsHubController::class, 'saveSection'])->defaults('section', 'payments')->name('app-ecommerce-settings-payments-save');

        Route::get('/settings/checkout', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'checkout')->name('app-ecommerce-settings-checkout');
        Route::get('/app/ecommerce/settings/checkout', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'checkout');
        Route::post('/settings/checkout/save', [\App\Http\Controllers\apps\SettingsHubController::class, 'saveSection'])->defaults('section', 'checkout')->name('app-ecommerce-settings-checkout-save');

        Route::get('/settings/shipping', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'shipping')->name('app-ecommerce-settings-shipping');
        Route::get('/app/ecommerce/settings/shipping', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'shipping');
        Route::post('/settings/shipping/save', [\App\Http\Controllers\apps\SettingsHubController::class, 'saveSection'])->defaults('section', 'shipping')->name('app-ecommerce-settings-shipping-save');

        Route::get('/settings/locations', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'locations')->name('app-ecommerce-settings-locations');
        Route::get('/app/ecommerce/settings/locations', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'locations');
        Route::post('/settings/locations/save', [\App\Http\Controllers\apps\SettingsHubController::class, 'saveSection'])->defaults('section', 'locations')->name('app-ecommerce-settings-locations-save');

        Route::get('/settings/notifications', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'notifications')->name('app-ecommerce-settings-notifications');
        Route::get('/app/ecommerce/settings/notifications', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->defaults('section', 'notifications');
        Route::post('/settings/notifications/save', [\App\Http\Controllers\apps\SettingsHubController::class, 'saveSection'])->defaults('section', 'notifications')->name('app-ecommerce-settings-notifications-save');

        // Modular Settings Sections & Actions
        Route::get('/settings/{section}', [\App\Http\Controllers\apps\SettingsHubController::class, 'showSection'])->name('settings.section');
        Route::post('/settings/{section}/save', [\App\Http\Controllers\apps\SettingsHubController::class, 'saveSection'])->name('settings.section.save');
        Route::post('/settings-action/email/test-smtp', [\App\Http\Controllers\apps\SettingsHubController::class, 'testSmtp'])->name('settings.email.test-smtp');
        Route::post('/settings-action/whatsapp/test', [\App\Http\Controllers\apps\SettingsHubController::class, 'testWhatsApp'])->name('settings.whatsapp.test');
        Route::post('/settings-action/templates/save', [\App\Http\Controllers\apps\SettingsHubController::class, 'saveTemplate'])->name('settings.templates.save');
        Route::post('/settings-action/cache/clear', [\App\Http\Controllers\apps\SettingsHubController::class, 'clearCache'])->name('settings.cache.clear');

        // Invoices
        Route::get('/invoices', 'App\Http\Controllers\apps\InvoiceList@index')->name('app-invoice-list');
        Route::get('/app/invoice/list', 'App\Http\Controllers\apps\InvoiceList@index');
        Route::get('/invoices/{id?}/preview', 'App\Http\Controllers\apps\InvoicePreview@index')->name('app-invoice-preview');
        Route::get('/invoices/{id?}/print', 'App\Http\Controllers\apps\InvoicePrint@index')->name('app-invoice-print');
        Route::get('/invoices/create', 'App\Http\Controllers\apps\InvoiceAdd@index')->name('app-invoice-add');
        Route::get('/invoices/edit', 'App\Http\Controllers\apps\InvoiceEdit@index')->name('app-invoice-edit');

        // System Users Management & Staff Profiles
        Route::get('/staff', [UserList::class, 'index'])->name('app-user-list');
        Route::get('/staff/account', [UserViewAccount::class, 'index'])->name('app-user-view-account');
        Route::get('/staff/security', [UserViewSecurity::class, 'index'])->name('app-user-view-security');
        Route::get('/staff/billing', [UserViewBilling::class, 'index'])->name('app-user-view-billing');
        Route::get('/staff/notifications', [UserViewNotifications::class, 'index'])->name('app-user-view-notifications');
        Route::get('/staff/connections', [UserViewConnections::class, 'index'])->name('app-user-view-connections');

        // AI Copilot
        Route::post('/ai/copilot', [\App\Http\Controllers\apps\AICopilotController::class, 'chat'])->name('app-ai-copilot-chat');
    });

    // Chat & Messaging App
    Route::get('/chat', [\App\Http\Controllers\apps\Chat::class, 'index'])->name('app-chat');
    Route::get('/app/chat', [\App\Http\Controllers\apps\Chat::class, 'index']);

    // Calendar App
    Route::get('/calendar', [\App\Http\Controllers\apps\Calendar::class, 'index'])->name('app-calendar');
    Route::get('/app/calendar', [\App\Http\Controllers\apps\Calendar::class, 'index']);

    // AI Copilot Hub & Settings
    Route::get('/settings/ai', [AISettingsController::class, 'index'])->name('app-ecommerce-settings-ai');
    Route::get('/app/ecommerce/settings/ai', [AISettingsController::class, 'index']);
    Route::get('/apps/ai-copilot', [AISettingsController::class, 'index'])->name('app-ai-copilot');
    Route::post('/settings/ai/save', [AISettingsController::class, 'store'])->name('app-ecommerce-settings-ai-save');
    Route::post('/app/ecommerce/settings/ai/save', [AISettingsController::class, 'store']);
    Route::post('/api/ai/copilot-chat', [\App\Http\Controllers\apps\AIProductToolsController::class, 'copilotChat'])->name('app-ai-product-copilot-chat');
    Route::post('/api/ai/generate-content', [\App\Http\Controllers\apps\AIProductToolsController::class, 'generateContent']);
    Route::post('/app/ecommerce/ai/generate', [\App\Http\Controllers\apps\AIProductToolsController::class, 'generateContent']);
    Route::post('/api/ai/quality-score', [\App\Http\Controllers\apps\AIProductToolsController::class, 'calculateQualityScore']);
    Route::post('/api/ai/extract-attributes', [\App\Http\Controllers\apps\AIProductToolsController::class, 'extractAttributes']);
    Route::post('/api/ai/suggest-category', [\App\Http\Controllers\apps\AIProductToolsController::class, 'suggestCategory']);

    // Branding & Logo Settings
    Route::get('/settings/branding', [\App\Http\Controllers\apps\EcommerceSettingsBranding::class, 'index'])->name('app-ecommerce-settings-branding');
    Route::get('/app/ecommerce/settings/branding', [\App\Http\Controllers\apps\EcommerceSettingsBranding::class, 'index']);
    Route::post('/settings/branding/save', [\App\Http\Controllers\apps\EcommerceSettingsBranding::class, 'store'])->name('app-ecommerce-settings-branding-save');
    Route::post('/app/ecommerce/settings/branding/save', [\App\Http\Controllers\apps\EcommerceSettingsBranding::class, 'store']);

    // Maps Settings
    Route::get('/settings/maps', [MapsSettingsController::class, 'index'])->name('app-ecommerce-settings-maps');
    Route::get('/app/ecommerce/settings/maps', [MapsSettingsController::class, 'index']);
    Route::post('/settings/maps/save', [MapsSettingsController::class, 'store'])->name('app-ecommerce-settings-maps-save');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\apps\SystemNotificationController::class, 'index'])->name('app-notifications');
    Route::post('/notifications/mark-all', [\App\Http\Controllers\apps\SystemNotificationController::class, 'markAllAsRead'])->name('app-notifications-mark-all');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\apps\SystemNotificationController::class, 'markAsRead'])->name('app-notifications-read');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\apps\SystemNotificationController::class, 'markAsRead']);

    // Access Hub & Roles Management
    Route::get('/roles', [\App\Http\Controllers\apps\RoleController::class, 'index'])->name('app-access-roles');
    Route::get('/app/roles', [\App\Http\Controllers\apps\RoleController::class, 'index'])->name('role.view');
    Route::get('/access-hub', [\App\Http\Controllers\apps\RoleController::class, 'index'])->name('app-access-hub');
    Route::post('/access-hub/roles', [\App\Http\Controllers\apps\RoleController::class, 'store'])->name('app-access-roles-store');
    Route::delete('/access-hub/roles/{id}', [\App\Http\Controllers\apps\RoleController::class, 'destroy'])->name('app-access-roles-destroy');
    Route::post('/access-hub/roles/sync-permissions', [\App\Http\Controllers\apps\RoleController::class, 'syncPermissions'])->name('app-access-roles-sync-permissions');
    Route::get('/permissions', [\App\Http\Controllers\apps\AccessPermission::class, 'index'])->name('app-access-permission-list');
    Route::get('/app/permissions', [\App\Http\Controllers\apps\AccessPermission::class, 'index'])->name('permissions.index');
    Route::get('/permissions/data', [\App\Http\Controllers\apps\AccessPermission::class, 'list'])->name('app-access-permission-data');
    Route::get('/app/access-permission/list', [\App\Http\Controllers\apps\AccessPermission::class, 'list']);
    Route::post('/permissions', [\App\Http\Controllers\apps\AccessPermission::class, 'store'])->name('app-access-permission-store');
    Route::delete('/permissions/{id}/delete', [\App\Http\Controllers\apps\AccessPermission::class, 'destroy'])->name('app-access-permission-destroy');
    
    // Suppliers Management
    Route::get('/suppliers', [\App\Http\Controllers\apps\SupplierController::class, 'index'])->name('app-suppliers');
    Route::post('/suppliers', [\App\Http\Controllers\apps\SupplierController::class, 'store'])->name('app-suppliers-store');
    Route::put('/suppliers/{id}', [\App\Http\Controllers\apps\SupplierController::class, 'update'])->name('app-suppliers-update');
    Route::delete('/suppliers/{id}', [\App\Http\Controllers\apps\SupplierController::class, 'destroy'])->name('app-suppliers-delete');

    // Purchase Management
    Route::get('/purchases', [\App\Http\Controllers\apps\PurchaseOrderController::class, 'index'])->name('app-purchases');
    Route::post('/purchases', [\App\Http\Controllers\apps\PurchaseOrderController::class, 'store'])->name('app-purchases-store');
    Route::post('/purchases/{id}/received', [\App\Http\Controllers\apps\PurchaseOrderController::class, 'markReceived'])->name('app-purchases-received');

    // Reports Suite
    Route::get('/reports', [\App\Http\Controllers\apps\ReportController::class, 'index'])->name('app-reports');
    Route::get('/reports/export-csv', [\App\Http\Controllers\apps\ReportController::class, 'exportCsv'])->name('app-reports-export-csv');

    // Global Search (Ctrl + K)
    Route::get('/global-search', [\App\Http\Controllers\apps\GlobalSearchController::class, 'search'])->name('app-global-search');

    // Calendar & Apps
    Route::get('/calendar', [Calendar::class, 'index'])->name('app-calendar');

    // Pages & Modals
    Route::get('/faq', [Faq::class, 'index'])->name('pages-faq');
    Route::get('/modals', [ModalExample::class, 'index'])->name('modal-examples');

    // User Profile Hub
    Route::get('/profile', [\App\Http\Controllers\pages\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/user/profile', [\App\Http\Controllers\pages\ProfileController::class, 'show'])->name('pages-profile-user');

    // Account Settings Modules
    Route::get('/account/settings', [\App\Http\Controllers\pages\ProfileController::class, 'edit'])->name('pages-account-settings-account');
    Route::get('/pages/account-settings-account', [\App\Http\Controllers\pages\ProfileController::class, 'edit']);
    Route::post('/account/settings/profile', [\App\Http\Controllers\pages\ProfileController::class, 'update'])->name('account-settings-profile-update');
    Route::post('/account/settings/photo', [\App\Http\Controllers\pages\ProfileController::class, 'updatePhoto'])->name('account-settings-photo-update');
    Route::post('/account/settings/photo-remove', [\App\Http\Controllers\pages\ProfileController::class, 'removePhoto'])->name('account-settings-photo-remove');

    // Account Security & Password
    Route::get('/account/security', [\App\Http\Controllers\pages\ProfileController::class, 'security'])->name('pages-account-settings-security');
    Route::get('/pages/account-settings-security', [\App\Http\Controllers\pages\ProfileController::class, 'security']);
    Route::post('/account/settings/password', [\App\Http\Controllers\pages\ProfileController::class, 'updatePassword'])->name('account-settings-password-update');

    // Account Notifications
    Route::get('/account/notifications', [\App\Http\Controllers\pages\ProfileController::class, 'notifications'])->name('pages-account-settings-notifications');
    Route::get('/pages/account-settings-notifications', [\App\Http\Controllers\pages\ProfileController::class, 'notifications']);
    Route::post('/account/settings/notifications', [\App\Http\Controllers\pages\ProfileController::class, 'updateNotifications'])->name('account-settings-notifications-update');

    // Account Connections
    Route::get('/pages/account-settings-connections', [\App\Http\Controllers\pages\AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');

    // SaaS Billing & Invoices Hub
    Route::get('/billing', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'index'])->name('billing.index');
    Route::get('/pages/account-settings-billing', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'index'])->name('pages-account-settings-billing');
    Route::post('/billing/subscribe', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'subscribe']);
    Route::post('/billing/cancel', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'cancel']);
    Route::post('/billing/resume', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'resume'])->name('billing-resume');
    Route::get('/billing/invoices/{id}/preview', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'invoicePreview'])->name('billing-invoice-preview');
    Route::get('/billing/invoices/{id}/print', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'invoicePreview'])->name('billing-invoice-print');

    // Secure Logout
    Route::post('/logout', [LoginBasic::class, 'logout'])->name('logout');
});

// Authentication Routes (Public)
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::post('/auth/login-basic', [LoginBasic::class, 'store'])->name('auth-login-basic-store');
Route::post('/login', [LoginBasic::class, 'store'])->name('login-store');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');

// Public Billing Webhooks
Route::post('/saas/billing/webhook', [\App\Http\Controllers\apps\SaaS\SubscriptionController::class, 'webhook'])->name('billing-webhook');

