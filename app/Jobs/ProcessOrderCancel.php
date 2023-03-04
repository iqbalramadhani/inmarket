<?php

namespace App\Jobs;

use App\Models\Wallet;
use App\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\BusinessSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrderCancel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

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
        $continue = false;
        if($this->order->payment_type=='wallet' && $this->order->payment_status=='paid') {
            $continue = true;
        } else {

            $setting = BusinessSetting::where('type', 'autocancel_order')->first();

            if($this->order->oy_payment()->get()->isEmpty()) {
                $continue = true;
            } else {   
                $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $this->order->oy_payment()->first()->updated_at);
                if(now()->greaterThan($updated_at->addDays($setting->value))) {
                    $continue = true;
                }
            }
        }
        if ($continue) {
            $this->order->update(
                ['delivery_status' => 'canceled']
            );

            $this->order->orderDetails()->update(
                ['delivery_status' => 'canceled']
            );
            if($this->order->payment_status == 'paid') {
                $customer = \App\User::find($this->order->user_id);
                $wallet = Wallet::create([
                    'user_id' => $customer->id,
                    'type' => 'TOPUP',
                    'amount' => $this->order->grand_total,
                    'payment_method' => 'refunded',
                    'payment_details' => 'Transaction has been Refunded -' . $this->order->code,
                    // 'partner_trx_id' => $result->partner_trx_id,
                ]);
                $customer->balance += $this->order->grand_total;
                $customer->save();
            }
        }
    }
}
