<?php

namespace App\Http\Controllers;

use App\BusinessSetting;
use App\Conversation;
use App\Mail\ConversationMailManager;
use App\Message;
use App\Product;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Mail;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
            $conversations = Conversation::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(5);
            return view('frontend.user.conversations.index', compact('conversations'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
            return back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_index()
    {
        if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
            $conversations = Conversation::orderBy('created_at', 'desc')->get();
            return view('backend.support.conversations.index', compact('conversations'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $seller_id = $request->seller_id;
        $customer_id = $request->customer_id;
        $product_id = $request->product_id;
        
        // $user = Auth()->user();
        
        // dd($request->all());
        if (!empty($request->complain_id)) {
            $complain_id = $request->complain_id;
            $conversation = Conversation::where('sender_id', $customer_id)->where('receiver_id', $seller_id)->where('complain_id', $complain_id)->first();
            
            // dd($conversation->toArray());
            if (!empty($conversation)) {
                return redirect()->route('conversations.show', encrypt($conversation->id));
            } else {
                $seller = User::find($seller_id);

                $conversation = new Conversation();
                $conversation->sender_id = $customer_id;
                $conversation->receiver_id = $seller_id;
                $conversation->product_id = $product_id;
                $conversation->complain_id = $complain_id;
            }

            return view('frontend.user.conversations.create_by_complain', compact('conversation'));
        } else {
            $conversation = Conversation::where('sender_id', $customer_id)->where('receiver_id', $seller_id)->where('product_id', $product_id)->first();

            if (!empty($conversation)) {
                return redirect()->route('conversations.show', encrypt($conversation->id));
            } else {
                $seller = User::find($seller_id);

                $conversation = new Conversation();
                $conversation->sender_id = $customer_id;
                $conversation->receiver_id = $seller_id;
                $conversation->product_id = $product_id;
                $conversation->complain_id = null;
            }
        }

        // dd($conversation->toArray());
        return view('frontend.user.conversations.create', compact('conversation'));

        // dd($array);
    }
    
    public function createForOrder(Request $request)
    {
        $seller_id = $request->seller_id;
        $customer_id = $request->customer_id;
        $product_id = $request->product_id;
        
        // $user = Auth()->user();
        
        if (!empty($request->complain_id)) {
            $complain_id = $request->complain_id;
            $conversation = Conversation::where('sender_id', $customer_id)->where('receiver_id', $seller_id)->where('product_id', $product_id)->where('complain_id', $complain_id)->first();
            
            if (!empty($conversation)) {
                return redirect()->route('conversations.show', encrypt($conversation->id));
            } else {
                $seller = User::find($seller_id);

                $conversation = new Conversation();
                $conversation->sender_id = $customer_id;
                $conversation->receiver_id = $seller_id;
                $conversation->product_id = $product_id;
                $conversation->complain_id = $complain_id;
            }

            return view('frontend.user.conversations.create_by_complain', compact('conversation'));
        } else {
            $conversation = Conversation::where('sender_id', $customer_id)->where('receiver_id', $seller_id)->where('product_id', $product_id)->first();

            if (!empty($conversation)) {
                return redirect()->route('conversations.show', encrypt($conversation->id));
            } else {
                $seller = User::find($seller_id);

                $conversation = new Conversation();
                $conversation->sender_id = $customer_id;
                $conversation->receiver_id = $seller_id;
                $conversation->product_id = $product_id;
                $conversation->complain_id = null;
            }
        }

        // dd($conversation->toArray());
        return view('frontend.user.conversations.create', compact('conversation'));

        // dd($array);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        if($request->complain_id) {
            $user_type = 'customer'; 
        } else {
            $user_type = Product::findOrFail($request->product_id)->user->user_type;
        }
        
        $conversation = new Conversation;
        $conversation->sender_id = ($request->sender_id) ?? Auth::user()->id;
        $conversation->receiver_id =  ($request->receiver_id) ?? Product::findOrFail($request->product_id)->user->id;
        $conversation->title = $request->title;
        $conversation->product_id = $request->product_id ?? 0;
        $conversation->complain_id = $request->complain_id ?? null;

        if ($conversation->save()) {
            $message = new Message;
            $message->conversation_id = $conversation->id;
            $message->user_id = Auth::user()->id;
            $message->message = $request->message;

            if ($message->save()) {
                $this->send_message_to_seller($conversation, $message, $user_type);
            }
        }

        flash(translate('Message has been send to seller'))->success();
        return back();
    }
    
    public function storeForOrder(Request $request)
    {
        $user_type = Product::findOrFail($request->product_id)->user->user_type;

        $conversation = new Conversation;
        $conversation->sender_id = Auth::user()->id;
        $conversation->receiver_id = Product::findOrFail($request->product_id)->user->id;
        $conversation->title = $request->title;
        $conversation->product_id = $request->product_id;
        $conversation->complain_id = $request->complain_id ?? null;

        if ($conversation->save()) {
            $message = new Message;
            $message->conversation_id = $conversation->id;
            $message->user_id = Auth::user()->id;
            $message->message = $request->message;

            if ($message->save()) {
                $this->send_message_to_seller($conversation, $message, $user_type);
            }
        }

        flash(translate('Message has been send to seller'))->success();
        return back();
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == Auth::user()->id) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();
        
        return view('frontend.user.conversations.show', compact('conversation'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $conversation = Conversation::findOrFail(decrypt($request->id));
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
            $conversation->save();
        } elseif ($conversation->receiver_id == Auth::user()->id) {
            $conversation->receiver_viewed = 1;
            $conversation->save();
        }
        return view('frontend.partials.messages', compact('conversation'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin_show($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == Auth::user()->id) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();
        return view('backend.support.conversations.show', compact('conversation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        foreach ($conversation->messages as $key => $message) {
            $message->delete();
        }
        if (Conversation::destroy(decrypt($id))) {
            flash(translate('Conversation has been deleted successfully'))->success();
            return back();
        }
    }
}
