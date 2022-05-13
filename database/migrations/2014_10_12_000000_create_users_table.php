<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string('key');
            $table->string('secret');
            $table->string('broker');
            $table->string('proxy_ip')->nullable();
            $table->string('proxy_username')->nullable();
            $table->string('proxy_password')->nullable();
            $table->integer('orders_limitation')->nullable();
            $table->decimal('volume_limitation')->nullable();
            $table->boolean('active')->default(1);        
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
        Schema::dropIfExists('users');
    }
};
