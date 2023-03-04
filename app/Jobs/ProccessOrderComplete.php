<?php

namespace App\Jobs;

use App\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProccessOrderComplete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $setting = BusinessSetting::where('type', 'autoconfirm_order')->first();

        $delivered_time = Carbon::createFromFormat('Y-m-d H:i:s', $this->order->delivered_time);
        if ($this->order->delivered_time!=0 && now()->greaterThan($delivered_time->addDays($setting->value))) {
            $this->order->update(
                ['delivery_status' => 'delivered']
            );
        }
    }
}
