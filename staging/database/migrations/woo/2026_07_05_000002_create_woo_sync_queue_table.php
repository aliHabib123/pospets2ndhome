<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWooSyncQueueTable extends Migration
{
    public function up()
    {
        Schema::create('woo_sync_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id')->index();
            $table->string('action', 20)->default('stock');
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('woo_sync_queue');
    }
}
