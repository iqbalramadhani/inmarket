<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SaveAgreeWithTosController extends Controller
{
    public function save(Request $request): void
    {
        if($request->is_agree_tos_customer === 'true'){
            $customer = \Auth::user();
            if($customer){
                $customer->is_agree_tos_customer = true;
                $customer->save();
            }
        }
    }
}