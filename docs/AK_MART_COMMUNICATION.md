# AK-Mart Unified Communication Center Architecture

## 1. Overview
The AK-Mart Unified Communication Center powers **Email**, **WhatsApp Business Cloud API**, and **In-App notifications** with enterprise-grade resilience, audit logging, template interpolation, and customer opt-out compliance.

---

## 2. Core Architecture

### A. CommunicationService (`App\Services\CommunicationService`)
- **Template Placeholders**: Interpolates `{{customer_name}}`, `{{order_number}}`, `{{order_total}}`, `{{tracking_number}}`, `{{carrier}}`, `{{store_name}}`, `{{discount_code}}`.
- **Preference Governance**: Enforces `marketing_opt_out` on customers. Transactional notifications (order confirmations, shipping updates, return approvals) always deliver; promotional broadcasts automatically skip opted-out users.
- **Fail-Safe Isolation**: Communication delivery failures (e.g. SMTP timeout or Meta API downtime) **NEVER roll back or interrupt successful e-commerce transactions**. The message status is recorded as `failed` in `communication_logs` for automated background retry.

---

## 3. Communication Channels Supported

1. **Email Engine**:
   - Transactional receipts, password resets, shipping notifications, and review invitations.
   - Bulk broadcast capability with audience filtering.
2. **WhatsApp Business Cloud API**:
   - Direct-to-consumer order tracking notifications.
   - Return approval and refund confirmations.
   - High-delivery marketing alerts to opted-in customers.
3. **In-App Notification Hub**:
   - Real-time bell dropdown notifications for admin and store staff.

---

## 4. UI Dashboard (`/communication`)
- **Live Logs**: Real-time status inspection with recipient, channel, payload, message ID, and delivery state.
- **Quick Dispatcher**: Test and direct-message tool for administrators.
- **Template Manager**: Edit standard transactional templates and variable bindings.
- **Marketing Campaign Manager**: Broadcast campaigns to custom audience segments (All, VIP, Inactive, Abandoned Carts).
