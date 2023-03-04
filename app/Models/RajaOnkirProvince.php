<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class RajaOnkirProvince extends Model
{
    protected $table = 'rajaongkir_provinces';
    use Cachable;
    protected $cachePrefix = "rajaongkir_provinces_cache";
    protected $cacheCooldownSeconds = 300;
}