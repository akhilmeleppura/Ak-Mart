# 🚀 AKMART — DEPLOYMENT, DEVOPS & PRODUCTION READINESS GUIDE

**Document ID**: AKMART-DOC-OPS-009  
**Deployment Model**: Docker Compose / Linux Nginx + PHP-FPM 8.2+ / Supervisor Queue Workers  
**Date**: August 2026  

---

## 1. PRODUCTION DEPLOYMENT TOPOLOGY

```text
               ┌────────────────────────────────────────────────────────┐
               │              LOAD BALANCER / REVERSE PROXY             │
               │               (Nginx / Cloudflare SSL)                 │
               └───────────────────────────┬────────────────────────────┘
                                           │
                                           ▼
               ┌────────────────────────────────────────────────────────┐
               │                  AKMART WEB SERVERS                    │
               │  • PHP 8.2+ FPM Workers (OPcache Enabled)              │
               │  • Laravel 12 Bootstrap (Config & Route Cached)        │
               │  • Vite Production Assets Bundled in `public/build`    │
               └───────────────────────────┬────────────────────────────┘
                                           │
                        ┌──────────────────┴──────────────────┐
                        ▼                                     ▼
      ┌─────────────────────────────────┐   ┌─────────────────────────────────┐
      │       DATABASE & CACHING        │   │         ASYNC WORKERS           │
      │ • MariaDB / MySQL 8.0 Primary   │   │ • Supervisor: Queue Listeners   │
      │ • Redis 7 (Session/Cache/Locks) │   │ • Cron: `php artisan schedule`  │
      └─────────────────────────────────┘   └─────────────────────────────────┘
```

---

## 2. PRODUCTION DEPLOYMENT CHECKLIST

```bash
# 1. Install optimized composer dependencies
composer install --no-dev --optimize-autoloader

# 2. Compile frontend production bundle
npm run build

# 3. Cache application configurations and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Run database migrations safely
php artisan migrate --force

# 5. Restart queue background workers
php artisan queue:restart

# 6. Verify system health
php artisan system:health
```

---

## 3. SCHEDULED TASKS & CRON

Configure crontab:
```text
* * * * * cd /path-to-akmart && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled jobs handled:
- Daily Business Brief computation (06:00 AM)
- Low stock reorder alerts & notification dispatches (Hourly)
- Abandoned cart recovery triggers (Every 30 mins)
- Database backup snapshot generation (Daily at 02:00 AM)
