<?php

use Illuminate\Database\Seeder;

class AdminCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('currency')->truncate();

        DB::table('currency')->insert([
            'admin_id' => 1,
            'type' => 1,
            'update_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('currency')->insert([
            'admin_id' => 2,
            'type' => 1,
            'update_at' => date("Y-m-d H:i:s"),
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
