<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Shop;

class SellerVerificationAdmin extends Mailable
{
    use Queueable, SerializesModels;

    protected $shop;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['shop'] = $this->shop;
        return $this->view('frontend.user.seller.emails.seller_verification_admin', $data);
    }
}
