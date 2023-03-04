<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['set_default'];

    protected $with = ['rajaongkir_city'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function rajaongkir_province()
    {
        return $this->belongsTo('App\Models\RajaOngkirProvince', 'province_id', 'province_id');
    }

    public function rajaongkir_city()
    {
        return $this->belongsTo('App\Models\RajaOngkirCity', 'city_id', 'city_id');
    }

    public function rajaongkir_subdistrict()
    {
        return $this->belongsTo('App\Models\RajaOngkirSubdistrict', 'subdistrict_id', 'subdistrict_id');
    }
}
