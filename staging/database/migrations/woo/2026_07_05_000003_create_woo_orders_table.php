<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWooOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('woo_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('woo_order_id')->unique();
            $table->string('status', 40)->nullable();
            $table->text('payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('woo_orders');
    }
}
