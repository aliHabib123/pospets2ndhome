# WooCommerce integration (POS side)

Everything for this integration lives under `app/Woo/` plus a dedicated set of
tables. The rest of the POS is untouched except **one line** in `config/app.php`
that registers `App\Woo\WooServiceProvider`. Remove that line to fully disable it.

## What was added

| Piece | Location |
|---|---|
| Service provider (routes, observers, commands) | `app/Woo/WooServiceProvider.php` |
| Config accessor (`woo_settings` table) | `app/Woo/WooSettings.php` |
| Core service (enqueue, stock aggregate, HMAC, push) | `app/Woo/WooSync.php` |
| Inbound API (export / order / map) | `app/Woo/Http/WooController.php` + `app/Woo/routes.php` |
| Observers (auto-catch every stock/product change) | `app/Woo/Observers/*` |
| Outbox drainer + reconcile | `app/Woo/Console/*` |
| Models | `app/Woo/Models/*` |
| Migrations (isolated subfolder) | `database/migrations/woo/*` |

Tables: `woo_settings`, `woo_sync_queue`, `woo_orders`, and a nullable
`items.woo_product_id` column.

## Endpoints (under `/api/woo`, no session/CSRF — auth is the shared secret)

- `GET  /api/woo/products` — catalogue export for the plugin's import button. Auth: `Authorization: Bearer <secret>`.
- `POST /api/woo/order` — a web order was placed; decrements stock. Auth: Bearer **and** `X-POS-Signature` (HMAC of the raw body). Idempotent on `woo_order_id`.
- `POST /api/woo/map` — plugin reports `pos_item_id => woo_product_id` after import.

## Configuration

Settings live in the `woo_settings` key/value table (seeded with safe defaults;
`sync_enabled` starts at `no`, so nothing happens until you turn it on):

| key | meaning |
|---|---|
| `sync_enabled` | `yes` to activate. Master switch. |
| `wp_url` | WordPress site root, e.g. `https://yourstore.com` |
| `shared_secret` | Long random string. Must match the plugin's shared secret. |
| `locations` | Ordered JSON array of location ids that make up web stock, e.g. `[6,1,3]`. Stock is decremented in this order on a web order. |
| `system_user_id` | Optional. User id to attribute web-order inventory rows to. Defaults to the lowest user id. |

Set them, e.g.:

```sql
UPDATE woo_settings SET value='https://yourstore.com' WHERE `key`='wp_url';
UPDATE woo_settings SET value='REPLACE_WITH_LONG_RANDOM' WHERE `key`='shared_secret';
UPDATE woo_settings SET value='[6,1,3]' WHERE `key`='locations';
UPDATE woo_settings SET value='yes' WHERE `key`='sync_enabled';
```

## Cron (the outbox worker)

Add a system cron so queued stock/price changes get pushed to WooCommerce. The
database is **not** migration-managed, so also note the `--path` for migrations.

```cron
# every minute: push queued stock/price changes to Woo
* * * * * cd /Applications/MAMP/htdocs/pospets2ndhome/staging && /Applications/MAMP/bin/php/php7.4.33/bin/php artisan woo:sync >> storage/logs/woo-sync.log 2>&1

# nightly safety net: re-push current stock for every mapped item
0 3 * * * cd /Applications/MAMP/htdocs/pospets2ndhome/staging && /Applications/MAMP/bin/php/php7.4.33/bin/php artisan woo:reconcile >> storage/logs/woo-sync.log 2>&1
```

## Migrations

