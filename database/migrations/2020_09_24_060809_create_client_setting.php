<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('page_key');
            $table->integer('show_rule')->default(0);
            $table->integer('read_rule')->default(1);
            $table->integer('edit_rule')->default(1);
            $table->integer('create_rule')->default(1);
            $table->integer('delete_rule')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
