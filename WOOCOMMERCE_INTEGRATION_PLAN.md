# WooCommerce Integration Plan

Integrate the Laravel POS with a WordPress/WooCommerce storefront so that product
data and inventory stay in sync between the two systems.

## Goal

- **POS is the source of truth.** Title, price, description, and stock all flow
  **POS → Woo**. WooCommerce is a storefront mirror.
- The only thing that flows back is **Woo → POS**: "an order was placed, decrement stock."
- Initial product import is triggered by a **button in WooCommerce** that pulls from
  the POS. Afterwards the data-entry team enriches products (categories, images) in Woo.

## Confirmed decisions

| Decision | Choice |
|---|---|
| Website inventory source | Configurable set of **1 or multiple locations** (ordered list) |
| Currency / price field | **USD** (`items.selling_price_usd`) |
| POS connectivity | POS is **publicly reachable** → real-time WooCommerce webhooks |
| Background processing | **Cron-driven** outbox worker (no always-on process required) |

## Core rule (prevents infinite loops)

POS always pushes an **absolute** stock value to Woo ("set stock = 14"), never a delta.
Woo → POS on an order **decrements** by the line quantity. Because Woo only calls POS on
*order* events (not on stock writes), there is no feedback loop.

### Multi-location decrement rule

Web stock is the **sum** of `item_quantities` across the configured `woo_locations`.
When an order arrives, POS decrements from the **first location with stock**, overflowing
to the next in the ordered list. Each touched location gets its own `Inventory` audit row.

## Relevant existing POS schema

- **`items`** (~1,521 rows): `id`, `upc_ean_isbn` (barcode), `item_name`, `size`,
  `description`, `selling_price` (LBP) + `selling_price_usd`, `type_id` (1 = tracked
  product), `avatar`.
- **`item_quantities`**: `(item_id, location_id, quantity)` — live stock per location,
  the inventory source of truth.
- **6 active locations** (Main Sin el Fil, Badaro, Ashrafieh, Sin el Fil, etc. + archive).
- Every stock mutation (`SaleController@store`, refund, receiving, transfer) calls
  `$itemQuantity->save()` and writes an `Inventory` audit row with a `remarks` tag
  like `SALE42`.

## Product matching key

Use the **POS item id** as the Woo SKU (e.g. `POS-1234`), **not** the barcode —
`upc_ean_isbn` is not guaranteed unique and some values are placeholders. POS also stores
the returned `woo_product_id` for O(1) future syncs.

## Configuration (stored in `settings` table)

| config | value |
|---|---|
| `woo_store_url` | `https://yourstore.com` |
| `woo_consumer_key` / `woo_consumer_secret` | Woo REST API keys (POS→Woo) |
| `woo_webhook_secret` | HMAC secret to verify inbound order webhooks |
| `woo_locations` | ordered JSON array of location ids, e.g. `[6,1,3]` |
| `woo_sync_enabled` | on/off master switch |

## Database changes

- `items.woo_product_id` (nullable int) — the 1:1 mapping, set on first import.
- **`woo_sync_queue`** (outbox): `item_id`, `action` (`stock`/`product`), `status`,
  `attempts`, `last_error`, timestamps.
- **`woo_orders`** (idempotency): `woo_order_id` UNIQUE, `status`, `processed_at`.

## POS side (Laravel)

1. **`GET /api/woo/products`** — export endpoint for the import button. Returns
   `type_id = 1` products: `sku = POS-{id}`, `name = item_name`,
   `price = selling_price_usd`, `description`, `stock = SUM(item_quantities)` over
   `woo_locations`. Protected by the consumer secret.
2. **`POST /api/woo/order`** — inbound webhook. Verifies HMAC, dedupes on `woo_orders`,
   decrements stock across `woo_locations` by the overflow rule, writes `Inventory` rows
   tagged `WEB{orderid}`.
3. **`ItemQuantityObserver` + `ItemObserver`** — on `saved`, if the item is web-mapped and
   the location is in `woo_locations`, enqueue an outbox row. Single choke point catches
   sales / refunds / receiving / transfers / manual edits with zero controller changes.
4. **`php artisan woo:sync`** (cron, every minute) — drains the outbox, recomputes the
   aggregate stock, `PUT`s to Woo, retries with backoff.
5. **`php artisan woo:reconcile`** (nightly) — re-pushes any drifted items; a safety net.

## WordPress side (small custom plugin)

- Admin page with **"Import from POS"** button → calls the export endpoint → creates
  **draft** products with SKU `POS-{id}` and the returned stock.
- Registers the **`order.processing` webhook** pointing at `/api/woo/order`.
- Stores the POS base URL + shared secret in WP options.

## Authentication

- **POS → Woo** uses WooCommerce REST API consumer key/secret stored in the `settings` table.
- **Woo → POS** verifies the webhook HMAC secret on every inbound order.

## Reliability

- Outbox table + cron beats direct HTTP calls: no lost updates if Woo is momentarily down,
  and the nightly reconciliation command re-pushes any drifted stock.

## Phasing

1. **Phase 1** — Settings + migrations + `items.woo_product_id` mapping, export endpoint,
   WP plugin import button. *(Get products flowing in; team starts enriching.)*
2. **Phase 2** — Observers + outbox + `woo:sync` cron. *(POS stock/price changes reflect on
   the site.)*
3. **Phase 3** — Inbound order webhook + idempotency + decrement rule. *(Web sales reduce
   POS stock.)*
4. **Phase 4** — Reconciliation command, refund/cancel restock, admin status dashboard.

## Prerequisites before Phases 2–3

- Woo REST API keys (consumer key/secret) generated in WooCommerce.
- Webhook secret generated for the inbound order webhook.
- The list of `woo_locations` (location ids in priority order) and the Woo store URL.
