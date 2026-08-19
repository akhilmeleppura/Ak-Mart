# AK-Mart — Shared Components, Modals & Partials Classification

**Total Classified Auxiliary & Shared Components:** 74

This document tracks all reusable Blade partials, modular dialogs, sidebars, and components that do not directly extend a top-level layout but are imported via `@include` or `<x-component>`.

| File Path | Classification | Role in Architecture |
|-----------|----------------|----------------------|
| `resources/views/api/api-token-manager.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/components/action-message.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/action-section.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/application-logo.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/application-mark.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/authentication-card-logo.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/authentication-card.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/banner.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/button.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/checkbox.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/confirmation-modal.blade.php` | 🧩 PARTIAL/MODAL | Interactive Modal / Dialog |
| `resources/views/components/confirms-password.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/danger-button.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/dialog-modal.blade.php` | 🧩 PARTIAL/MODAL | Interactive Modal / Dialog |
| `resources/views/components/dropdown-link.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/dropdown.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/input-error.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/input.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/label.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/modal.blade.php` | 🧩 PARTIAL/MODAL | Interactive Modal / Dialog |
| `resources/views/components/nav-link.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/responsive-nav-link.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/secondary-button.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/section-border.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/section-title.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/validation-errors.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/components/welcome.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/content/apps/app-user-view-billing.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/content/apps/saas/sitemap.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/content/apps/_settings-sidebar.blade.php` | 🧩 PARTIAL/MODAL | Section Navigation Sidebar |
| `resources/views/content/pages/pages-account-settings-billing.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/emails/dunning-reminder.blade.php` | 🟡 AUXILIARY | Email Notification Template |
| `resources/views/HS/standard-datatable.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/layouts/commonMaster.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/layoutMaster.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/menu/horizontalMenu.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/menu/horizontalMenuBackUp.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/menu/submenu.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/menu/submenuBackup.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/menu/verticalMenu.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/menu/verticalMenuBackUp.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/navbar/navbar-front.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/navbar/navbar.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/scripts.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/scriptsFront.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/scriptsIncludes.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/scriptsIncludesFront.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/styles.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/layouts/sections/stylesFront.blade.php` | 🟡 AUXILIARY | Layout Template Container |
| `resources/views/profile/delete-user-form.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/profile/logout-other-browser-sessions-form.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/profile/two-factor-authentication-form.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/profile/update-password-form.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/profile/update-profile-information-form.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `resources/views/_partials/macros.blade.php` | 🧩 PARTIAL/MODAL | Reusable Component |
| `resources/views/_partials/_modals/modal-pricing.blade.php` | 🧩 PARTIAL/MODAL | Interactive Modal / Dialog |
| `resources/views/_partials/_modals/modal-select-payment-methods.blade.php` | 🧩 PARTIAL/MODAL | Interactive Modal / Dialog |
| `resources/views/_partials/_modals/modal-select-payment-providers.blade.php` | 🧩 PARTIAL/MODAL | Interactive Modal / Dialog |
| `resources/views/_partials/_modals/modal-share-project.blade.php` | 🧩 PARTIAL/MODAL | Interactive Modal / Dialog |
| `resources/views/_partials/_modals/modal-two-factor-auth.blade.php` | 🧩 PARTIAL/MODAL | Interactive Modal / Dialog |
| `resources/views/_partials/_search-modal.blade.php` | 🧩 PARTIAL/MODAL | Interactive Modal / Dialog |
| `Modules/Accounting/resources/views/accounting/accounting-menu.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `Modules/Accounting/resources/views/trialbalance/trial-balance-pdf.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `Modules/Billing/resources/views/components/layouts/master.blade.php` | 🧩 PARTIAL/MODAL | Layout Template Container |
| `Modules/Billing/resources/views/index.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `Modules/Billing/resources/views/payment-options/sub-menu.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `Modules/Ecommerce/resources/views/components/layouts/master.blade.php` | 🧩 PARTIAL/MODAL | Layout Template Container |
| `Modules/Ecommerce/resources/views/index.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `Modules/General/resources/views/components/layouts/master.blade.php` | 🧩 PARTIAL/MODAL | Layout Template Container |
| `Modules/General/resources/views/index.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `Modules/Permission/resources/views/components/layouts/master.blade.php` | 🧩 PARTIAL/MODAL | Layout Template Container |
| `Modules/Permission/resources/views/index.blade.php` | 🟡 AUXILIARY | Reusable Component |
| `Modules/SampleModule/resources/views/components/layouts/master.blade.php` | 🧩 PARTIAL/MODAL | Layout Template Container |
| `Modules/SampleModule/resources/views/test/sample-menu.blade.php` | 🟡 AUXILIARY | Reusable Component |
