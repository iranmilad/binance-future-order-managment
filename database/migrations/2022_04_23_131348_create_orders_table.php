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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger("order_id");
            $table->string("broker");
            $table->bigInteger("user_id");
            $table->string("status");
            $table->string("clientOrderId");
            $table->string("symbol");
            $table->string("side");
            $table->string("type");
            $table->string("positionSide")->nullable();
            $table->string("timeInForce")->nullable();
            $table->decimal("quantity")->nullable();
            $table->string("reduceOnly")->nullable();
            $table->decimal("price")->nullable();
            $table->decimal("avgPrice")->nullable();
            $table->string("newClientOrderId")->nullable();
            $table->decimal("stopPrice")->nullable();
            $table->string("closePosition")->nullable();
            $table->decimal("activationPrice")->nullable();
            $table->decimal("callbackRate")->nullable();
            $table->string("workingType")->nullable();
            $table->string("priceProtect")->nullable();
            $table->string("newOrderRespType")->nullable();
            $table->bigInteger("recvWindow")->nullable();
            $table->bigInteger("tp_child_order_id")->nullable();
            $table->bigInteger("sl_child_order_id")->nullable();
            $table->bigInteger("timestamp")->nullable();

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
        Schema::dropIfExists('orders');
    }
};
