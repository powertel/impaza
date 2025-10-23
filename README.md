# Impaza

Impaza is a Laravel 9 application for operations and fault management across NOC, technicians, departments, finance, and customers. It tracks faults through assignment, rectification, and clearance with activity logs.

## Features
- Role-based access control (Spatie Permissions)
- Fault lifecycle: create, assign, rectify, clear
- Sections/departments, technicians, materials, permits
- Customers, links, POPs, stores, cities/suburbs
- Remarks, stage logs, and seedable statuses/RFO

## Tech Stack
- PHP 8.0+, Laravel 9
- MySQL 8, Redis
- Nginx + PHP-FPM (Docker)
- Laravel Mix + Vue 2 + Bootstrap 5

## Quick Start (Local)
Prereqs: PHP 8.0+, Composer, Node.js + npm, MySQL 8

1. Copy env: `cp ".env - Copy.example" .env` (Windows: `copy ".env - Copy.example" ".env"`)
2. Configure `.env` (`APP_URL`, `DB_*`, etc.)
3. Install deps: `composer install` and `npm install`
4. App key: `php artisan key:generate`
5. Migrate + seed: `php artisan migrate --seed`
6. Build assets: `npm run dev`
7. Serve: `php artisan serve` → http://localhost:8000

## Docker Development
1. Copy env: `cp .env-docker.example .env`
2. Build & start: `docker compose up -d --build`
3. Dev overlay (live code mounts):
   `docker compose -f compose.yaml -f compose.dev.yaml up -d`
4. App: http://localhost:8080
5. phpMyAdmin: http://localhost:8081 (DB host: `mysql`, user `root`, pass `root`)
   - Host DB port: `3307`
   - Redis host port: `6380`

## Infobip WhatsApp
Configure these env vars for WhatsApp notifications via Infobip:
- `INFOBIP_BASE_URL` (e.g., `https://api.infobip.com` or your regional subdomain)
- `INFOBIP_API_KEY` (Infobip API key)
- `INFOBIP_WHATSAPP_NUMBER` (your sender number/MSISDN, include `+`)
- `INFOBIP_DEFAULT_COUNTRY_CODE` (used for normalizing local numbers, e.g., `+263`)
- `INFOBIP_STATUS_TEMPLATE` (approved WhatsApp template name used for customer notifications)

Template placeholders used by customer notifications (default order):
- `[fault_ref_number, status_desc]`

Test helpers:
- Send text: `POST /test-infobip-send` (`phone`, `message`)
- Send template: `POST /test-infobip-send-template` (`to`, `templateName`, `language`, optional `placeholders`)
- Check status: `GET /test-infobip-status?messageId=...`

Notes:
- Templates bypass 24-hour session limits; text messages may fail with `7010 No session` if inactive.
- Ensure recipients are in E.164 format; the app normalizes common local formats using `INFOBIP_DEFAULT_COUNTRY_CODE`.

## Testing
- Run tests: `php artisan test`

## Common Tasks
- Seed admin user: `php artisan db:seed --class=CreateAdminUserSeeder`
- Clear caches: `php artisan optimize:clear`

## Project Structure
- `app/` domain models, services (e.g., `Services/FaultLifecycle.php`)
- `resources/views/` pages (faults, assign, clear_faults, technicians, finance)
- `routes/` HTTP & API routes
- `database/seeders/` initial data
- `public/` compiled assets
- `docker/` PHP-FPM & Nginx images

## Contributing
Open issues or PRs and follow Laravel best practices.
