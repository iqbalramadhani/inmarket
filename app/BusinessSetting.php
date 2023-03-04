<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use Cachable;
    protected $cachePrefix = "business_setting_cache";
    protected $cacheCooldownSeconds = 300;

    protected $fillable = ['value'];
}
