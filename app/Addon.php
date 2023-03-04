<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use Cachable;
    protected $cachePrefix = "addon_trans_cache";
    protected $cacheCooldownSeconds = 300;
}
