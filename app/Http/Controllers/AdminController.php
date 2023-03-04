<?php

namespace App\Http\Controllers;

use App\Category;
use App\OrderDetail;
use App\Product;
use App\Wallet;
use Cache;
use Illuminate\Http\Request;
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
        $root_categories = Category::where('level', 0)->get();

        $cached_graph_data = Cache::remember('cached_graph_data', 86400, function () use ($root_categories) {
            $num_of_sale_data = null;
            $qty_data = null;
            foreach ($root_categories as $key => $category) {
                $category_ids = \App\Utility\CategoryUtility::children_ids($category->id);
                $category_ids[] = $category->id;

                $num_of_sale_data .= Product::whereIn('category_id', $category_ids)->sum('num_of_sale') . ',';

                $products = Product::whereIn('category_id', $category_ids)->get();
                $qty = 0;
                foreach ($products as $key => $product) {
                    foreach ($product->stocks as $key => $stock) {
                        $qty += $stock->qty;
                    }
                }
                $qty_data .= $qty . ',';
            }
            $item['num_of_sale_data'] = $num_of_sale_data;
            $item['qty_data'] = $qty_data;

            return $item;
        });

        $status = $request->status ?? '0';
        $provinsi_2 = $request->provinsi_2 ?? '';

        $seller = DB::table('sellers')
            ->leftJoin('users', 'user_id', '=', 'users.id')
            ->leftJoin('addresses', 'users.id', '=', 'addresses.user_id')
            ->where('addresses.province', 'LIKE', '%' . $provinsi_2 . '%')
            ->where('sellers.verification_status', 'LIKE', '%' . $status . '%')
            ->where('addresses.province', '<>', '')
            ->where('addresses.province', '<>', null)
            ->groupBy('sellers.verification_info', 'addresses.province')
            ->paginate();

        foreach ($seller->items() as $row) {
            $row->jumlah = DB::table('sellers')
                ->leftJoin('users', 'user_id', '=', 'users.id')
                ->leftJoin('addresses', 'users.id', '=', 'addresses.user_id')
                ->where('addresses.province', 'LIKE', $row->province)
                ->where('addresses.province', '<>', '')
                ->where('addresses.province', '<>', null)
                ->where('sellers.verification_status', $row->verification_status)
                ->count();
        }

        $buyer = DB::table('shops')
            ->leftJoin('users', 'user_id', '=', 'users.id')
            ->leftJoin('addresses', 'users.id', '=', 'addresses.user_id')
            ->leftJoin('sellers', 'users.id', '=', 'sellers.user_id')
            ->select(['*', 'sellers.id as seller_id', 'shops.name as shop_name', 'addresses.province as address_province'])
            ->groupBy('users.id', 'addresses.user_id');
        if (isset($request->nama_usaha)) {
            $buyer->where('addresses.province', 'LIKE', '%' . $request->nama_usaha . '%');
        }
        if (isset($request->provinsi_1)) {
            $buyer->where('addresses.province', 'LIKE', '%' . $request->provinsi_1 . '%');
        }

        $buyer = $buyer->paginate(3);
        foreach ($buyer->items() as $row) {
            $row->jumlah = DB::table('orders')->where('seller_id', $row->seller_id)->where('delivery_status', 'delivered')->count();
        }

        $kategori = $_GET['kategori'] ?? '';
        $produk = $_GET['produk'] ?? '';
        $products = \App\Product::with('category')->whereHas('category', function ($query) use ($kategori) {
            return $query->where('name', 'like', '%' . $kategori . '%');
        })->where('name', 'like', '%' . $produk . '%')->paginate(3);

        $new_customer = \App\Customer::whereMonth('created_at', (int)date('m'))->whereYear('created_at', (int)date('Y'))->count();
        $new_seller = \App\Seller::whereMonth('created_at', (int)date('m'))->whereYear('created_at', (int)date('Y'))->count();

        $queryGetCommision = OrderDetail::where('payment_status', 'paid')
            ->where('delivery_status', 'confirmed')
            ->whereMonth('created_at', (int)date('m'))
            ->get();
        $total_commission_seller = $queryGetCommision->sum('total_commission_seller');
        $total_commission_inatrade = $queryGetCommision->sum('total_commission_inatrade');

        $total_wallet_disbusrsement = Wallet::where('type', 'DISBURSEMENT')->whereMonth('created_at', (int)date('m'))->sum('amount');

        return view('backend.dashboard', compact('root_categories', 'cached_graph_data',
            'seller', 'buyer', 'products', 'new_customer', 'new_seller', 'total_commission_seller',
            'total_commission_inatrade',
        'total_wallet_disbusrsement'
        ));
    }
}
