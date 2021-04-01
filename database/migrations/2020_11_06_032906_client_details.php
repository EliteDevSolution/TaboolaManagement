<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->string('business_name');
            $table->string('cnpj');
            $table->string('address');
            $table->string('phone_number')->nullable();
            $table->string('bank_name');
            $table->string('bank_proxy_name');
            $table->string('bank_account_confirm');
            $table->string('bank_cpf_cnpj');
            $table->string('ip_address');
            $table->string('accept_date_time');
            $table->string('doc_version');
            $table->integer('accept_status')->defalut(0);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('client_details');
    }
}
