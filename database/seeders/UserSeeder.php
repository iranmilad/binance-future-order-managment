<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "key" => "f73f8e61d6b6b176fd8c560640ee05057e85b2a289f8089b474fd078a3af904f",
            "secret" => "3992d1885f68703b9a4fedb938a6eac9266dffa3c66f5e6fb9078e6bffec9809",
            "broker" => "binance",
            "proxy_ip" => "",
            "proxy_username" => "",
            "proxy_password" => "",
            "orders_limitation" => 10,
            "volume_limitation" => 5000,
        ]);
    }
}
