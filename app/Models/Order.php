<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use  HasFactory;
    protected $fillable = [
        'order_id',
        'broker',
        'user',
        'status',
        'symbol',
        'side',
        'type',
        'positionSide',
        'timeInForce',
        'quantity',
        'reduceOnly',
        'price',
        'avgPrice',
        'newClientOrderId',
        'stopPrice',
        'closePosition',
        'activationPrice',
        'callbackRate',
        'workingType',
        'priceProtect',
        'newOrderRespType',
        'recvWindow',
        'timestamp',
    ];

   
    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
