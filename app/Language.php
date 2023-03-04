<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use App;

class Language extends Model
{
    use Cachable;
    protected $cachePrefix = "language_cache";
    protected $cacheCooldownSeconds = 300;
}
