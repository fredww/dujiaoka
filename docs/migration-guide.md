# Upgrade and Migration Guide: Laravel 6 → Laravel 10, Dcat → Filament, Payments Consolidation

## Overview
- Objective: Migrate the project from Laravel 6.x to Laravel 10.x+, replace Dcat Admin with Filament, and consolidate payments to PayPal and Stripe.
- Scope: Framework upgrade, admin UI migration, RBAC consolidation, payment SDK upgrades, asset pipeline migration, testing/CI/CD hardening, and rollback safety.

## Prerequisites
- Runtime: PHP 8.1+ (with necessary extensions), Composer 2.
- Node.js 18+ for Vite, npm 9+.
- Staging environment and database backup capability.
- Sandbox accounts: Stripe and PayPal (client/secret keys and webhook endpoints).

## High-Level Plan
1. Prepare environment and branches; freeze production.
2. Incrementally upgrade Laravel (6 → 8 → 9 → 10) or direct jump with iterative fixes.
3. Stand up Filament in parallel; replicate Dcat features by modules.
4. Replace legacy payments: keep Stripe and PayPal only; remove other gateways.
5. Migrate assets from Mix/webpack to Vite.
6. Expand tests and adapt CI/CD; run canary release and monitor.
7. Decommission Dcat and legacy gateways; finalize cleanup.

## Dependencies Matrix (Target Versions)
- laravel/framework: ^10
- spatie/laravel-permission: ^6
- filament/filament: ^3
- spatie/laravel-ignition: ^2
- nunomaduro/collision: ^7
- phpunit/phpunit: ^10
- stripe/stripe-php: ^14
- paypal/paypal-checkout-sdk: ^1
- vite + laravel-vite-plugin

## Dcat → Filament Feature Mapping
- Admin Dashboard: Dcat dashboards → Filament Widgets for KPIs (orders, revenue, stock).
- RBAC: Dcat roles/permissions → Spatie roles/permissions; Filament Policies/Gates for resource-level control.
- Tables: Dcat grids → Filament Tables (search, sort, filters, bulk actions, export).
- Forms: Dcat forms → Filament Forms (validation, dependent selects, repeaters, rich text if needed).
- Actions: Dcat row/bulk actions → Filament `Tables\Actions` with queued jobs and notifications.
- Routing: Admin routes under Filament panel (e.g., `/admin`), public storefront routes unchanged.

## Database Adjustments
- RBAC: Install Spatie tables (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`).
- Virtual Cards (carmis): Ensure fields for status (`available`, `reserved`, `sold`), encryption for `card_data`, audit logs (`card_events`).
- Payments: Add `payments` table if missing with `provider`, `provider_id`, `amount`, `currency`, `status`, `metadata`, `refunded_amount`, `idempotency_key`.
- Webhooks: Add delivery logs for Stripe and PayPal events.

## Payments Consolidation & Upgrades
- Remove gateways: Alipay, WeChat, Mapay, Paysapi, PayJS, Yipay, VPay, Coinbase, Epusdt, TokenPay.
- Stripe:
  - Use Checkout Session or PaymentIntent + Payment Element.
  - Enable Apple Pay via Payment Request Button (+ domain verification).
  - Webhooks: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`, `charge.refunded`.
  - SCA/3DS handled by PaymentIntent.
- PayPal:
  - Use Orders API v2: create → approve → capture.
  - Webhooks: `PAYMENT.CAPTURE.COMPLETED`, `PAYMENT.CAPTURE.DENIED`, `PAYMENT.CAPTURE.REFUNDED`.
- Multi-currency: Configure currency per market; avoid storing PAN; exchange via trusted APIs only when necessary.

## Routing and Middleware Modernization
- Use class-based routes (`[Controller::class, 'method']`) after Laravel 8+.
- Remove `RouteServiceProvider` `$namespace` reliance; adopt modern `routes/web.php` conventions.
- Replace `CheckForMaintenanceMode` with `PreventRequestsDuringMaintenance`.

## Security & Compliance
- PCI DSS (SAQ A target): Never store card data; offload card handling to Stripe/PayPal.
- Transport security: TLS 1.2+, secure headers, strict CSP for admin and checkout.
- Secrets: Rotate and store in env (`STRIPE_SECRET`, `PAYPAL_CLIENT`, `PAYPAL_SECRET`).
- Virtual card security: `Crypt::encryptString` at rest, one-time reveal tokens, audit logs and rate limits.

## Interface Compatibility & Deprecations
- Public storefront endpoints remain.
- Admin endpoints move to Filament; if API exists, provide shims or versioned endpoints (`/api/admin/v2`).
- Deprecation schedule: Announce legacy admin shutdown after parity and acceptance.

## Testing & CI/CD Plan
- Unit: card generation & encryption, reservation/allocation services, payment service abstractions.
- Feature: checkout success/failure, webhook idempotency, refunds, admin bulk operations.
- Security: RBAC, reveal token flow, rate limits, secrets management.
- Performance: order queries and inventory lists under load.
- CI: GitHub Actions with PHP 8.1 and Node 18; run `composer test`, `npm run build` via Vite; cache deps.

## Rollback Strategy
- Keep `release/laravel6` branch and container image; enable fast DNS/traffic switch.
- DB snapshots with PITR; avoid destructive migrations.
- Feature flags (`config/upgrade.php`) to toggle new flows.
- Blue/green deployment and canary release with observability (errors, payment success rate).

## Detailed Step-by-Step
1. Environment Readiness
   - Upgrade PHP to 8.1+, Node to 18+.
   - Add feature flags in `config/upgrade.php` and `.env`.
2. Framework Upgrade
   - Bump composer dependencies; replace ignition/collision/phpunit.
   - Update middleware and `RouteServiceProvider`, convert routes gradually.
   - Fix type hints and signatures where required.
3. Filament Deployment
   - Install Filament; scaffold `CarmisResource`, `OrderResource`, `PayResource`, `UserResource`.
   - Install Spatie RBAC; migrate roles/permissions; protect resources via policies.
   - Replicate Dcat features page-by-page, verify parity.
4. Payment Modernization
   - Stripe: integrate Checkout/PaymentIntent; add webhooks & refunds.
   - PayPal: integrate Orders API; add webhooks & refunds.
   - Remove legacy gateways: controllers, routes, admin forms, translations.
5. Frontend Migration
   - Switch to Vite; update Blade to `@vite`.
6. Tests & CI/CD
   - Expand tests; set up Actions; run canary.
7. Cleanup & Decommission
   - Remove Dcat and unused assets; confirm acceptance criteria.

## Acceptance Criteria
- Laravel 10 app boots, routes and middleware updated, tests pass.
- Filament admin provides parity for dashboards, tables, forms, actions, and RBAC.
- Only Stripe and PayPal available; end-to-end payment success, refund works; webhooks processed reliably.
- Virtual card lifecycle secure and auditable.
- CI/CD green; rollback plan validated.

## Work Items Checklist
- [ ] Upgrade framework & dependencies per matrix
- [ ] Replace middleware and routing approach
- [ ] Install Filament & Spatie RBAC
- [ ] Implement resources: Carmis, Order, Pay, User
- [ ] Migrate roles/permissions
- [ ] Implement Stripe modern flow (+ Apple Pay)
- [ ] Implement PayPal Orders API v2
- [ ] Remove legacy gateways
- [ ] Switch to Vite assets
- [ ] Add tests & webhooks
- [ ] Configure CI/CD and observability
- [ ] Run canary and finalize cleanup