<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OYIndonesia extends Model
{
    //
    protected $table='pg_oy_log';

    protected $fillable = [
        'trx_id',
        'status',
        'url',
        'request',
        'response',
        'partner_trx_id'
    ];

    public function wallet() {
        return $this->belongsTo(\App\Wallet::class, 'trx_id', 'trx_id');
    }
}
