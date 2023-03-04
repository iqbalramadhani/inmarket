<?php

namespace App\Models\Traits;

use App\Models\BusinessSetting;
use Cache;

trait MarkupPrice
{
    public function markupPrice($actual_price): int
    {
        // get setting markup
        $markup =  Cache::remember('markup_setting', 1600, function () {
            return BusinessSetting::where('type', 'markup_price')->first();
        });

        if($markup && $markup->value){
            $markup = json_decode($markup->value);
            if((boolean)$markup->enable){
                $markup_price_value = $markup->value;

                // formula
                // markup price + actual price
                if($markup->type === 'persentase'){
                     $new_price =  (($markup_price_value/100) * $actual_price) + $actual_price;
                }else{
                    $new_price = $actual_price + $markup_price_value;
                }
            }else{
                $new_price = $actual_price;
            }

            return $new_price;
        }

        return $actual_price;
    }

    public function calculateCommission($unit_price, $quantity)
    {
        // get setting markup
        $markup =  Cache::remember('markup_setting', 1600, function () {
            return BusinessSetting::where('type', 'markup_price')->first();
        });

        if($markup && $markup->value){
            $markup = json_decode($markup->value);
            if((boolean)$markup->enable){
                $markup_price_value = $markup->value;

                // formula
                if($markup->type === 'persentase'){
                    $commission =  (($markup_price_value/100) * $unit_price);
                }else{
                    $commission = $markup_price_value;
                }
            }else{
                $commission = 0;
            }
            return $commission * $quantity;
        }

        return 0;
    }

}