<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SaveAgreeWithTosController extends Controller
{
    public function save(Request $request): void
    {
        if($request->is_agree_tos_seller === 'true'){
            $seller = \Auth::user();
            if($seller){
                $seller->is_agree_tos_seller = true;
                $seller->save();
            }
        }
    }
}