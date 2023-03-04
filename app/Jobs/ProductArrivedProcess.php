<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\RajaOngkirService;
use App\Order;
use ProductArrivedProcess as PAP;

class ProductArrivedProcess implements ShouldQueue
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
        //
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->order->resi_number!='' && $this->order->resi_number!=null && $this->order->delivery_status=='on_delivery' && $this->order->delivered_time==null){
            $rajaOngkir = new RajaOngkirService();
            $result = $rajaOngkir->track($this->order->resi_number, $this->order->resi_courier);
            if($result['rajaongkir']['status']['code'] === 200){
                if($result['rajaongkir']['result']['delivered']){
                    $pod_datetime = $result['rajaongkir']['result']['delivery_status']['pod_date'] .' '. $result['rajaongkir']['result']['delivery_status']['pod_time'];
                    $this->order->delivered_time = $pod_datetime;
                    $this->order->save();
                    $this->order->orderDetails()->where('order_id', $this->order->id)->update([
                        'delivery_status' => 'delivered'
                    ]);

                    return 1;
                }
            }
        } else if($this->order->delivered_time!=null) {
            return 1;
        }
        throw new \Exception('Job will fail.');
    }

    public function failed(Exception $exception)
    {
        // Create log file
    }
}
