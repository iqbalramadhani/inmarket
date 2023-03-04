<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use App\Models\Courier;
use App\CommissionHistory;
use App\Mail\ConversationMailManager;
use App\Order;
use App\Services\RajaOngkirService;
use Auth;
use Illuminate\Http\Request;
use App\Conversation;
use App\Message;
use Mail;


class ComplainProductController extends Controller
{
    public function complainProduct(Request $request)
    {
        $order_id = $request->order_id;
        $product_id = $request->product_id;

        //SAMPLE
        // $order_id = '20211023-10514268';
        // $product_id = 14;
        //ENDSAMPLE

        // dd($request->all());

        $order = Order::where('code', $order_id)->firstOrFail();
        $order_detail = $order->orderDetails()->where('product_id', $product_id)->firstOrFail();
        // dd($order_detail->toArray());

        if ($order_detail->complain()) {
            $data['order_detail'] = $order_detail;
        }

        // dd($order_detail->order()->first());

        return view('frontend.user.customer.complains.create_complain', $data);
    }

    public function complainOrder(Request $request)
    {
        $order_id = $request->order_id;

        // // $product_id = $request->product_id;

        // //SAMPLE
        //     // $order_id = '20211023-10514268';
        //     // $product_id = 14;
        // //ENDSAMPLE

        // // dd($request->all());

        $order = Order::where('code', $order_id)->firstOrFail();
        // $order_detail = $order->orderDetails()->where('product_id', $product_id)->firstOrFail();
        // // dd($order_detail->toArray());

        // if($order_detail->complain())

        // $data['order_detail'] = $order_detail;

        // // dd($order_detail->order()->first());

        $complain = Complain::where('order_id', $order->id)->get();
        if ($complain->isEmpty());{
            $complain->order = $order;
            $data['complain'] = $complain;
            // $data['order'] = $order;
            return view('frontend.user.customer.complains.create_complain_order', $data);
        }
        // dd($complain);
        return view('frontend.user.customer.complains.create_complain_order', $data);

    }

    public function store(Request $request)
    {

        $order_id = $request->order_id;
        $product_id = $request->product_id;

        //SAMPLE
        // $order_id = '20211023-10514268';
        // $product_id = 14;
        //ENDSAMPLE

        $order = Order::where('code', $order_id)->get();
        if ($order->isEmpty()) {
            flash(translate('Something wrong, please try again'))->warning();
            return redirect()->route('purchase_history.index');
        }
        $order = $order->first();
        $order_detail = $order->orderDetails()->where('product_id', $product_id)->firstOrFail();

        $complain = $order_detail->complain()->get();

        if ($complain->isEmpty()) {
            $complain = $order_detail->complain()->create([
                'order_id' => $order_detail->order_id,
                'order_detail_id' => $order_detail->id,
                'product_id' => $product_id,
                'seller_id' => $order_detail->seller_id,
                'user_id' => $order->user_id,
                'reason' => $request->reason,
                'status' => 'requested',
            ]);

            $order_detail->delivery_status = 'complained';
            $order_detail->save();

            $images = explode(',', $request->images);
            foreach ($images as $image) {
                $imageone = $complain->images()->create([
                    'complain_id' => $complain->id,
                    'thumbnail_img' => (int) $image,
                ]);
            }

            flash(translate('The Product has just been complained, Please procceed the refund step'))->success();
            return redirect()->route('complain.show', ['complain_slug' => encrypt($complain->id)]);
        }

        flash(translate('Complained product has being requested,  Please procceed the refund step'))->warning();
        return redirect()->route('complain.show', ['complain_slug' => encrypt($complain->first()->id)]);

    }

    public function storeOrder(Request $request)
    {

        // dd($request->all());

        $order_id = $request->order_id;

        //SAMPLE
        // $order_id = '20211023-10514268';
        // $product_id = 14;
        //ENDSAMPLE
        if(!$request->order_id || !$request->images || !$request->reason) {
            if(!$request->images) {
                flash(translate('You need to attach an image to create complain'))->warning();
            }
            if(!$request->reason) {
                flash(translate('You need to make reason to create complain'))->warning();
            }
            return back();
        }

        $order = Order::where('code', $order_id)->get();
        if ($order->isEmpty()) {
            flash(translate('Something wrong, please try again'))->warning();
            return back();
        }


        $order = $order->first();
        // $order_detail = $order->orderDetails()->where('product_id', $product_id)->firstOrFail();
        $complains = Complain::where('order_id', $order->id)->get();

        if ($complains->isEmpty()) {
            $complain = Complain::create([
                'order_id' => $order->id,
                'seller_id' => $order->seller_id,
                'user_id' => $order->user_id,
                'reason' => $request->reason,
                'status' => 'requested',
            ]);

            $order->orderDetails()->update([
                'delivery_status' => 'complained',
            ]);

            $conversation = new Conversation;
            $conversation->sender_id = $order->user_id;
            $conversation->receiver_id =  $order->seller_id;
            $conversation->title = 'Complain Order - #'.$order->code;
            $conversation->product_id = 0;
            $conversation->complain_id = $complain->id;

            if ($conversation->save()) {
                $message = new Message;
                $message->conversation_id = $conversation->id;
                $message->user_id = $order->user_id;
                $message->message = "Halo, Saya ingin mengajukan pengembalian dana";
    
                if ($message->save()) {
                    $this->send_message_to_seller($conversation, $message, 'customer');
                }
            }

            // $order_detail->delivery_status = 'complained';
            // $order_detail->save();

            $images = explode(',', $request->images);
            foreach ($images as $image) {
                $imageone = $complain->images()->create([
                    'complain_id' => $complain->id,
                    'thumbnail_img' => (int) $image,
                ]);
            }

            flash(translate('The Product has just been complained, Please procceed the refund step'))->success();
            return redirect()->route('complain.show', ['complain_slug' => encrypt($complain->id)]);
        }

        flash(translate('Complained product has being requested,  Please procceed the refund step'))->warning();
        return redirect()->route('complain.show', ['complain_slug' => encrypt($complain->first()->id)]);

    }

