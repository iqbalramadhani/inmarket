<?php

namespace App\Http\Controllers;

// use Illuminate\Http;

use App\Http\Controllers\CheckoutController;
use App\Models\OYIndonesia;
use App\Models\UserBankAccount;
use App\Order;
use App\Wallet;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;
use Uuid;

class OYIndonesiaController extends Controller
{
    //
    public function __construct()
    {

    }

    public function getPaymentStatus($wallet_id)
    {

        $wallet = Wallet::findOrFail($wallet_id);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', env('OYID_BASEURL') . '/api/payment-checkout/' . $wallet->trx_id, [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-oy-username' => env('OYID_USERNAME'),
                'x-api-key' => env('OYID_APIKEY'),
            ],
            // 'body' => $body,
            'timeout' => 50,
        ]);

        $result = json_decode((string) $response->getBody());

        return (object) ['oy_id' => $result];

    }

    public function getDisburstmentStatus($wallet_id)
    {

        $wallet = Wallet::findOrFail($wallet_id);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', env('OYID_BASEURL') . '/api/remit-status', [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-oy-username' => env('OYID_USERNAME'),
                'x-api-key' => env('OYID_APIKEY'),
            ],
            'json' => [
                'partner_trx_id' => $wallet->partner_trx_id,
            ],
            // 'body' => $body,
            'timeout' => 50,
        ]);

        // dd($response);

        $result = json_decode((string) $response->getBody());

        return $result;

    }

    // public function PaymentStatous(Request $request)
    // {
    //     $result = $this->updatePaymentStatus($request->wallet_id);

    //     // dd($result->wallet());

    //     if ($result->data->status == 'CREATED' || $result->data->status == 'WAITING_PAYMENT') {

    //         $data['sub_type'] = $wallet->payment_details;
    //         $data['result'] = $wallet->oy_id;
    //         return view('frontend.user.wallet.waiting_payment', $data);
    //     }

    //     $updateWallet  = $this->updateWallet($wallet->id);
    //     return view('frontend.user.wallet.waiting_payment', $data);
    // }

    public function confirmation()
    {

    }

    public function withdrawWallet(Request $request)
    {
        if (Session::has('_old_token') && Session::get('_old_token') == Session::token()) {
            Session::forget('_old_token');
            return redirect()->route('wallet.index');
        }

        $user = Auth()->user();

        if($user->balance - $request->amount < 0) {
            flash(translate('Wallet amount is not sufficient'))->error();
            return back();
        }
        // $request->validate([
        //     'bank_code' => 'required',
        //     'account_number' => 'required',
        //     'amount' => 'required',
        // ]);

        $user_account = UserBankAccount::find($request->bank_id);
        $request->bank_code = $user_account->bank_code;
        $request->account_number = $user_account->account_number;

        $client = new \GuzzleHttp\Client();
        $partner_trx_id = (string) Uuid::generate(5, date('Y-m-d H:i:s') . 'Withdraw Wallet' . Auth::user()->name, Uuid::NS_DNS);
        $body = [
            "recipient_bank" => $request->bank_code,
            "recipient_account" => $request->account_number,
            'amount' => $request->amount,
            'note' => 'Withdraw Wallet',
            'email' => Auth()->user()->email,
            'partner_trx_id' => $partner_trx_id,
        ];

        $response = $client->request('POST', env('OYID_BASEURL') . '/api/remit', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-oy-username' => env('OYID_USERNAME'),
                'x-api-key' => env('OYID_APIKEY'),
            ],
            'json' => $body,
            // 'body' => $body,
            'timeout' => 50,
        ]);

        $bodyResult = $response->getBody();
        $result = json_decode((string) $bodyResult);

        if ($result->status->code != 101) {
            flash(translate($result->status->message . 'Please try again'))->error();
            return back();
        }

        // dd($result);

        // dd($result->status;

        $wallet = Wallet::create([
            'user_id' => $user->id,
            'type' => 'DISBURSEMENT',
            'amount' => $request->amount,
            'payment_method' => 'OY_ID',
            'payment_details' => '-',
            'partner_trx_id' => $result->partner_trx_id,
        ]);

        // dd($wallet->toArray());

        // $response = json_encode(json_decode((string)$response->getBody(), false));
        $oyid = OYIndonesia::create(
            [
                'partner_trx_id' => $partner_trx_id,
                'status' => $result->status->message,
                'request' => json_encode($body),
                'response' => json_encode(json_decode((string) $response->getBody(), false)),
            ]
        );
        $updateWallet = $this->updateWallet($wallet->id);

        Session::put('_old_token', Session::get('_token'));

        if ($result->status->code == 101) {
            flash(translate($result->status->message . ' Please manually check your disburstment status'))->success();
        } else {
            flash(translate($result->status->message . 'Please try again'))->error();
        }

        return redirect()->route('wallet.index');
        // dd(json_decode((string)$response->getBody()));
    }

    public function accountInquiry(Request $request)
    {
        $request->validate([
            'bank_code' => 'required',
            'account_number' => 'required',
        ]);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', env('OYID_BASEURL') . '/api/account-inquiry', [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-oy-username' => env('OYID_USERNAME'),
                'x-api-key' => env('OYID_APIKEY'),
            ],
            'json' => [
                "bank_code" => $request->bank_code,
                "account_number" => $request->account_number,
            ],
            // 'body' => $body,
            'timeout' => 50,
        ]);

        return response()->json(
            json_decode((string) $response->getBody())
        );
    }

    public function showPaymentStatus(Request $request, $wallet_id)
    {
        $wallet = Wallet::findOrFail($wallet_id);
        if ($wallet->type == 'TOPUP') {

            $result = $this->getPaymentStatus($wallet_id);
            if ($result->oy_id->data->status == 'CREATED' || $result->oy_id->data->status == 'WAITING_PAYMENT') {

                $data['sub_type'] = $wallet->payment_details;
                $data['result'] = $wallet->oy_id;
                $data['wallet'] = $wallet;

                flash(translate('Please procceed the payment first'))->success();

                return view('frontend.user.wallet.waiting_payment', $data);
            } else if ($result->oy_id->data->status == 'EXPIRED') {
                $wallet->oy_id->update(
                    [
                        'partner_trx_id' => $result->oy_id->data->partnerTxId,
                        'status' => "EXPIRED",
                        'response' => json_encode($result->oy_id->data),
                    ]
                );

                flash(translate('Payment Has Expired'))->error();
                return redirect()->route('wallet.index');
            } else if ($result->oy_id->data->status == 'COMPLETE') {
                if ($wallet->oy_id->status != 'COMPLETE') {
                    $wallet->oy_id->update(
                        [
                            'partner_trx_id' => $result->oy_id->data->partnerTxId,
                            'status' => "COMPLETE",
                            'response' => json_encode($result->oy_id->data),
                        ]
                    );
                    $updateWallet = $this->updateWallet($wallet_id);

                }

                flash(translate('Payment has successfully done'))->success();
                return redirect()->route('wallet.index');
            }
        } else if ($wallet->type == 'DISBURSEMENT') {
            $result = $this->getDisburstmentStatus($wallet_id);
            $wallet->oy_withdraw->update(
                [
                    'trx_id' => $result->trx_id,
                    'status' => $result->status->message,
                    'response' => json_encode($result),
                ]
            );

            $this->updateWallet($wallet_id);
            flash(translate('Disbursement has successfully done'))->success();
            return redirect()->route('wallet.index');

        }

        return redirect()->route('wallet.index');
        // return view('frontend.user.wallet.waiting_payment', $data);
    }

    private function updateWallet($wallet_id)
    {
        $wallet = Wallet::findOrFail($wallet_id);
        $user = $wallet->user;

        if ($wallet->type == 'TOPUP') {
            if ($wallet->oy_id->status == 'COMPLETE') {
                $user->balance = $user->balance + $wallet->amount;
            }
            $user->save();
        } else if ($wallet->type == 'DISBURSEMENT') {
            $response = json_decode($wallet->oy_withdraw->response);
            if ($response->status->code == '101') {
                $user->balance = $user->balance - $wallet->amount;
            } else if (json_decode($wallet->oy_withdraw->response)->status->code == '000') {
            } else {
                $user->balance = $user->balance + $wallet->amount;
            }
            $user->save();
        }

        if ($wallet->save()) {
            return 1;
        }
        return 0;
    }

    public function createPayment($payment_option, $disabledList = "")
    {
        // dd(Session::get('_old_token'), Session::token());
        if (Session::has('_old_token') && Session::get('_old_token') == Session::token()) {
            Session::forget('_old_token');
            return redirect()->route('wallet.index');
        }
        // if(session::token() != '')
        $user = Auth()->user();

        switch ($payment_option) {
            case "oyid_va":
                $sub_type = "Bank Transfer";
                break;
            case "oyid_card":
                $sub_type = "Credit/Debit Card";
                break;
            case "oyid_wallet":
                $sub_type = "E-Wallet";
                break;
            case "oyid_qris":
                $sub_type = "QRIS";
                break;
            default:
                $sub_type = "-";
                break;
        }

        if (Session::has('payment_type')) {
            if (Session::get('payment_type') == 'cart_payment') {
                // $order = Order::findOrFail(Session::get('order_id'));
                // $amount = $order->grand_total;
            } elseif (Session::get('payment_type') == 'wallet_payment') {
                $amount = Session::get('payment_data')['amount'];
                $description = 'Wallet Top Up';
                $include_admin_fee = false;
            }
        } else {
            dd('wrong path');
        }
        $partner_trx_id = (string) Uuid::generate(5, date('Y-m-d H:i:s') . 'Top Up Wallet' . Auth::user()->name, Uuid::NS_DNS);
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

        $body = (string) $response->getBody();

        if (json_decode($body)->status == false) {
            echo $body;
        } else {

            $result = json_decode($body);

            $oyid = OYIndonesia::create(
                [
                    'trx_id' => $result->payment_link_id,
                    'status' => "CREATED",
                    'url' => $result->url,
                    'partner_trx_id' => $partner_trx_id,
                ]
            );

            // dd($oyid);

            $wallet = Wallet::create([
                'user_id' => $user->id,
                'type' => 'TOPUP',
                // 'amount' => round(Session::get('payment_data')['amount'] * 100),
                'amount' => Session::get('payment_data')['amount'],
                'payment_method' => $payment_option,
                'payment_details' => $sub_type,
                'trx_id' => $oyid->trx_id,
                'partner_trx_id' => $partner_trx_id,
            ]);

            Session::put('_old_token', Session::get('_token'));
            return view('frontend.user.wallet.waiting_payment', compact('sub_type', 'result', 'wallet'));
        }
    }

    public function getCheckoutPaymentStatus(Request $request)
    {
        $partner_trx_id = $request->partner_trx_id;

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', env('OYID_BASEURL') . '/api/payment-checkout/status?send_callback=true&partner_tx_id=' . $partner_trx_id, [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-oy-username' => env('OYID_USERNAME'),
                'x-api-key' => env('OYID_APIKEY'),
            ],
            // 'body' => $body,
            'timeout' => 50,
        ]);

        $result = json_decode((string) $response->getBody());
        $oyid = OYIndonesia::where('partner_trx_id', $partner_trx_id)->firstOrFail();

        if ($result->success) {
            $oyid->update([
                'status' => strtoupper($result->data->status),
            ]);
        }

        $oyid->update([
            'response' => json_encode($result->data),
        ]);

        $orders_query = Order::where('oy_trx_id', $partner_trx_id);
        $orders = $orders_query->get();
        $payment = $orders[0]->payment_type;

        if ($result->data->status == 'complete') {
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

        return response()->json($result);

    }

        public function oySuccess(Request $request)
        {
            $order_ids = Session::get('order_ids');
            $payment = Session::get('payment_type');
            

            // dd($order_ids);
            // dd($orders);

            $orders = Order::WhereIn('id', $order_ids);
            // // dd($order->first()->oy_trx_id);
            // // dd($order->code());
            $oyid = OYIndonesia::where('partner_trx_id', $orders->first()->oy_trx_id)->firstOrFail();

            $response = json_decode($oyid->response, true);

            // dd($response);
            // dd($responsesuccess);
            if ($response['status'] && $response['status'] == 'complete') {
                $checkout = new CheckoutController();
                return $checkout->checkout_done($order_ids, $payment);
            } else {
                flash(translate('Something error has happened, please try again'))->error();
                return back();
            }
        }
    //Autostep 5
    /** */

    /*
        public function oySuccess()
        {
            $order_id = Session::get('order_id');
            $payment = Session::get('payment_type');

            $order = Order::findOrFail($order_id);
            // dd($oyid);
            // dd($order);
            $oyid = OYIndonesia::where('partner_trx_id', $order->oy_trx_id)->firstOrFail();
            $response = json_decode($oyid->response);
            // dd($order);
            if ($response->status && $response->status == 'complete') {
                Session::forget('_old_order_id');
                // dd($response, $payment, $order->id, $order->toArray());
                $checkout = new CheckoutController();
                return $checkout->checkout_done($order_id, $payment);
            } else {
                flash(translate('Something error has happened, please try again'))->error();
                return back();
            }
        }
    */

    public function getCheckout($payment_option, $disabledList = "")
    {
        switch ($payment_option) {
            case "oyid_va":
                $sub_type = "Bank Transfer";
                break;
            case "oyid_card":
                $sub_type = "Credit/Debit Card";
                break;
            case "oyid_wallet":
                $sub_type = "E-Wallet";
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

        $order = Order::findOrFail(Session::get('order_id'));

        $status = $order->orderDetails->first()->delivery_status;
        if (Session::has('payment_type')) {
            if (Session::get('payment_type') == 'cart_payment') {
                // $partner_trx_id = (string) Uuid::generate(3, $order->kode, Uuid::NS_DNS);
                $partner_trx_id = $order->code;
                $amount = $order->grand_total;
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

        $body = (string) $response->getBody();

        $result = json_decode($body);
        // dd($result->status);
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
            // Session::put('_old_token', Session::get('_token'));
            return view('frontend.oyid.order_payment_oyid', compact('result', 'sub_type', 'order', 'status', 'partner_trx_id'));
        }

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

        try {
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
        
            //code...
        } catch (\Throwable $th) {
            flash(translate('Something error has happened, please try again'))->error();
            return redirect()->route('checkout.shipping_info');
        }
        // dump($body);
        $body = (string) $response->getBody();
        
        
        $result = json_decode($body);
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

            $order_ids = Session::get('order_ids');
            $payment = Session::get('payment_type');
            $orders = Order::WhereIn('id', $order_ids)->get();

            Session::forget('_old_order_id');

            //Autostep 5
            /** */
                // $checkout = new CheckoutController();
                // return $checkout->checkout_done($order_ids, $payment);
            //Autostep 5
            /** */
            
            // Session::put('_old_token', Session::get('_token'));
            // dd($orders->get()->toArray());
            // dd($orders->get());
            return view('frontend.oyid.order_payment_oyid', compact('result', 'sub_type', 'orders', 'status', 'partner_trx_id'));
        }

    }
}
