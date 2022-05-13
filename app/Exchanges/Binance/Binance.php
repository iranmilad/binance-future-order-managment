<?php

namespace App\Exchanges\Binance;

use App\Exchanges\Exchange;
use App\Exchanges\Requests\NewOrderRequestDto;
use App\Exchanges\Responces\NewOrderResponceDto;
use Lin\Binance\BinanceFuture;
use stdClass;

class Binance implements Exchange
{

    private $binance;

    public function __construct($user)
    {
        $this->binance = new BinanceFuture(
            $user->key,
            $user->secret
            , env("BINANCE_HOST"));

        if (env("USE_PROXY")) {
            $this->binance->setOptions([
                'proxy' => [
                    "ip" => $user->proxy_ip,
                    "username" => $user->proxy_username,
                    "password" => $user->proxy_password,
                ],
            ]);
        }
    }

    public function newOrder(NewOrderRequestDto $request)
    {
        $input = array();
        foreach ($request as $key => $value) {
            if ($value && $key != "sl" && $key != "tp" && $key != "timeInForce") {
                $input[$key] = $value;
            }
        }
        try {
            $input["type"] = "MARKET";
            $result = (object) $this->binance->trade()->postOrder($input);

            $responce = new NewOrderResponceDto();
            if ($request->tp) {
                $input["type"] = "TAKE_PROFIT";
                $input["price"] = $request->tp;
                $input["stop_price"] = $request->tp;
                $input["timeInForce"] = $request->timeInForce;
                $child_tp = (object) $this->binance->trade()->postOrder($input);
                $responce->tp_child_order_id = $child_tp->orderId;
            }

            if ($request->sl) {
                $input["type"] = "STOP";
                $input["price"] = $request->sl;
                $input["stop_price"] = $request->sl;
                $input["timeInForce"] = $request->timeInForce;
                $child_sl = (object) $this->binance->trade()->postOrder($input);
                $responce->sl_child_order_id = $child_sl->orderId;
            }

            if (isset($result->code)) {
                $er = new stdClass();
                $er->code = $result->code;
                $er->msg = $result->msg;
                return $er;
            } else {
                $responce->clientOrderId = $result->clientOrderId;
                $responce->cumQty = $result->cumQty;
                $responce->cumQuote = $result->cumQuote;
                $responce->executedQty = $result->executedQty;
                $responce->orderId = $result->orderId;
                $responce->broker = "Binance";
                $responce->avgPrice = $result->avgPrice;
                $responce->origQty = $result->origQty;
                $responce->price = $result->price;
                $responce->reduceOnly = $result->reduceOnly;
                $responce->symbol = $result->symbol;
                $responce->side = $result->side;
                $responce->type = $result->type;
                $responce->positionSide = $result->positionSide;
                $responce->status = $result->status;
                $responce->stopPrice = $result->stopPrice;
                $responce->closePosition = $result->closePosition;
                $responce->timeInForce = $result->timeInForce;
                $responce->origType = $result->origType;
                $responce->activatePrice = $result->activatePrice;
                $responce->priceRate = $result->priceRate;
                $responce->updateTime = $result->updateTime;
                $responce->workingType = $result->workingType;
                $responce->worpriceProtectkingType = $result->priceProtect;
                return $responce;
            }

        } catch (\Exception$e) {
            return $this->handleError($e);
        }
    }

    public function cancelOrder($symbol, $orderId, $origClientOrderId)
    {
        try {
            $result = $this->binance->trade()->deleteOrder([
                'symbol' => $symbol,
                'orderId' => $orderId,
                'origClientOrderId' => $origClientOrderId,
            ]);
            return $result;
        } catch (\Exception$e) {
            return $this->handleError($e);
        }
    }

    public function deleteAllOpenOrders($symbol)
    {
        try {
            $result = $this->binance->trade()->deleteAllOpenOrders([
                'symbol' => $symbol,
            ]);
            return $result;
        } catch (\Exception$e) {
            return $this->handleError($e);
        }
    }

    public function getOpenOrders($symbol)
    {
        try {
            $input = [];
            if ($symbol) {
                $input = ["symbol" => $symbol];
            }

            $result = $this->binance->user()->getOpenOrders($input);
            return $result;
        } catch (\Exception$e) {
            return $this->handleError($e);
        }
    }

    public function getOrders($symbol)
    {
        try {
            $input = [];
            if ($symbol) {
                $input = ["symbol" => $symbol];
            }

            $result = $this->binance->user()->getAllOrders($input);
            return $result;
        } catch (\Exception$e) {
            return $this->handleError($e);
        }
    }

    public function getOrder($symbol,$order_id=null)
    {
        try {     
            $input = ["symbol" => $symbol];
            if ($order_id) {
                $input[] = ["order_id" => $order_id];
            }
  
            $result = $this->binance->user()->getOrder($input);
            return $result;
        } catch (\Exception$e) {
            return $this->handleError($e);
        }
    }

    public function totalAmount()
    {
        try { 
  
            $result = $this->binance->user()->getBalance();
            return $result;
        } catch (\Exception$e) {
            return $this->handleError($e);
        }
    }

    public function currentPositionMode()
    {
        try {
            $result = $this->binance->trade()->getPositionSideDual();
            return $result;
        } catch (\Exception$e) {
            return $this->handleError($e);
        }
    }

    private function handleError($e)
    {
        $msg = json_decode($e->getMessage());
        if (isset($msg->code)) {
            $er = new stdClass();
            $er->code = $msg->code;
            $er->msg = $msg->msg;
            return $er;
        }
        return $msg;
    }

}
