<?php

namespace App\Http\Controllers;

use Lin\Binance\Binance;
use Lin\Binance\BinanceFuture;

class HomeController extends Controller
{
    public function test()
    {
        $binance = new BinanceFuture(
            "f73f8e61d6b6b176fd8c560640ee05057e85b2a289f8089b474fd078a3af904f",
             "3992d1885f68703b9a4fedb938a6eac9266dffa3c66f5e6fb9078e6bffec9809"
            ,"https://testnet.binancefuture.com");

        //Send in a new order.
        try {
            $result=$binance->trade()->postOrder([
                'symbol'=>'BTCUSDT',
                'side'=>'BUY',
                'type'=>'LIMIT',
                'quantity'=>'0.01',
                'price'=>'1000',
                'timeInForce'=>'GTC',
            ]);
            print_r($result);
        } catch (\Exception$e) {
            print_r($e->getMessage());
        }

//Check an order's status.
        // try {
        //     $result = (object) $binance->user()->getOrder([
        //         'symbol' => 'BTCUSDT',
        //         'orderId' =>"3033937226",
        //         'origClientOrderId' => "bUnKVl3WRb6YsRJGKaYUsI",
        //     ]);
        //     print_r($result);
        // } catch (\Exception$e) {
        //     print_r($e->getMessage());
        // }

    }

}
