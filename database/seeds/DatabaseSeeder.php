<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
    	$this->disableForeignKeys();
        $this->call(AdminsTableSeeder::class);
        $this->call(AdminCurrencySeeder::class);
        $this->enableForeignKeys();

    }

    private function disableForeignKeys()
    {
      if (DB::connection()->getDriverName() == 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
      }
    }

    private function enableForeignKeys()
    {
      if (DB::connection()->getDriverName() == 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
      }
    }
}