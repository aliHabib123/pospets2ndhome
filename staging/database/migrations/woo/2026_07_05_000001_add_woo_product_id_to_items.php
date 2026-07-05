<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWooProductIdToItems extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'woo_product_id')) {
                $table->unsignedBigInteger('woo_product_id')->nullable()->index()->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'woo_product_id')) {
                $table->dropColumn('woo_product_id');
            }
        });
    }
}
