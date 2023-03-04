<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use Cachable;
    protected $cachePrefix = "couerier_cache";
    protected $cacheCooldownSeconds = 300;
}