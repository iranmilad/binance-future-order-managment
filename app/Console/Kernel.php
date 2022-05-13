<?php

namespace App\Console;

use App\Exchanges\Binance\Binance;
use App\Exchanges\Exchange;
use App\Jobs\SendNotificationJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    private $brokers = array();
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $orders = DB::table('orders')->where("status", "new")->get(["order_id", "symbol","broker","user_id","status"]);

            $outOrders = array();

            foreach ($orders as $order) {
                if ($broker = $this->getBroker($order)) {
                    $res = $broker->getOrder($order->symbol, $order->order_id);
                    if (!isset($res->code)) {
                        if ($res->data->status != $order->status) {
                            $order->update($res);
                            $outOrders[] = $res;
                        }
                    }
                }
            }

            if(sizeof($outOrders)>0){
                SendNotificationJob::dispatch($outOrders);
            }
        })->everyMinute();
    }

    private function getBroker($order): Exchange
    {
        if (!isset($this->brokers[$order->broker])) {
            if ($order->broker == "Binance") {
                $this->brokers[$order->broker] = new Binance($order->user);
            }
            return null;
        }
        return $this->brokers[$order->broker];
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
