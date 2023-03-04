<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndonesiaCity extends Model
{
    public $timestamps = false;
    protected $fillable = ['rajaongkir_cities_id'];

    public function province()
	{
	    return $this->belongsTo('App\Models\IndonesiaProvince', 'province_code');
	}

	public function districts()
    {
        return $this->hasMany('App\Models\IndonesiaDistrict', 'city_code');
    }

    public function villages()
    {
        return $this->hasManyThrough('App\Models\IndonesiaVillage', 'App\Models\IndonesiaDistrict');
    }
}