<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class RajaOngkirSubdistrict extends Model
{
    protected $table = 'rajaongkir_subdistricts';
    use Cachable;
    protected $cachePrefix = "rajaongkir_subdistricts_cache";
    protected $cacheCooldownSeconds = 300;
}