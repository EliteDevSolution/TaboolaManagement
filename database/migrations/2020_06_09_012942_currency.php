<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Currency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency', function (Blueprint $table) {
           $table->increments('id');
           $table->integer('admin_id');
           $table->unsignedInteger('type');
           $table->float('min_value', 8,2)->default(4.2);
           $table->float('max_value', 8,2)->default(4.2);
           $table->string('update_at');
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
