<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use Cachable;
    protected $cachePrefix = "translation_cache";
    protected $cacheCooldownSeconds = 300;
}
