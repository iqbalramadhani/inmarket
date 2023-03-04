<?php

namespace App\Console;

use App\Models\Order;
use App\Models\ProductStock;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Session;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // Masukkan Kode Anda Disini
        $schedule->call(function () {
            $order = Order::where('check_by_cron', 0)->get();
            $client = new \GuzzleHttp\Client();

            DB::beginTransaction();
            try {
                foreach ($order as $or) {
                    if(!empty($or->oy_trx_id)){
                        $response = $client->request('GET', env('OYID_BASEURL') . '/api/payment-checkout/status?send_callback=true&partner_tx_id=' . $or->oy_trx_id, [
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'x-oy-username' => env('OYID_USERNAME'),
                                'x-api-key' => env('OYID_APIKEY'),
                            ],
                            'timeout' => 50,
                        ]);
    
                        $result = json_decode($response->getBody());
    
                        if ($result->success == true) {
                            if ($result->data->status == 'expired') {
                                foreach ($or->orderDetails as $ord) {
                                    $ord->payment_status = 'expired';
                                    $ord->save();
    
                                    $product_stock = ProductStock::where('product_id', $ord->product_id)->first();
                                    $product_stock->qty += $ord->quantity;
                                    $product_stock->save();
                                }

                                $or->update([
                                    'check_by_cron' => 1,
                                    'payment_status' => 'expired',
                                ]);
                            }
                        }
                    }
                    sleep(10);
                }

                Session::forget('_old_order_id');

                DB::commit();

                Log::info('Cronjob berhasil dijalankan');
            } catch (\Throwable $th) {
                DB::rollBack();
                Log::info($th->getMessage());
                Log::info($th->getLine());
                Log::info($th->getFile());
            }
            
        })->hourly();
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
