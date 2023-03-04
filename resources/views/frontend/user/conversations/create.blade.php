@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="h6">
            <span>{{ translate('Conversations With ')}}</span>
            @if ($conversation->sender_id == Auth::user()->id && $conversation->receiver->shop != null)
                <a href="{{ route('shop.visit', $conversation->receiver->shop->slug) }}" class="">{{ $conversation->receiver->shop->name }}</a>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title fs-16 fw-600 mb-0">#{{ $conversation->title }}
            (
                {{ translate('Between you and') }}
                @if ($conversation->sender_id == Auth::user()->id)
                    {{ $conversation->receiver->name }}
                @else
                    {{ $conversation->sender->name ?? null }}
                @endif
            )
            </h5>
        </div>

        <div class="card-body">
            <ul class="list-group list-group-flush">
                @if($conversation->product)
                    <div class="form-group">
                        <div>
                            <div class="row gutters-10 align-items-center">
                                <div class="col-2">
                                    <a href="{{ route('product', $conversation->product->slug) }}" class="d-block text-reset">
                                        <img
                                            class="img-fit lazyload h-xxl-110px h-xl-80px h-120px"
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="{{ uploaded_asset($conversation->product->thumbnail_img) }}"
                                            alt="{{ $conversation->product->getTranslation('name') }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                            style="border: 1px solid #e2e5ec; opacity: 1; padding: 5px 5px 5px 5px;"
                                        >
                                    </a>
                                </div>
                                <div class="col-7 text-left">
                                    <h4 class="fs-13 text-truncate-2">
                                        <a href="{{ route('product', $conversation->product->slug) }}" class="d-block text-reset">{{ $conversation->product->getTranslation('name') }}</a>
                                    </h4>
                                    <div class="rating rating-sm mt-1">
                                        {{ renderStarRating($conversation->product->rating) }}
                                    </div>
                                    <div class="mt-2">
                                        <span class="fs-17 fw-600 text-primary">{{ home_discounted_base_price($conversation->product) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </ul>
            <form class="pt-4" action="{{ route('conversations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $conversation->product_id }}">
                <input type="hidden" name="receiver_id" value="{{ $conversation->receiver_id }}">
                <input type="hidden" name="sender_id" value="{{ $conversation->sender_id }}">
                <div class="form-group">
                    <textarea class="form-control" rows="4" name="title" placeholder="{{ translate('Type your reply') }}" required></textarea>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{ translate('Send') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
    function refresh_messages(){
        $.post('{{ route('conversations.refresh') }}', {_token:'{{ @csrf_token() }}', id:'{{ encrypt($conversation->id) }}'}, function(data){
            $('#messages').html(data);
        })
    }

    refresh_messages(); // This will run on page load
    setInterval(function(){
        refresh_messages() // this will run after every 5 seconds
    }, 4000);
    </script>
@endsection
