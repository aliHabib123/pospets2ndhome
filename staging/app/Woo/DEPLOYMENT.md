# WooCommerce integration — as-built deployment runbook

This records exactly how the integration was deployed and verified in production, so
a future session (or another person) can reproduce or troubleshoot it. For the design
and reference details see `README.md` in this folder.

## As-built environment

| Thing | Value |
|---|---|
| POS (Laravel) app | `https://pos.pets2ndhome.com/staging/public` (docroot maps to `~/pos.pets2ndhome.com`, Laravel public is `staging/public`) |
| POS server path | `/home5/cileyaco/pos.pets2ndhome.com/staging` |
| POS PHP (CLI/cron) | `/usr/local/bin/php` (PHP 7.4 for this domain) |
| WooCommerce store | `https://pets2ndhome.com` (PHP 8.3) |
| Web location | location id **6** — "Sin el Fil" (`woo_settings.locations = [6]`) |
| Price field | `items.selling_price_usd` (USD) |
| Shared secret | stored in `woo_settings.shared_secret` **and** the plugin settings — NOT written in this doc on purpose. Must be byte-identical on both sides. |

## 1. Files deployed (POS)

- `staging/app/Woo/**` (the whole folder)
- `staging/database/migrations/woo/**`
- one line added to `staging/config/app.php` registering `App\Woo\WooServiceProvider::class`

## 2. Migrations (POS)

The DB is **not** migration-managed, so migrations were run scoped to the woo subfolder
only (never a blanket `migrate`):

```bash
cd /home5/cileyaco/pos.pets2ndhome.com/staging
php artisan migrate:status --path=database/migrations/woo   # verify: 4 rows, all N
php artisan migrate --path=database/migrations/woo --force   # --force: APP_ENV=production
```

Created: `woo_settings`, `woo_sync_queue`, `woo_orders`, and `items.woo_product_id`.

## 3. POS configuration

```sql
UPDATE woo_settings SET value='https://pets2ndhome.com'  WHERE `key`='wp_url';
UPDATE woo_settings SET value='<shared-secret>'          WHERE `key`='shared_secret';
UPDATE woo_settings SET value='[6]'                      WHERE `key`='locations';
UPDATE woo_settings SET value='yes'                      WHERE `key`='sync_enabled';
```

After editing config or routes: `php artisan config:clear` (and `route:clear`).

## 4. Plugin install + configuration (WordPress)

1. Upload/activate **POS WooCommerce Sync** (needs WooCommerce active).
2. **Settings → Permalinks → Save Changes** — flushes rewrite rules so the plugin's
   REST routes (`/wp-json/pos-sync/v1/...`) resolve. **Required after activation.**
3. **WooCommerce → POS Sync**: set POS base URL = `https://pos.pets2ndhome.com/staging/public`,
   Shared secret = same secret, Order sync = on.

### Gotcha: 404s that look like a bug but are just rewrite rules
- The REST ping `/wp-json/pos-sync/v1/ping` returned a themed 404 until the plugin was
  activated **and** permalinks were re-saved.
- A published product page (`/product/<slug>/`) returned the same themed 404 — same cause.
  Fix: **Settings → Permalinks → Save Changes** (structure must be "Post name", not "Plain").
  This is a WordPress/WooCommerce rewrite issue, unrelated to the integration.

## 5. Cron (cPanel → Cron Jobs)

In the cPanel form the **schedule and command are separate fields** — do not put the
`* * * * *` inside the command. Use absolute paths (cron does not expand `~`).

- Every minute — drain the outbox:
  - Common Settings: *Once Per Minute (\* \* \* \* \*)*
  - Command: `cd /home5/cileyaco/pos.pets2ndhome.com/staging && /usr/local/bin/php artisan woo:sync >> storage/logs/woo-sync.log 2>&1`
- Nightly 3 AM — reconcile drift (`0 3 * * *`):
  - Command: `cd /home5/cileyaco/pos.pets2ndhome.com/staging && /usr/local/bin/php artisan woo:reconcile >> storage/logs/woo-sync.log 2>&1`

There are two pre-existing `*/16` `queue:work` cron jobs for other sites
(`website_58932e86`, `website_ec0728d6`) — **unrelated, leave them alone.**

Verify firing: `tail -f /home5/cileyaco/pos.pets2ndhome.com/staging/storage/logs/woo-sync.log`
→ a `Queue empty.` / `Pushed …` line each minute.

## 6. Verification performed (all passed)

| Check | How | Result |
|---|---|---|
| Plugin reachable | `curl https://pets2ndhome.com/wp-json/pos-sync/v1/ping` | `{"ok":true,...}` |
| POS export reachable | `curl -H "Authorization: Bearer <secret>" https://pos.pets2ndhome.com/staging/public/api/woo/products` | products JSON |
| Import | plugin "Import from POS" | 2103 created, 0 failed |
| Mapping back to POS | `SELECT COUNT(*) FROM items WHERE woo_product_id IS NOT NULL;` | 2103 |
| POS → Woo push | change stock at loc 6, `php artisan woo:sync` | "Pushed 1 item(s), 0 failed" |
| Woo → POS order | admin order → **Processing** | `WEB<id>` inventory row, stock dropped at loc 6 |
| Cancel → restock | order → **Cancelled** | `WEBCANCEL<id>` row (+qty), `woo_orders.status=cancelled`, stock restored |

### Handy test method
You do **not** need the storefront to test the order flow. Create an order in
**WooCommerce → Orders → Add order**, add a `POS-`-SKU product, set status to
**Processing** (decrements) then **Cancelled** (restocks). Both fire the same hooks
a real customer order would.

### Post-verify SQL
```sql
SELECT * FROM inventories WHERE remarks LIKE 'WEB%' ORDER BY id DESC LIMIT 5;      -- WEB + WEBCANCEL rows
SELECT woo_order_id, status FROM woo_orders ORDER BY id DESC LIMIT 5;
```

## Current behaviour summary

- Full **cancellation** restocks (single location → restock lands on location 6). **Refunds do not** restock (by design).
- Stock is pushed to Woo as an **absolute** value, so there is no feedback loop.
- Everything is dormant unless `woo_settings.sync_enabled = 'yes'`.
