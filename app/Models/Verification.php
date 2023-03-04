<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    //
    protected $table="seller_info_history";

    protected $fillable = [
        'user_id',
        'details'
    ];
}
