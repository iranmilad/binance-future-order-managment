<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'key',
        'secret',
        'broker',
        'active',
        'proxy_ip',
        'proxy_username',
        'proxy_password',
        'orders_limitation',
        'volume_limitation',
    ];

   
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function todayOrders(){
        return $this->hasMany(Order::class)->whereDate("created_at",now());
    }
}
