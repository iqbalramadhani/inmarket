<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use Cachable;
    protected $cachePrefix = "banks_cache";
    protected $cacheCooldownSeconds = 300;
}