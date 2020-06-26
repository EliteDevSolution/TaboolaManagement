<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('admins')->truncate();

        DB::table('admins')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'view_id' => '198258254',
            'client_id' => '16319a23623643279a5b89acb8d83b63',
            'client_secret' => '136f4b26949247cd8d176ce619476a80',
            'account_name' => 'taboolaaccount-juliaterresrosagmailcom',
            'password' => bcrypt('password'),
            'is_super' => true,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('admins')->insert([
            'name' => 'Jason Wang',
            'email' => 'test1@test.com',
            'view_id' => '217276213',
            'client_id' => '3823e18c46e143058eeceb2efa5d82b1',
            'client_secret' => 'ac9c3da86c2343f19bcd6a62074478e0',
            'account_name' => 'taboolaaccount-adrianoterresrosagmailcom',
            'password' => bcrypt('123456'),
            'is_super' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('admins')->insert([
            'name' => 'Jane Eyer',
            'email' => 'test2@test.com',
            'view_id' => '217276213',
            'client_id' => '3823e18c46e143058eeceb2efa5d82b1',
            'client_secret' => 'ac9c3da86c2343f19bcd6a62074478e0',
            'account_name' => 'taboolaaccount-adrianoterresrosagmailcom',
            'password' => bcrypt('123456'),
            'is_super' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('admins')->insert([
            'name' => 'Cebin cruel',
            'email' => 'test3@test.com',
            'view_id' => '217276213',
            'client_id' => '3823e18c46e143058eeceb2efa5d82b1',
            'client_secret' => 'ac9c3da86c2343f19bcd6a62074478e0',
            'account_name' => 'taboolaaccount-adrianoterresrosagmailcom',
            'password' => bcrypt('123456'),
            'is_super' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