    public function send_message_to_seller($conversation, $message, $user_type)
    {
        $array['view'] = 'emails.conversation';
        $array['subject'] = 'Sender:- ' . Auth::user()->name;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = 'Hi! You recieved a message from ' . Auth::user()->name . '.';
        $array['sender'] = Auth::user()->name;

        if ($user_type == 'admin') {
            $array['link'] = route('conversations.admin_show', encrypt($conversation->id));
        } else {
            $array['link'] = route('conversations.show', encrypt($conversation->id));
        }

        $array['details'] = $message->message;

        try {
            Mail::to($conversation->receiver->email)->queue(new ConversationMailManager($array));
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }
    }

    public function show($id)
    {

        $complain = Complain::findOrFail(decrypt($id));
        $data['complain'] = $complain;
        // dd($complain->order()->get());
        // $data['order_detail'] = $complain->order_detail()->first();

        // dd($complain->getImageString());
        // dd($complain->order_detail()->first());
        if (Auth()->user()->user_type == 'customer') {
            return view('frontend.user.customer.complains.show_complain', $data);
        } else {
            return view('frontend.user.seller.complains.show_complain', $data);
        }
    }

    public function showBakup($id)
    {

        $complain = Complain::findOrFail(decrypt($id));
        $data['complain'] = $complain;
        $data['order_detail'] = $complain->order_detail()->first();

        // dd($complain->getImageString());
        // dd($complain->order_detail()->first());
        if (Auth()->user()->user_type == 'customer') {
            return view('frontend.user.customer.complains.show_complain', $data);
        } else {
            return view('frontend.user.seller.complains.show_complain', $data);
        }
    }

    public function approve($slug)
    {
        $id = decrypt($slug);
        $complain = Complain::findOrFail($id);

        $complain->status = 'accepted';
        $complain->save();

        return back();
    }

    public function form_delivery($id)
    {
        $complain = Complain::findOrFail(decrypt($id));
        // dd($c);
        // dd($complain->toArray());
        $couriers = Courier::all();
        $shop = Auth::user()->shop;
        return view('frontend.user.customer.complains.form_delivery', [
            // 'order' => $complain->order_detail()->first()->order()->first(),
            'complain' => $complain,
            'couriers' => $couriers,
            // 'shop' => $shop
        ]);
    }

    public function store_delivery($id, Request $request)
    {
        $complain = Complain::findOrFail(decrypt($id));
        // dd($complain->toArray());
        $complain->resi_number = $request->resi;
        $complain->resi_courier = $request->resi_courier;
        $complain->resi_courier_service = $request->resi_courier_service;
        $complain->status = 'procceeded';
        $complain->save();

        // dd($complain->toArray());

        return redirect()->route('complain.show', ['complain_slug' => $id]);
    }

    public function complete($id, Request $request)
    {
        $complain = Complain::findOrFail(decrypt($id));

        // dump($complain->toArray());
        // dump($complain->order_detail()->first()->toArray());
        if ($complain->resi_number && $complain->status == 'procceeded') {
            $rajaOngkir = new RajaOngkirService();
            $result = $rajaOngkir->track($complain->resi_number, $complain->resi_courier);
            if ($result['rajaongkir']['status']['code'] === 200) {
                if (env('APP_ENV')=='production' && $result['rajaongkir']['status']['code'] == 200) {
                    $amount = $complain->order()->first()->orderDetails()->sum('price');

                    // if ($result['rajaongkir']['result']['delivered']) {
                        $complain->status = 'completed';
                        $complain->save();
                        wallet_proccess($complain->user_id, $amount, 'Refunded', 'Product Refunded - #' . $complain->order()->first()->code, 'TOPUP');
                        wallet_proccess($complain->seller_id, $amount * -1, 'Refunded', 'Product Refunded - #' . $complain->order()->first()->code, 'DISBURSEMENT');

                        $order_detail = $complain->order()->first()->orderDetails(
                            ['is_refunded' => true]
                        );
                        // $order_detail->is_refunded = true;
                        // $order_detail->save();
                    // }

                    $is_complained = false;
                    foreach ($complain->order_detail()->get() as $order_detail) {
                        if ($order_detail->delivery_status == 'complained') {
                            $is_complained = true;
                        } else {
                            $is_complained = false;
                            break;
                        }
                    }
                    // if ($is_complained) {
                        $order = $complain->order()->first();
                        $order->delivery_status = 'completed';
                        $order->save();
                        
                        $order->orderDetails()->update([
                            'delivery_status' => 'completed'
                        ]);
                        // dd($order);
                    // }
                }
            }
            CommissionHistory::where('order_id', $complain->order_id)
                ->delete();
        }

        return redirect()->route('complain.show', ['complain_slug' => $id]);
        // dd($complain->toArray());
    }
}
