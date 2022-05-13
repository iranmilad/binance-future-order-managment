<?php

namespace App\Http\Controllers;

use App\Exchanges\Binance\Binance;
use App\Exchanges\Exchange;
use App\Exchanges\Requests\NewOrderRequestDto;
use App\Exchanges\Responces\NewOrderResponceDto;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    private $user;
    private $exchange;

    public function __construct()
    {
        $userId = request()->input("user_id");

        $user = User::find($userId);

        if (!$user) {
            abort(404, "کاربر یافت نشده است.");
            // return  $this->responseJson("کاربر یافت نشده است.", null, 404, "error");
        }
        $this->user = $user;
        $this->exchange = $this->getExchange();
    }
    
    public function newOrder(Request $request)
    {
        // return $this->user->todayOrders()->count();
        if($this->user->todayOrders()->count()>=$this->user->orders_limitation){
            return $this->responseJson("سقف تعداد سفارش در روز کاربر پر شده است.", null, 403, "error");
        }
        if($this->user->todayOrders()->sum("quantity")>=$this->user->volume_limitation){
            return $this->responseJson("سقف حجم سفارش در روز کاربر پر شده است.", null, 403, "error");
        }
        $req = $this->getRequestDto($request);

        $result= $this->exchange->newOrder($req);

        if (isset($result->orderId)) {
            $this->store($request, $result, $this->user);
            return $this->responseJson("سفارش با موفقیت ثبت شد.", $result);
        } else {
            return $this->responseJson("خطایی رخ داده است", $result, 417, "error");
        }
    }

    public function cancelOrder(Request $request)
    {
        $symbol=$request->input("symbole");
        $order_id=$request->input("order_id");
        $order=Order::where('order_id',$order_id)->first();
        $result= $this->exchange->cancelOrder( $symbol,$order_id,$order->origClientOrderId??null);

        if($order->tp_child_order_id){
            $this->exchange->cancelOrder( $symbol,$order->tp_child_order_id,$order->origClientOrderId??null);
        }

        if($order->sl_child_order_id){
            $this->exchange->cancelOrder( $symbol,$order->sl_child_order_id,$order->origClientOrderId??null);
        }

        if (isset($result->orderId)) {
            $order->update("status","cancel");
            return $this->responseJson("سفارش با موفقیت کنسل شد.", $result);
        } else {
            return $this->responseJson("خطایی رخ داده است", $result, 417, "error");
        }
    }

    public function closeOrder(Request $request)
    {
        $symbol=$request->input("symbole");
        $order_id=$request->input("order_id");
        $order=Order::where('order_id',$order_id)->first();
        $result= $this->exchange->cancelOrder( $symbol,$order_id,$order->origClientOrderId??null);

        if (isset($result->orderId)) {
            $order->update("status","cancel");
            return $this->responseJson("سفارش با موفقیت کنسل شد.", $result);
        } else {
            return $this->responseJson("خطایی رخ داده است", $result, 417, "error");
        }
    }

    public function deleteAllOpenOrders(Request $request)
    {
        $symbol=$request->input("symbol");

        $result= $this->exchange->deleteAllOpenOrders($symbol);

        if (isset($result->code)) {
            Order::where("symbol",$symbol)->update("status","cancel");
            return $this->responseJson("خطایی رخ داده است", $result, 417, "error");
        } else { return $this->responseJson("", $result);}
    }

    public function allOpenOrder(Request $request)
    {
        $symbol=$request->input("symbol");

        $result= $this->exchange->getOpenOrders($symbol);

        if (isset($result->code)) {
            return $this->responseJson("خطایی رخ داده است", $result, 417, "error");
        } else { return $this->responseJson("", $result);}
    }

    public function allOrder(Request $request)
    {
        $symbol=$request->input("symbol");

        $result= $this->exchange->getOrders($symbol);

        if (isset($result->code)) {
            return $this->responseJson("خطایی رخ داده است", $result, 417, "error");
        } else { return $this->responseJson("", $result);}
    }

    public function  getOrder(Request $request)
    {
        $symbol=$request->input("symbol");
        $order_id=$request->input("order_id");

        $result= $this->exchange->getOrder($symbol,$order_id);

        if (isset($result->code)) {
            return $this->responseJson("خطایی رخ داده است", $result, 417, "error");
        } else { return $this->responseJson("", $result);}
    }

    public function  totalAmount()
    {
        $result= $this->exchange->totalAmount();

        if (isset($result->code)) {
            return $this->responseJson("خطایی رخ داده است", $result, 417, "error");
        } else { return $this->responseJson("", $result);}
    }

    public function currentPositionMode()
    {
        $result= $this->exchange->currentPositionMode();

        if (isset($result->code)) {
            return $this->responseJson("خطایی رخ داده است", $result, 417, "error");
        } else { return $this->responseJson("", $result);}
    }

    private function getExchange(): Exchange
    {
       $_exchange=null;
        $exchange =  $this->user->broker?? "binance";
        if ($exchange == "binance") {
            $_exchange = new Binance($this->user);
        }

        return $_exchange;
    }

    private function store(Request $request, NewOrderResponceDto $responce, $user)
    {
        Order::create([
            'order_id' => $responce->orderId,
            'broker' => $responce->broker,
            'user' => $user->id,
            'status' => $responce->status,
            'symbol' => $responce->symbol,
            'side' => $responce->side,
            'type' => $responce->type,
            'positionSide' => $responce->positionSide,
            'timeInForce' => $responce->timeInForce,
            'quantity' => $request->input("quantity"),
            'reduceOnly' => $responce->reduceOnly,
            'price' => $responce->price,
            'avgPrice' => $responce->avgPrice,
            'newClientOrderId' => $request->input("newClientOrderId"),
            'stopPrice' => $responce->stopPrice,
            'closePosition' => $responce->closePosition,
            'activationPrice' => $responce->activatePrice,
            'callbackRate' => $request->input("callbackRate"),
            'workingType' => $responce->workingType,
            'priceProtect' => $responce->priceProtect,
            'newOrderRespType' => $request->input("newOrderRespType"),
            'recvWindow' => $request->input("recvWindow"),
            'timestamp' => $request->input("recvWindow"),
        ]);
    }

    private function getRequestDto($request)
    {
        $req = new NewOrderRequestDto();
        $req->symbol = $request->input("symbol");
        $req->side = $request->input("side");
        $req->type = $request->input("type", "MARKET");
        $req->positionSide = $request->input("positionSide");
        $req->timeInForce = $request->input("timeInForce", "GTC");
        $req->quantity = $request->input("quantity");
        $req->reduceOnly = $request->input("reduceOnly");
        $req->price = $request->input("price", 0);
        $req->newClientOrderId = $request->input("newClientOrderId");
        $req->stopPrice = $request->input("stopPrice");
        $req->closePosition = $request->input("closePosition");
        $req->activationPrice = $request->input("activationPrice");
        $req->callbackRate = $request->input("callbackRate");
        $req->workingType = $request->input("workingType");
        $req->priceProtect = $request->input("priceProtect");
        $req->newOrderRespType = $request->input("newOrderRespType");
        $req->recvWindow = $request->input("recvWindow");
        $req->timestamp = $request->input("timestamp");

        return $req;
    }

}
