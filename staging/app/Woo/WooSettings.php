<?php

namespace App\Woo;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Thin, defensive accessor for the dedicated `woo_settings` key/value table.
 *
 * Everything here degrades to safe defaults if the table or rows are missing,
 * so the rest of the POS is never affected by this integration.
 */
class WooSettings
{
    /** @var array|null Per-request cache of settings. */
    protected static $cache = null;

    /**
     * Load all rows once per request.
     */
    protected static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        self::$cache = [];

        try {
            if (! Schema::hasTable('woo_settings')) {
                return self::$cache;
            }

            foreach (DB::table('woo_settings')->get() as $row) {
                self::$cache[$row->key] = $row->value;
            }
        } catch (\Throwable $e) {
            // Leave cache empty; callers fall back to defaults.
        }

        return self::$cache;
    }

    public static function get(string $key, $default = null)
    {
        $all = self::all();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function enabled(): bool
    {
        return self::get('sync_enabled', 'no') === 'yes';
    }

    public static function secret(): string
    {
        return (string) self::get('shared_secret', '');
    }

    public static function wpUrl(): string
    {
        return rtrim((string) self::get('wp_url', ''), '/');
    }

    /**
     * Ordered list of location ids that make up "web stock".
     *
     * @return int[]
     */
    public static function locations(): array
    {
        $raw = self::get('locations', '[]');
        $decoded = json_decode((string) $raw, true);

        if (! is_array($decoded)) {
            return [];
        }

        return array_values(array_map('intval', $decoded));
    }

    /**
     * User id to attribute web-order inventory rows to (inventories.user_id is NOT NULL).
     */
    public static function systemUserId(): int
    {
        $id = (int) self::get('system_user_id', 0);
        if ($id > 0) {
            return $id;
        }

        try {
            return (int) \App\User::min('id') ?: 1;
        } catch (\Throwable $e) {
            return 1;
        }
    }

    /** Clear the per-request cache (used by tests / long-running commands). */
    public static function flush(): void
    {
        self::$cache = null;
    }
}
