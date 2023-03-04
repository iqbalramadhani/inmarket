@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">Review Product</h5>
        </div>

        <table class="table table-borderless table-responsive">
            <thead>
            <tr>
                <th>#</th>
                <th>{{ translate('Product')}}</th>
                <th>{{ translate('Rating')}}</th>
                <th>{{ translate('Comment')}}</th>
            </tr>
            </thead>
            <tbody>
                
            @foreach ($order->orderDetails as $key => $orderDetail)
            @php 
                $review = $orderDetail->product->reviews->where('user_id', $orderDetail->order->user_id)->where('order_id', $orderDetail->order_id)->first() 
            @endphp
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>
                        @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                            <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">{{ $orderDetail->product->getTranslation('name') }}</a>
                        @elseif($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                            <a href="#" target="_blank">{{ $orderDetail->product->getTranslation('name') }}</a>
                        @else
                            <strong>{{  translate('Product Unavailable') }}</strong>
                        @endif
                    </td>
                    <td>
                        <div class="rating rating-sm">
                                @if($review!=null)
                                    {{ renderStarRating($review->rating) }}
                                @else
                                    -
                                @endif
                            </div>
                    </td>
                    <td>
                        @if($review!=null)
                            {{$review->comment}}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($review==null)
                            <a href="{{route('post-review.product.create', ['slug' => $orderDetail->product->slug, 'order_id' => $orderDetail->order_id])}}" class="btn btn-primary btn-xs">Beri ulasan</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
