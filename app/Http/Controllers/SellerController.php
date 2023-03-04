<?php

namespace App\Http\Controllers;

use App\Notifications\EmailVerificationNotification;
use App\Order;
use App\OrderDetail;
use App\Product;
use App\Seller;
use App\Shop;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\SellerApproved;
use Mail;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $approved = null;
        $sellers = Seller::whereIn('user_id', function ($query) {
            $query->select('id')
                ->from(with(new User)->getTable());
        })->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'seller')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')->orWhere('email', 'like', '%' . $sort_search . '%');
            })->pluck('id')->toArray();
            $sellers = $sellers->where(function ($seller) use ($user_ids) {
                $seller->whereIn('user_id', $user_ids);
            });
        }
        if ($request->approved_status != null) {
            $approved = $request->approved_status;
            $sellers = $sellers->where('verification_status', $approved);
        }
        $sellers = $sellers->paginate(15);
        return view('backend.sellers.index', compact('sellers', 'sort_search', 'approved'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.sellers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (User::where('email', $request->email)->first() != null) {
            flash(translate('Email already exists!'))->error();
            return back();
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_type = "seller";
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            if (get_setting('email_verification') != 1) {
                $user->email_verified_at = date('Y-m-d H:m:s');
            } else {
                $user->notify(new EmailVerificationNotification());
            }
            $user->save();

            $seller = new Seller;
            $seller->user_id = $user->id;

            if ($seller->save()) {
                $shop = new Shop;
                $shop->user_id = $user->id;
                $shop->slug = 'demo-shop-' . $user->id;
                $shop->save();

                flash(translate('Seller has been inserted successfully'))->success();
                return redirect()->route('sellers.index');
            }
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $seller = Seller::findOrFail(decrypt($id));
        return view('backend.sellers.edit', compact('seller'));
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
        $seller = Seller::findOrFail($id);
        $user = $seller->user;
        $user->name = $request->name;
        $user->email = $request->email;
        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
        }
        if ($user->save()) {
            if ($seller->save()) {
                flash(translate('Seller has been updated successfully'))->success();
                return redirect()->route('sellers.index');
            }
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $seller = Seller::findOrFail($id);

        Shop::where('user_id', $seller->user_id)->delete();

        Product::where('user_id', $seller->user_id)->delete();

        $orders = Order::where('user_id', $seller->user_id)->get();
        Order::where('user_id', $seller->user_id)->delete();

        foreach ($orders as $key => $order) {
            OrderDetail::where('order_id', $order->id)->delete();
        }

        User::destroy($seller->user->id);

        if (Seller::destroy($id)) {
            flash(translate('Seller has been deleted successfully'))->success();
            return redirect()->route('sellers.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function bulk_seller_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $seller_id) {
                $this->destroy($seller_id);
            }
        }

        return 1;
    }

    public function show_verification_request($id)
    {
        $seller = Seller::findOrFail($id);
        return view('backend.sellers.verification', compact('seller'));
    }

    public function approve_seller($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->verification_status = 1;
        if ($seller->save()) {
            flash(translate('Seller has been approved successfully'))->success();
            return redirect()->route('sellers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function reject_seller($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->verification_status = 0;
        $seller->verification_info = null;
        if ($seller->save()) {
            flash(translate('Seller verification request has been rejected successfully'))->success();
            return redirect()->route('sellers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function payment_modal(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        return view('backend.sellers.payment_modal', compact('seller'));
    }

    public function profile_modal(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        return view('backend.sellers.profile_modal', compact('seller'));
    }

    public function updateApproved(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        $seller->verification_status = $request->status;
        if ($seller->save()) {
            $template = new SellerApproved($seller);
            Mail::to($seller->user->email)->send($template);
            return 1;
        }
        return 0;
    }

    public function login($id)
    {
        $seller = Seller::findOrFail(decrypt($id));

        $user = $seller->user;

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id)
    {
        $seller = Seller::findOrFail($id);

        if ($seller->user->banned == 1) {
            $seller->user->banned = 0;
            flash(translate('Seller has been unbanned successfully'))->success();
        } else {
            $seller->user->banned = 1;
            flash(translate('Seller has been banned successfully'))->success();
        }

        $seller->user->save();
        return back();
    }

    public function download()
    {

        // $seller = DB::table('sellers')
        //     ->join('shops', 'sellers.user_id', '=', 'shops.user_id')
        //     ->join('users', 'sellers.user_id', '=', 'users.id')
        //     ->get();
        $seller = Seller::with('user.shop')->get();

        $header = [
            'No',
            'Nama',
            'Email',
            'Phone',
            'Status Verifikasi',
            'Nama Usaha',
            'Alamat Toko',
            'No. Kontak Usaha',
            'Facebook',
            'Youtube',
            'Google',
            'Twitter',
            'Tanggal Join',
            'Details',
        ];
        $newData = [];
        $newData[] = $header;
        $i = 1;
        foreach ($seller as $row) {
            $text = "";
            $newDataTemp = [];
            $newDataTemp = [
                $i,
                $row->user->name,
                $row->user->email,
                $row->user->phone,
                ($row->verification_status) ? 'Terverifikasi' : 'Belum Terverifikasi',
                $row->user->shop->name,
                $row->user->shop->address,
                $row->user->shop->phone,
                $row->user->shop->facebook,
                $row->user->shop->youtube,
                $row->user->shop->google,
                $row->user->shop->twitter,
                $row->created_at,
            ];
            // if(!count(json_decode($row->verification_info))) {
                //     continue;
                // }
            if ($row->verification_info != null) {
                foreach (json_decode($row->verification_info) as $data) {
                    $text .= $data->label . " : " . $data->value;
                }
            }
            $row->details = $text;
            $newDataTemp[] = $text;
            $newData[] = $newDataTemp;
            
            $i++;
        }

        $filename = time() . ".xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $heading = false;
        if (!empty($newData)) {
            foreach ($newData as $row) {
                echo implode("\t", array_values($row)) . "\n";
            }
        }

        exit;

        // dd($newData);
        // echo "asdasd";
    }
}
