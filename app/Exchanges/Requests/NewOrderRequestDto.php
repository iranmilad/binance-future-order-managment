<?php

namespace App\Exchanges\Requests;

class NewOrderRequestDto{
    public $symbol;
    public $side;
    public $type;
    public $positionSide;
    public $timeInForce;
    public $quantity;
    public $reduceOnly;
    public $price;
    public $newClientOrderId;
    public $stopPrice;
    public $closePosition;
    public $activationPrice;
    public $callbackRate;
    public $workingType;
    public $priceProtect;
    public $newOrderRespType;
    public $recvWindow;
    public $timestamp;
    public $sl;
    public $tp;
}