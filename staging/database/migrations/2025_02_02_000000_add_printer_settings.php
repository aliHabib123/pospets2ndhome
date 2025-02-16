<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrinterSettings extends Migration
{
    public function up()
    {
        // Add printer settings to settings table
        DB::table('settings')->insert([
            ['config' => 'printer_ip', 'value' => '127.0.0.1'],
            ['config' => 'printer_port', 'value' => '9100'],
            ['config' => 'printer_name', 'value' => 'POS-58'],
        ]);
    }

    public function down()
    {
        DB::table('settings')
            ->whereIn('config', ['printer_ip', 'printer_port', 'printer_name'])
            ->delete();
    }
}
