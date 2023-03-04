<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use Cachable;
    protected $cachePrefix = "currency_cache";
    protected $cacheCooldownSeconds = 300;
}
