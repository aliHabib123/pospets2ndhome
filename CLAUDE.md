# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel 5.5 Point-of-Sale (POS) application** for a pet supplies shop. The application lives entirely inside the `staging/` subdirectory. The root-level `index.php` simply redirects to `staging/public/`, and `login.php`/`item-movement.php` are a separate, older standalone PHP password-protection layer that is not part of the Laravel app.

**Important:** All Laravel work happens under `staging/`. The web root served by MAMP is `staging/public/`.

## Development Commands

All commands must be run using MAMP's PHP 7.4 binary:

```bash
# Run migrations
/Applications/MAMP/bin/php/php7.4.33/bin/php artisan migrate

# Install Composer packages
/Applications/MAMP/bin/php/php7.4.33/bin/php /Applications/MAMP/bin/php/composer.phar install

# Require a new Composer package
/Applications/MAMP/bin/php/php7.4.33/bin/php /Applications/MAMP/bin/php/composer require <package>

# Run PHPUnit tests
/Applications/MAMP/bin/php/php7.4.33/bin/php vendor/bin/phpunit

# Run a single test
/Applications/MAMP/bin/php/php7.4.33/bin/php vendor/bin/phpunit --filter TestName

# Build frontend assets (from staging/)
npm run dev       # development build
npm run prod      # production build
npm run watch     # watch mode
```

Clear the route/config cache via the browser at `/clear-cache`, or use artisan directly.

## Architecture

### Dual API + Controller Pattern

Every transactional screen (Sale, Receiving, Transfer, WholeSale) uses two complementary layers:

1. **Temp table + AJAX API** — AngularJS in the browser POSTs items to a `*TempApiController` (e.g. `SaleTempApiController`), which stores rows in a `*_temp` database table keyed by the current user and location. This is the "cart" for each transaction.
2. **Submit controller** — When the user finalises the transaction, a standard Laravel form POST goes to the main controller (e.g. `SaleController@store`), which reads the Temp rows, writes the permanent records, adjusts `item_quantities`, writes `inventories` audit rows, then truncates the Temp table.

Controllers involved in each domain:
- **Sales**: `SaleController`, `SaleApiController` (item catalogue JSON), `SaleTempApiController` (cart CRUD)
- **Wholesale**: `WholeSaleController`, `WholeSaleApiController`, `WholeSaleTempApiController`
- **Receiving**: `ReceivingController`, `ReceivingApiController`, `ReceivingTempApiController`
- **Transfer**: `TransferController`, `TransferApiController`, `TransferTempApiController`

### Frontend: AngularJS + Blade

Blade templates extend `resources/views/app.blade.php`. Dynamic POS screens (sale, transfer, receiving, wholesale) attach AngularJS controllers declared in `public/js/` (e.g. `sale.js`, `transfer.js`). The `ng-app="tagpos"` directive is on the `<html>` tag. Vue.js is scaffolded but not meaningfully used.

### Location Session

Every user must select a **location** before accessing most features. The chosen location is stored in `Session::get('selectedLocationId')`. Controllers guard against a missing location with a redirect to `/locations/choose`. All inventory and item queries are scoped to this session value.

### Multi-Currency

Prices are stored in USD. A `Rate` setting (USD→LBP) is stored in the `settings` table and fetched as `Setting::where('config', 'Rate')->first()->value`. The API (`SaleApiController@getRate`) exposes it to the AngularJS front-end. When a sale is saved, all prices are multiplied by `$rate` before persisting.

### Authorization

Uses `spatie/laravel-permission` (v2). Permission checks are done inline with `Auth::user()->hasPermissionTo('permission_name')` or `Auth::user()->hasRole('Role')`. Role-based gate logic also lives in some controllers directly. Key permissions: `sales`, `wholesales`, `receiving`, `send_transfer`, `receive_transfer`, `refund`, `reports`, `inventory`, `settings`, `users`, etc.

### Inventory Tracking

Every stock movement (sale, receiving, transfer) creates an `Inventory` record with `in_out_qty` (negative for outflows), `qty_before_transaction`, and a `remarks` string like `SALE42` or `RECEIVING7`. `ItemQuantity` holds the current stock count per `(item_id, location_id)` pair and is updated in the same request.

### Item Types

- `type_id = 0` — Service (no stock tracking)
- `type_id = 1` — Product (tracked via `item_quantities`)
- `type_id = 2` — Item Kit (bundle); selling one kit decrements quantities of all its constituent items from `item_kit_items`

### Receipt Printing

`App\Services\PrinterService` wraps `mike42/escpos-php`. On macOS it uses CUPS; on Windows it uses `WindowsPrintConnector`; on Linux it uses `/dev/usb/lp0`. The printer name is configurable via `Setting::where('config', 'printer_name')`. When the printer is unavailable, it falls back to a `DummyPrintConnector` silently. Test the printer at `/test-printer`.

### Reports

`GeneralController` handles all reporting (`generalReports/*` routes): sales, wholesale, receiving, transfer, closeout (daily summary), item movement, categories profit, and inventory by location. Reports are filtered by location, date range, and sometimes role (Admins see all locations; Moderators are scoped to location 7).

## Database

MySQL. The schema is defined through Eloquent migrations plus the seeded `settings`, `permissions`, and `roles` tables. There is no seed file for items — the database is populated through the app UI. The `settings` table uses a `config`/`value` key-value structure (e.g. `Rate`, `printer_name`, `store_name`).
