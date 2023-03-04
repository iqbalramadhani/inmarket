<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\OYIndonesia;
use App\Order;
use App\Wallet;
use Illuminate\Http\Request;

class OYController extends Controller
{
    //

    public function offline_callback(Request $request)
    {

        $wallets = Wallet::where('partner_trx_id', $request->partner_tx_id)->get();

        if ($wallets->isEmpty()) {
            $oy = OYIndonesia::where('partner_trx_id', $request->partner_tx_id)->firstOrFail();
            $oy->update([
                'status' => strtoupper($request->status),
                'response' => json_encode($request->all()),
            ]);
            // dd($oy->toArray());

            
            $orders_query = Order::where('oy_trx_id', $request->partner_tx_id);
            $orders = $orders_query->get();
            $payment = $orders[0]->payment_type;
            
            
            if ($request->status == 'complete') {
                // dd($orders->toArray());
                $orders_query->update([
                    'payment_status' => 'paid',
                    'delivery_status' => 'confirmed',
                ]);

                foreach ($orders as $order) {
                    // dump($order->orderDetails());
                    $order->orderDetails()->update([
                        'payment_status' => 'paid',
                        'delivery_status' => 'confirmed',
                    ]);
                }
            }

            return 1;

            // dump($oy->toArray());
        }

        // dd($wallets->isEmpty());

        $wallet = $wallets->first();
        if ($wallet->oy_withdraw->status == 'COMPLETE') {
            return 0;
        }

        $wallet->oy_withdraw->update(
            [
                'status' => strtoupper($request->status),
                'response' => json_encode($request->all()),
            ]
        );

        $this->updateWallet($wallet->id);
    }

    public function disbursement(Request $request)
    {
        $wallet = Wallet::where('partner_trx_id', $request->partner_trx_id)->firstOrFail();

        if ($wallet->oy_withdraw->status == 'success') {
            return 0;
        }

        $wallet->oy_withdraw->update(
            [
                'trx_id' => $request->trx_id,
                'status' => $request->status['message'],
                'response' => json_encode($request->all()),
            ]
        );
        $this->updateWallet($wallet->id);
    }

    private function updateWallet($wallet_id)
    {
        $wallet = Wallet::findOrFail($wallet_id);
        $user = $wallet->user;

        $oy = json_decode($wallet->oy_withdraw);
        $response = json_decode($oy->response);

        if ($wallet->type == 'TOPUP') {
            if ($response->status == 'complete') {
                $user->balance = $user->balance + $wallet->amount;
                $user->save();
            }

        } else if ($wallet->type == 'DISBURSEMENT') {
            if ($response->status->code == '000') {
            } else {
                $user->balance = $user->balance + $wallet->amount;
                $user->save();
            }
        }

        if ($wallet->save()) {
            return 1;
        }
        return 0;
    }

    public function getCheckout2($payment_option, $disabledList = "")
    {
        switch ($payment_option) {
            case "oyid_va":
                $sub_type = "Bank Transfer";
                break;
            case "oyid_card":
                $sub_type = "Card";
                break;
            case "oyid_wallet":
                $sub_type = "E Wallet";
                break;
            case "oyid_qris":
                $sub_type = "QRIS";
                break;
            default:
                $sub_type = "-";
                break;
        }

        // if(session::token() != '')
        $user = Auth()->user();

        // dd($payment_option, $sub_type, $disabledList, Session::all());

        $orders = Order::whereIn('id', Session::get('order_ids'))->get();
        $status = 'pending';
        if (Session::has('payment_type')) {
            if (Session::get('payment_type') == 'cart_payment') {
                $partner_trx_id = (string) Uuid::generate(3, implode(',', $orders->pluck('code')->toArray()), Uuid::NS_DNS);
                $amount = array_sum($orders->pluck('grand_total')->toArray());
                $description = $sub_type;
                $include_admin_fee = true;
            } elseif (Session::get('payment_type') == 'wallet_payment') {
                // $amount = Session::get('payment_data')['amount'];
                // $description = 'Wallet Top Up';
                // $include_admin_fee = false;
            }
        } else {
            dd('wrong path');
        }

        $body = [
            "description" => $description,
            "partner_tx_id" => $partner_trx_id,
            "notes" => "",
            "sender_name" => $user->name,
            "amount" => $amount,
            'email' => $user->email,
            "phone_number" => $user->phone,
            "is_open" => false,
            "step" => "select-payment-method",
            "include_admin_fee" => $include_admin_fee,
            "list_disabled_payment_methods" => $disabledList,
            "list_enabled_banks" => "002, 008, 009, 013, 022",
            "list_enabled_ewallet" => "shopeepay_ewallet, dana_ewallet, linkaja_ewallet, ovo_ewallet",
            "expiration" => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
        ];

        // Session::forget('_old_order_id');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', env('OYID_BASEURL') . '/api/payment-checkout/create-v2', [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-oy-username' => env('OYID_USERNAME'),
                'x-api-key' => env('OYID_APIKEY'),
            ],
            'json' => $body,
            'timeout' => 50,
        ]);

        // dump($body);
        $body = (string) $response->getBody();

        $result = json_decode($body);
        // dd($result);
        // dd(!$result->status);
        if (!$result->status && $result->message == "partner_tx_id must be unique") {
            // dd($partner_trx_id);
            $oyid = OYIndonesia::where('partner_trx_id', $partner_trx_id)->firstOrFail();
            $result = json_decode($oyid->request);
            return view('frontend.oyid.order_payment_oyid', compact('result', 'sub_type', 'order', 'status', 'partner_trx_id'));
        } else if (!$result->status) {
            flash(translate('Something error has happened, please try again'))->error();
            return redirect()->back();
        } else {
            $oyid = OYIndonesia::create(
                [
                    'trx_id' => $result->payment_link_id,
                    'partner_trx_id' => $partner_trx_id,
                    'status' => "CREATED",
                    'url' => $result->url,
                    'request' => $body,
                ]
            );

            $result = json_decode($oyid->request);

            // $orders = Order::whereIn('id', Session::get('order_ids'))->get();
            foreach ($orders as $order) {
                $order->update([
                    'oy_trx_id' => $partner_trx_id,
                ]);
            }
            // Session::put('_old_token', Session::get('_token'));
            // dd($orders);
            // dd($sub_type);
            return view('frontend.oyid.order_payment_oyid', compact('result', 'sub_type', 'orders', 'status', 'partner_trx_id'));
        }

    }
}
