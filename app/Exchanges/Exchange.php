<?php

namespace App\Exchanges;

use App\Exchanges\Requests\NewOrderRequestDto;

interface Exchange
{

    public function newOrder(NewOrderRequestDto $request);

    public function cancelOrder($symbol, $orderId, $origClientOrderId);

    public function deleteAllOpenOrders($symbol);

    public function getOpenOrders($symbol);

    public function getOrders($symbol);

    public function getOrder($symbol,$order_id=null);
    
    public function totalAmount();

    public function currentPositionMode();

}
