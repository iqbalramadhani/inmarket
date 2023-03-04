<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

  protected $with = ['user', 'indonesia_province', 'indonesia_city', 'indonesia_district', 'indonesia_village'];
  protected $fillable = ['indonesia_province_id','indonesia_city_id','indonesia_district_id','indonesia_subdistrict_id'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function indonesia_province()
  {
    return $this->belongsTo('App\Models\IndonesiaProvince', 'indonesia_province_id', 'code');
  }

  public function indonesia_city()
  {
    return $this->belongsTo('App\Models\IndonesiaCity', 'indonesia_city_id', 'code');
  }

  public function indonesia_district()
  {
    return $this->belongsTo('App\Models\IndonesiaDistrict', 'indonesia_district_id', 'code');
  }

  public function indonesia_village()
  {
    return $this->belongsTo('App\Models\IndonesiaVillage', 'indonesia_subdistrict_id', 'code');
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
