<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndonesiaVillage extends Model
{
    public $timestamps = false;

    public function district()
	{
	    return $this->belongsTo('App\Models\IndonesiaDistrict', 'district_code');
	}
}