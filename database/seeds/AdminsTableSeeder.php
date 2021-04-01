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
        DB::table('client_settings')->truncate();

        DB::table('admins')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'view_id' => '[{"value":"206725596:lupatimes.com"}]',
            'client_id' => '3823e18c46e143058eeceb2efa5d82b1',
            'client_secret' => 'ac9c3da86c2343f19bcd6a62074478e0',
            'account_name' => 'taboolaaccount-adrianoterresrosagmailcom',
            'password' => bcrypt('Ph)aeSkZ@mequ@#zi)88Cl001'),
            'is_super' => true,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        DB::table('admins')->insert([
            'name' => 'Client',
            'email' => 'client001@client.com',
            'view_id' => '[{"value":"229683454:pt1.lupatimes.com"}]',
            'client_id' => '35076c66a7994a3899608f2b3df5da6b',
            'client_secret' => 'ade09254084a438487cd08902f4582c0',
            'account_name' => 'smartpublishers0001-br-sc',
            'password' => bcrypt('123456'),
            'is_super' => false,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'report_page',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'campaign_page',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'column_visibility',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'currency_setting',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'campaign_management_page',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'ads_page',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'financial_setting',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'payment_history',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'content_page',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'utm_generator',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        ////Cliend Permission

        DB::table('client_settings')->insert([
            'user_id' => 2,
            'page_key' => 'report_page',
            'show_rule' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 2,
            'page_key' => 'campaign_page',
            'show_rule' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 2,
            'page_key' => 'column_visibility',
            'show_rule' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 2,
            'page_key' => 'currency_setting',
            'show_rule' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 2,
            'page_key' => 'campaign_management_page',
            'show_rule' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 2,
            'page_key' => 'ads_page',
            'show_rule' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 2,
            'page_key' => 'financial_setting',
            'show_rule' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 2,
            'page_key' => 'payment_history',
            'show_rule' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'content_page',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('client_settings')->insert([
            'user_id' => 1,
            'page_key' => 'utm_generator',
            'show_rule' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        //////////////////////////// Currency //////////////////////////////
        DB::table('currency')->insert([
            'admin_id' => 1,
            'type' => 1,
            'min_value' => 4.2,
            'max_value' => 4.2,
            'update_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('currency')->insert([
            'admin_id' => 2,
            'type' => 1,
            'min_value' => 4.2,
            'max_value' => 4.2,
            'update_at' => date("Y-m-d H:i:s"),
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
