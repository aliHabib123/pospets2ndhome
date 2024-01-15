<?php

use Illuminate\Database\Seeder;

class TagSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tagpos_settings')->insert([
            'languange' => 'en',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }
}