These migrations are in an isolated subfolder so a blanket `artisan migrate`
(which would try to run this project's un-tracked legacy migrations) never touches
them. Run only these:

```bash
php artisan migrate --path=database/migrations/woo --force
php artisan migrate:status --path=database/migrations/woo   # check
```

## Safety / isolation notes

- `sync_enabled=no` → observers, commands and outbound pushes are all no-ops.
- The observers wrap everything in try/catch and never throw into a POS request,
  so a sale/receiving/transfer can never fail because of this integration.
- Stock is always pushed to Woo as an **absolute** value (never a delta), so there
  is no risk of a feedback loop with the order webhook.
- Inbound writes only touch `item_quantities` / `inventories` exactly like
  `SaleController` does (audit row tagged `WEB<order_id>`), plus the `woo_*` tables.

## Environment notes & issues faced (read this first in a new session)

These are real gotchas hit while building this on the dev machine. They will save
a future session (possibly on another PC) a lot of time.

1. **MAMP MySQL is on port 8889, not 3306.** The CLI client lives at
   `/Applications/MAMP/Library/bin/mysql80/bin/mysql`, creds `root` / `root`.
   Example:
   ```bash
   /Applications/MAMP/Library/bin/mysql80/bin/mysql -uroot -proot -h127.0.0.1 -P8889 pospets2ndhome -e "SHOW TABLES LIKE 'woo_%';"
   ```
   (Port 3306 refuses the connection — MySQL simply isn't there.)

2. **The database is NOT migration-managed.** `php artisan migrate:status` shows
   **every** migration as "N" (not run), including the 2018 ones — the schema was
   imported directly. So **never run a blanket `php artisan migrate`**: it would try
   to run the legacy migrations (`create_permission_tables`, etc.) against tables
   that already exist and break. Always scope to this integration:
   ```bash
   php artisan migrate --path=database/migrations/woo --force
   php artisan migrate:status --path=database/migrations/woo
   ```
   Migrations live in `database/migrations/woo/` (a subfolder Laravel 5.5 does not
   scan by default) specifically to keep them out of any accidental blanket run.
   `--force` is required because `APP_ENV` is `production`.

3. **Why a dedicated `woo_settings` table instead of the shared `settings` table:**
   `settings.value` is `varchar(50)` (too short for a URL or secret) and its
   `read_only` column is `NOT NULL` with no default. Rather than alter a table the
   whole app reads, the integration uses its own `woo_settings` (key/text) table.

4. **Apache runs PHP 8.x, but the CLI is PHP 7.4.** Laravel 5.5 on PHP 8 emits a
   flood of `Deprecated:` notices, and `display_errors` is on, so **HTTP responses
   from the browser/curl are polluted with deprecation HTML that buries the JSON**
   (the request still succeeds — HTTP 200). This is pre-existing, not our code.
   To test our endpoints cleanly, **boot the kernel from the CLI (PHP 7.4)** instead
   of curling Apache. Pattern that works (used during development):
   ```php
   error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
   require '<staging>/vendor/autoload.php';
   $app = require '<staging>/bootstrap/app.php';
   $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
   $res = $kernel->handle(Illuminate\Http\Request::create('/api/woo/products','GET'));
   ```
   Wrap any mutating test (like the order decrement) in `DB::beginTransaction()` …
   `DB::rollBack()` so no real inventory changes.

5. **`php artisan route:list` is broken (pre-existing).** It throws
   `Class App\Http\Controllers\Auth\PasswordController does not exist` because a
   legacy route references a controller that was deleted. This means you **cannot
   use `route:list` to verify the woo routes** — they still register and work at
   runtime. Verify by dispatching a request (see #4) instead.

6. **Domain facts baked into the code** (confirmed against the live DB): tracked
   products are `type_id = 1`; the website price is `items.selling_price_usd` (USD);
   `inventories.user_id` is `NOT NULL` (hence `system_user_id` / lowest-user fallback).

7. **The outbox only queues pushes for *mapped* items** (`items.woo_product_id`
   not null). Before the plugin's import has run and called `/api/woo/map`, stock
   changes correctly produce **no** queue rows. That is expected, not a bug.

## Flow recap

1. Plugin "Import" → `GET /api/woo/products` → creates draft products → plugin calls
   `POST /api/woo/map` → `items.woo_product_id` filled in.
2. Any stock/price change in the POS → observer queues a row → `woo:sync` pushes the
   absolute stock/price to the plugin.
3. Web order → plugin `POST /api/woo/order` → POS decrements across `locations` and
   writes an audit row; idempotent per order.
