# InnEase CRM System

Multi-tenant hotel/property CRM built on Laravel 12. One platform serves a Master (super admin)
plus three tenants with different feature tiers, and every CRM table is isolated by `tenant_id`.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

## Demo accounts

All seeded accounts use the password `password`.

| Perspective | Email | Notes |
| --- | --- | --- |
| Master / Super Admin | `master@innease.test` | Tenant selector in the header, global BI, tenant + subscription CRUD |
| Tenant A – Azure Bay Hotel | `admin@azurebay.test`, `manager@azurebay.test`, `staff@azurebay.test` | Transactions and data collection |
| Tenant B – Bluewater Suites | `admin@bluewater.test`, `manager@bluewater.test`, `staff@bluewater.test` | BI dashboard and action board |
| Tenant C – Coral Group of Hotels | `admin@coral.test`, `manager@coral.test`, `staff@coral.test` | Multi-branch, full BI, operational task list |

## Tenant isolation

- `Guests`, `Reservations`, `Feedback`, `Sales`, `Interactions`, `Actions`, `Promotions` and
  `Branches` all carry `tenant_id` (branch-aware tables also carry `branch_id`).
- `App\Models\Concerns\BelongsToTenant` adds a global scope driven by `App\Support\TenantContext`,
  so tenant users only ever read or write their own rows — including route model binding, which
  returns 404 for another tenant's record.
- `App\Http\Middleware\ResolveTenant` resolves the tenant from the signed-in user, or from the
  Master's header selector, and blocks tenant users whose organization is suspended.
- A Master with no tenant selected gets the global platform perspective.

## Modules

| Area | Route | Highlights |
| --- | --- | --- |
| Dashboards | `/dashboard` | Tenant-specific BI; every KPI card drills down (e.g. `/feedback?status=unresolved`) |
| Master admin | `/master/tenants`, `/master/subscriptions` | Tenant CRUD, activate/suspend, plans, renewal dates, feature access |
| Transactions | `/reservations`, `/sales` | Reservation CRUD, check-in/check-out (auto room charge), POS entries |
| Data collection | `/guests`, `/feedback`, `/interactions` | Guest profiles, feedback, inquiries / complaints / special requests |
| Actions | `/actions` | Open / In Progress / Resolved board, assignment, priorities |
| Branching (Tenant C) | `/branches` | Branch CRUD plus a header branch filter that scopes all data and analytics |
| Sales report | `/reports/sales?period=daily\|weekly\|monthly` | Chart plus breakdown and transaction tables |
| Promotions | `/promotions` | `implemented_by`, required `reason_for_implementation`, `approved_by`, Pending/Approved/Rejected workflow |

Promotions are only active once approved and inside their date range; editing an approved
promotion sends it back to Pending. Only `master`, `admin` and `manager` roles may approve or
reject, and rejections require review notes.

The original library management module remains available under `/library`.

## Tests

```bash
php artisan test        # tenant isolation, promotion workflow, page smoke tests
vendor/bin/pint --dirty # code style
```
