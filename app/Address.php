<?php

namespace App;

use App\Models\RajaOngkirCity;
use App\Models\RajaOngkirSubdistrict;
use App\Models\RajaOnkirProvince;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['set_default'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCityAttribute($value)
    {
        return $this->rajaongkirCity->city_name ?? null;
    }

    public function rajaongkirCity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RajaOngkirCity::class, 'city_id');
    }

    public function province(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RajaOnkirProvince::class, 'province_id');
    }

    public function subdistrict(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RajaOngkirSubdistrict::class, 'subdistrict_id');
    }
}
