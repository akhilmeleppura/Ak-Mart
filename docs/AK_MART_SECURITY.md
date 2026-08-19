# AK-Mart 2026 Security Architecture & RBAC Guide

## 1. 👑 Supreme Admin Architecture
The Supreme Admin is the highest-level operational identity with permanent, hardcoded, universal bypass rights across every system gate, authorization policy, and database tenant filter.

- **Universal Bypass Mechanism**: Handled in `AppServiceProvider::boot()` via `Gate::before()`:
  ```php
  Gate::before(function ($user, $ability) {
      if ($user && ($user->is_supreme_admin || $user->is_super_admin)) {
          return true; // Unconditional bypass
      }
  });
  ```
- **Universal Credentials**:
  - **Email**: `supreme@ak-mart.com`
  - **Password**: `supreme123`

---

## 2. Authentication & Data Protection
- **Multi-Factor Authentication**: TOTP (Time-Based One-Time Password) Two-Factor Authentication with encrypted secrets and recovery codes.
- **CSRF & XSS Protection**: Enforced on all non-API web routes via Laravel CSRF tokens and Blade automatic HTML entity escaping.
- **HMAC SHA-256 Webhook Signatures**: Outbound developer webhook payloads are signed using SHA-256 HMAC headers (`X-AKMart-Signature`).
- **Database & Passwords**: Passwords hashed using Bcrypt with default cost factor of 12.
- **Immutable Audit Logging**: Traceable security and operational events recorded in `audit_logs` and `stock_movements`.
