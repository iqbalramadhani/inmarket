<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Shop;

class ShopStatusChange extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $data = [];
    public $shop;
    public function __construct(array $data, Shop $shop)
    {
        $this->data = $data;
        $this->shop = $shop;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->data);
        $data['data'] = (object) $this->data;
        $data['shop'] = (object) $this->shop;
        return $this->view('frontend.user.seller.emails.shop_status_change', $data);
    }
}
