<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Wallet;
use Artisan;
use Cache;
use CoreComponentRepository;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_dashboard(Request $request)
    {   
        CoreComponentRepository::initializeCache();
        $root_categories = Category::where('level', 0)->get();

        $cached_graph_data = Cache::remember('cached_graph_data', 86400, function() use ($root_categories){
            $num_of_sale_data = [];
            $qty_data = null;
            $list_categori = [];
            $verified_sellers = verified_sellers_id();
            $list_id = [];
            $no=0;
            foreach ($root_categories as $key => $category){
                $category_ids = \App\Utility\CategoryUtility::children_ids($category->id);
                $category_ids[] = $category->id;
                $list_categori[] = $category->id;

                $products = Product::with('stocks')->whereIn('category_id', $category_ids)->whereIn('user_id',$verified_sellers)->get();
                $qty = 0;
                
                foreach ($products as $key => $product) {
                    foreach ($product->stocks as $key => $stock) {
                        $qty += $stock->qty;
                    }
                }

                $sale = DB::table('products')
                    ->join('sellers','sellers.id','=','products.user_id')
                    ->join('orders','orders.seller_id','=','sellers.id')
                    ->where('orders.delivery_status','delivered')
                    ->where('products.category_id',$category->id)
                    ->whereIn('products.user_id',$verified_sellers)
                    ->groupBy('orders.id')
                    ->get(['orders.id']);
                
                $orders = [];

                foreach($sale as $row){
                    $orders[] = $row->id;
                }

                $gabung  = implode(',',$orders);

                foreach($list_id as $row => $list){
                    if($list == $gabung){
                        $list_id[$row] = "";
                        $num_of_sale_data[$row] = 0;
                    }
                }

                $list_id[] = $gabung;
                
                $qty_data .= $qty.',';
                $num_of_sale_data[$no] = $sale->count();
                $no++;
            }

            $item['num_of_sale_data'] = implode(',',$num_of_sale_data);
            $item['qty_data'] = $qty_data;

            return $item;
        });

        $new_customer = \App\Models\Customer::whereMonth('created_at', (int)date('m'))->whereYear('created_at', (int)date('Y'))->count();
        $new_seller = \App\Models\Seller::whereMonth('created_at', (int)date('m'))->whereYear('created_at', (int)date('Y'))->count();

        $status = $request->status ?? '0';
        $provinsi_2 = $request->provinsi_2 ?? '';

        $seller = DB::table('sellers')
            ->select(['rajaongkir_provinces.province_name as province','sellers.verification_status',DB::raw('count(sellers.id) as jumlah')])
            ->join('users', 'sellers.user_id', '=', 'users.id')
            ->join('addresses', 'users.id', '=', 'addresses.user_id')
            ->join('rajaongkir_provinces','rajaongkir_provinces.province_id','=','addresses.province_id')
            ->where('sellers.verification_status', '1')
            ->groupBy('addresses.province_id')
            ->orderByDesc('jumlah')
            ->limit(3)->get();

        $buyer = DB::table('orders')
            ->select(['orders.*', 'shops.user_id as seller_id', 'shops.name as shop_name','rajaongkir_provinces.province_name as address_province',DB::raw('count(orders.id) as jumlah')])
            ->join('shops','shops.user_id','=','orders.seller_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('addresses', 'users.id', '=', 'addresses.user_id')
            ->join('rajaongkir_provinces','rajaongkir_provinces.province_id','=','addresses.province_id')
            ->where('orders.delivery_status','delivered')
            ->groupBy('addresses.province_id')
            ->orderByDesc('jumlah')
            ->get();
        
        $products = \App\Models\Product::with('category')->whereHas('category')->paginate(3);

        $queryGetCommision = OrderDetail::where('payment_status', 'paid')
            ->where('delivery_status', 'confirmed')
            ->whereMonth('created_at', (int)date('m'))
            ->get();
        $total_commission_seller = $queryGetCommision->sum('total_commission_seller');
        $total_commission_inatrade = $queryGetCommision->sum('total_commission_inatrade');

        $total_wallet_disbusrsement = Wallet::where('type', 'DISBURSEMENT')->whereMonth('created_at', (int)date('m'))->sum('amount');

        return view('backend.dashboard', compact('root_categories', 'cached_graph_data', 'seller', 'buyer', 'products', 'new_customer', 'new_seller','total_commission_seller','total_commission_inatrade','total_wallet_disbusrsement'));
    }

    function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        flash(translate('Cache cleared successfully'))->success();
        return back();
    }
}
