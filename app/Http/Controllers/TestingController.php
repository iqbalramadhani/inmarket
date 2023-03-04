<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\BusinessSetting;
use Mail;
use App\Mail\UserRegistered;
use App\Mail\SellerVerification;
use App\Mail\SellerApproved;
use Auth;
use App\Jobs\ProccessOrderComplete;
use App\Jobs\ProductArrivedProcess;
use App\Jobs\ProcessOrderCancel;
use App\Order;
use App\Services\RajaOngkirService;

class TestingController extends Controller
{
    public function terbaik(Request $request) {
        
        $user = Auth()->user();
        switch ($request->o) {
            case 'user_register':
                $template = new UserRegistered($user);
                Mail::to($user->email)->send($template);

                break;
            case 'seller_verification':
                $template = new SellerVerification($user->shop);
                Mail::to($user->email)->send($template);
                break;

            case 'seller_approved':
                $seller = $user->seller()->first();
                $template = new SellerApproved($seller);
                // dd($template);
                Mail::to($user->email)->send($template);
                break;
            
            default:
                # code...
                break;
        }
    }

    public function create_job() {
        // foreach(Order::where('delivery_status', 'on_delivery')->where('resi_number', '<>', '')->get() as $order) {
        //     ProductArrivedProcess::withChain([
        //         new ProccessOrderComplete($order),
        //     ])->dispatch($order);
        // }

        // foreach(Order::whereIn('delivery_status', ['confirmed','pending'])->where('updated_at', '<>', now()->addDays(2))->get() as $order) {
        //     if($order->oy_payment()->get()->isNotEmpty() && strtolower($order->oy_payment()->first()->status) == 'success') {
        //         ProcessOrderCancel::dispatch($order);
        //     }
        // }

        $setting = BusinessSetting::where('type', 'autocancel_order')->first();
        // dd($setting->value ?? 2);
        foreach(Order::whereIn('delivery_status', ['confirmed','pending'])->where('updated_at', '<>', now()->addDays(2))->get() as $order) {

            if($order->oy_payment()->get()->isNotEmpty()) {
                if(in_array(strtolower($order->oy_payment()->first()->status), ['success', 'created', 'complete', 'waiting'])) {
                    ProcessOrderCancel::dispatch($order);
                    // dump('udah bayar', $order->toArray());
                }
            } else {
                // dump('belum bayar', $order->toArray());
                ProcessOrderCancel::dispatch($order);
            }
            // dump($order->oy_payment()->get());
            // dump($order->oy_payment()->first()->status);
            // dump(in_array(strtolower($order->oy_payment()->first()->status), ['success', 'created', 'complete', 'waiting']));
        }
    }
}
