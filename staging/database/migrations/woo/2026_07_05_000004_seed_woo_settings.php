<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

/**
 * Dedicated key/value table for the integration's configuration. Kept separate
 * from the shared `settings` table so the integration touches nothing else and
 * is free of that table's varchar(50) value limit.
 */
class SeedWooSettings extends Migration
{
    /** @var array<string,string> */
    private $defaults = [
        'sync_enabled'   => 'no',   // Master switch. Set to 'yes' to activate.
        'wp_url'         => '',      // e.g. https://yourstore.com
        'shared_secret'  => '',      // Must match the plugin's shared secret.
        'locations'      => '[]',    // Ordered JSON array of location ids, e.g. [6,1,3]
        'system_user_id' => '',      // Optional: user id for web-order inventory rows.
    ];

    public function up()
    {
        if (! Schema::hasTable('woo_settings')) {
            Schema::create('woo_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('key', 60)->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        foreach ($this->defaults as $key => $value) {
            if (! DB::table('woo_settings')->where('key', $key)->exists()) {
                DB::table('woo_settings')->insert([
                    'key'        => $key,
                    'value'      => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('woo_settings');
    }
}
