<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\OYIndonesia;

class Wallet extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'partner_trx_id',
        'user_id',
        'amount',
        'payment_method',
        'payment_details',
        'trx_id',
        'type'
    ];


    public function oy_id() {
        return $this->hasOne(OYIndonesia::class, 'trx_id', 'trx_id');
    }
    public function oy_withdraw() {
        return $this->hasOne(OYIndonesia::class, 'partner_trx_id', 'partner_trx_id');
    }
}
