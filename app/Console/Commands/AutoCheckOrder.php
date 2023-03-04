<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProccessOrderComplete;
use App\BusinessSetting;
use App\Jobs\ProductArrivedProcess;
use App\Jobs\ProcessOrderCancel;
use App\Order;
use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Log;

class AutoCheckOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autocheck:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach(Order::where('delivery_status', 'on_delivery')->where('resi_number', '<>', '')->get() as $order) {
            ProductArrivedProcess::withChain([
                new ProccessOrderComplete($order),
            ])->dispatch($order);
        }

        $setting = BusinessSetting::where('type', 'autocancel_order')->first();
        // foreach(Order::whereIn('delivery_status', ['confirmed','pending'])->where('updated_at', '<>', now()->addDays($setting->value))->get() as $order) {
        //     if($order->oy_payment()->get()->isNotEmpty() && in_array(strtolower($order->oy_payment()->first()->status), ['success', 'created', 'completed', 'waiting'])) {
        //         ProcessOrderCancel::dispatch($order);
        //     }
        // }

        foreach(Order::whereIn('delivery_status', ['confirmed','pending'])->where('updated_at', '<>', now()->addDays($setting->value ?? 2))->get() as $order) {

            if($order->oy_payment()->get()->isNotEmpty()) {
                if(in_array(strtolower($order->oy_payment()->first()->status), ['success', 'created', 'complete', 'waiting'])) {
                    ProcessOrderCancel::dispatch($order);
                    // dump('udah bayar', $order->toArray());
                }
            } else {
                // dump('belum bayar', $order->toArray());
                ProcessOrderCancel::dispatch($order);
            }
        }

        
    }
}
