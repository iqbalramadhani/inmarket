<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Product;
use Auth;
use App\Order;
use Illuminate\Http\Request;

class CreateReviewProductController extends Controller
{
    public function index($order_code)
    {
        $order = Order::where('code', $order_code)->firstOrFail();
        return view('frontend.user.customer.reviews.create_review',[
            'order' => $order,
        ]);
    }

    public function reviewProduct($order_id, $slug)
    {
        
        $product = Product::where('slug', $slug)->firstOrFail();
        $review = Review::where('user_id', Auth::user()->id)->where('order_id', $order_id)->where('product_id', $product->id)->get();
        if($review->isNotEmpty()) {
            flash(translate('You have reviewed the product'))->warning();
            return back();
        }

        return view('frontend.user.customer.reviews.create_product_review',[
            'product' => $product,
            'order_id' => $order_id,
        ]);
    }

    public function store(Request $request, $order_id, $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $order = Order::findOrFail($order_id);
        $orderDetail = $order->orderDetails->where('product_id', $product->id)->first()->firstOrFail();

        Review::create([
            'product_id' => $product->id,
            'user_id' => \Auth::user()->id,
            'rating' => (int)$request->rating,
            'order_id' => (int)$order_id,
            'comment' => $request->comment,
            'status' => 1
        ]);
        
        $orderDetail->update([
            'is_reviewed' => true
        ]);

        return redirect()->route('post-review.create', ['order_code' => $order->code]);
    }
}