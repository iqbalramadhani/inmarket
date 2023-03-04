<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CommissionHistory;
use App\Models\Wallet;
use App\Models\Seller;
use App\Models\User;
use App\Models\Search;
use Auth;
use DataTables;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function stock_report(Request $request)
    {
        $sort_by =null;
        $verified_sellers = verified_sellers_id();
        $products = Product::whereIn('user_id',$verified_sellers)->orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            if ($request->category_id != null && $request->category_id != ''){
                $products = $products->where('category_id', $request->category_id);
            }
        }
        $data = $products->with('stocks', 'category')->get();

        if ($request->ajax()) {
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('stock', function($data){
                    $qty = 0;
                    foreach ($data->stocks as $key => $stock) {
                        $qty += $stock->qty;
                    }
                    return $qty;
                })
                ->make(true);
        }

        return view('backend.reports.stock_report');
    }

    public function in_house_sale_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('num_of_sale', 'desc')->where('added_by', 'admin');
        if ($request->has('category_id')){
            if ($request->category_id != null && $request->category_id != ''){
                $products = $products->where('category_id', $request->category_id);
            }
        }
        $data = $products->get();

        if ($request->ajax()) {
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('name', function($data){
                    return $data->getTranslation('name');
                })
                ->make(true);
        }
        return view('backend.reports.in_house_sale_report');
    }

    public function seller_sale_report(Request $request)
    {
        $sellers = Seller::orderBy('created_at', 'desc');
        if ($request->has('verification_status')){
            if ($request->verification_status != null && $request->verification_status != ''){
                $sellers = $sellers->where('verification_status', $request->verification_status);
            }
        }
        $data = $sellers->get();

        if ($request->ajax()) {
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('seller_name', function($data){
                    return $data->user ? $data->user->name : '-';
                })
                ->addColumn('shop_name', function($data){
                    return $data->user->shop ? $data->user->shop->name : '-';
                })
                ->addColumn('num_of_sale', function($data){
                    return \App\Models\Order::where('seller_id', $data->user->id)->where('delivery_status','delivered')->count('id');
                })
                ->addColumn('order_amount', function($data){
                    return convert_price(\App\Models\OrderDetail::where('seller_id', $data->user->id)->where('delivery_status','delivered')->sum('price'));
                })
                ->addColumn('verification_status', function($data){
                    return $data->verification_status == 1 ? translate('Approved') : translate('Non Approved');
                })
                ->make(true);
        }
        return view('backend.reports.seller_sale_report');
    }

    public function wish_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            if ($request->category_id != null && $request->category_id != ''){
                $products = $products->where('category_id', $request->category_id);
            }
        }
        $data = $products->with('wishlists', 'category')->get();

        if ($request->ajax()) {
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('wishlist', function($data){
                    return $data->wishlists->count();
                })
                ->make(true);
        }
        return view('backend.reports.wish_report');
    }

    public function user_search_report(Request $request){
        $data = Search::orderBy('count', 'desc')->get();
        if ($request->ajax()) {
            return Datatables::of($data)->addIndexColumn()
                ->make(true);
        }
        return view('backend.reports.user_search_report');
    }
    
    public function commission_history(Request $request) {
        $seller_id = null;
        $date_range = null;
        
        if(Auth::user()->user_type == 'seller') {
            $seller_id = Auth::user()->id;
        } if($request->user_id) {
            $seller_id = $request->user_id;
        }
        
        $commission_history = CommissionHistory::orderBy('created_at', 'desc');
        
        if ($request->min_date != null && $request->min_date != '') {
            $commission_history = $commission_history->where('created_at', '>=', $request->min_date);
            $commission_history = $commission_history->where('created_at', '<=', $request->max_date);
        }

        if ($seller_id != null && $seller_id != '') {
            $commission_history = $commission_history->where('seller_id', '=', $seller_id);
        }
        
        $data = $commission_history->with('order.orderDetails')->get();

        if ($request->ajax()) {
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('order_code', function($data){
                    return isset($data->order) ? $data->order->code : translate('Order Deleted');
                })
                ->addColumn('admin_commission', function($data){
                    return $data->order->orderDetails[0]->total_commission_inatrade;
                })
                ->addColumn('seller_commission', function($data){
                    return $data->order->orderDetails[0]->total_commission_seller;
                })
                ->addColumn('shipping_cost', function($data){
                    return $data->order->orderDetails[0]->shipping_cost;
                })
                ->addColumn('date', function($data){
                    return date('d-m-Y', strtotime($data->created_at));
                })
                ->make(true);
        }

        if(Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.reports.commission_history_report', compact('seller_id'));
        }
        return view('backend.reports.commission_history_report', compact('seller_id'));
    }
    
    public function wallet_transaction_history(Request $request) {
        
        $users_with_wallet = User::whereIn('id', function($query) {
            $query->select('user_id')->from(with(new Wallet)->getTable());
        })->get();

        $wallet_history = Wallet::orderBy('created_at', 'desc');

        if($request->user_id != null && $request->user_id != '') {
            $wallet_history = $wallet_history->where('user_id', '=', $request->user_id);
        }

        if ($request->min_date != null && $request->min_date != '') {
            $wallet_history = $wallet_history->whereDate('created_at', '>=', $request->min_date);
            $wallet_history = $wallet_history->whereDate('created_at', '<=', $request->max_date);
        }
        
        $data = $wallet_history->get();

        if ($request->ajax()) {
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('customer', function($data){
                    return $data->user ? $data->user->name : translate('User Not found');
                })
                ->addColumn('date', function($data){
                    return date('d-m-Y', strtotime($data->created_at));
                })
                ->addColumn('amount', function($data){
                    return convert_price($data->amount);
                })
                ->addColumn('payment_method', function($data){
                    return ucfirst(str_replace('_', ' ', $data->payment_method));
                })
                ->make(true);
        }

        return view('backend.reports.wallet_history_report', compact('users_with_wallet'));
    }

    public function buyer_province(Request $request) {
        $buyer = DB::table('orders')
            ->select(['orders.*', 'shops.user_id as seller_id', 'shops.name as shop_name','rajaongkir_provinces.province_name as address_province',DB::raw('count(orders.id) as amount')])
            ->join('shops','shops.user_id','=','orders.seller_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('addresses', 'users.id', '=', 'addresses.user_id')
            ->join('rajaongkir_provinces','rajaongkir_provinces.province_id','=','addresses.province_id')
            ->where('orders.delivery_status','delivered')
            ->groupBy('addresses.province_id');

        if (isset($request->provinsi_1)) {
            $buyer->where('addresses.province_id', $request->provinsi_1);
        }

        $data = $buyer->get();

        if ($request->ajax()) {
            return Datatables::of($data)->make(true);
        }

        return view('backend.reports.buyer_of_province');
    }

    public function seller_count(Request $request){
        $seller = DB::table('sellers')
            ->select(['rajaongkir_provinces.province_name as province','sellers.verification_status',DB::raw('count(sellers.id) as jumlah')])
            ->join('users', 'sellers.user_id', '=', 'users.id')
            ->join('addresses', 'users.id', '=', 'addresses.user_id')
            ->join('rajaongkir_provinces','rajaongkir_provinces.province_id','=','addresses.province_id')
            ->where('sellers.verification_status', '1')
            ->groupBy('addresses.province_id');

        if (isset($request->province)) {
            $seller->where('addresses.province_id',$request->province);
        }
        
        $data = $seller->get();
        
        if ($request->ajax()) {
            return Datatables::of($data)->make(true);
        }

        $data_provinsi = DB::table('indonesia_provinces')->get();
        
        return view('backend.reports.seller_count',compact('data_provinsi'));
    }
}
