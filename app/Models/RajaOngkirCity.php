<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class RajaOngkirCity extends Model
{
    protected $table = 'rajaongkir_cities';
    protected $primaryKey = 'city_id';

    use Cachable;
    protected $cachePrefix = "rajaongkir_city_cache";
    protected $cacheCooldownSeconds = 300;
}