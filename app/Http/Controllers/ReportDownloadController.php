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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DownloadSellerSaleReport;
use App\Exports\DownloadStockReport;
use App\Exports\DownloadWishlistReport;
use App\Exports\DownloadUserSearchReport;
use App\Exports\DownloadInhouseSaleReport;
use App\Exports\DownloadCommissionHistoryReport;
use App\Exports\DownloadWalletTransactionHistoryReport;

class ReportDownloadController extends Controller
{
    public function download_seller_sale_report(Request $request)
    {
        $sort_by =null;
        $sellers = Seller::orderBy('created_at', 'desc');
        if ($request->has('verification_status')){
            $sort_by = $request->verification_status;
            $sellers = $sellers->where('verification_status', $sort_by);
        }
        $sellers = $sellers->get();
        $filename = 'laporan-penjualan-produk-penjual'.'.xlsx';
        return Excel::download(new DownloadSellerSaleReport($sellers), $filename);
    }

    public function download_stock_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->get();
        $filename = 'laporan-stok-produk'.'.xlsx';
        return Excel::download(new DownloadStockReport($products), $filename);
    }

    public function download_wish_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->get();
        $filename = 'laporan-wishlist-produk'.'.xlsx';
        return Excel::download(new DownloadWishlistReport($products), $filename);
    }

    public function download_user_search_report(Request $request){
        $searches = Search::orderBy('count', 'desc')->get();
        $filename = 'laporan-pencarian-produk'.'.xlsx';
        return Excel::download(new DownloadUserSearchReport($searches), $filename);
    }

    public function download_in_house_sale_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('num_of_sale', 'desc')->where('added_by', 'admin');
        if ($request->has('category_id')){
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->get();
        $filename = 'laporan-penjualan-produk-internal'.'.xlsx';
        return Excel::download(new DownloadInhouseSaleReport($products), $filename);    
    }

    public function download_commission_history(Request $request) {
        $seller_id = null;
        $date_range = null;
        
        if(Auth::user()->user_type == 'seller') {
            $seller_id = Auth::user()->id;
        } if($request->seller_id) {
            $seller_id = $request->seller_id;
        }
        
        $commission_history = CommissionHistory::orderBy('created_at', 'desc');
        
        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $commission_history = $commission_history->where('created_at', '>=', $date_range1[0]);
            $commission_history = $commission_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($seller_id){
            $commission_history = $commission_history->where('seller_id', '=', $seller_id);
        }
        
        $commission_history = $commission_history->with('order.orderDetails')->get();
        $filename = 'laporan-riwayat-komisi'.'.xlsx';
        return Excel::download(new DownloadCommissionHistoryReport($commission_history), $filename);
    }

    public function download_wallet_transaction_history(Request $request) {
        $user_id = null;
        $date_range = null;
        
        if($request->user_id) {
            $user_id = $request->user_id;
        }

        $wallet_history = Wallet::orderBy('created_at', 'desc');
        
        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $wallet_history = $wallet_history->where('created_at', '>=', $date_range1[0]);
            $wallet_history = $wallet_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($user_id){
            $wallet_history = $wallet_history->where('user_id', '=', $user_id);
        }
        
        $wallets = $wallet_history->get();
        $filename = 'laporan-riwayat-isi-ulang-dompet'.'.xlsx';
        return Excel::download(new DownloadWalletTransactionHistoryReport($wallets), $filename);
    }
}