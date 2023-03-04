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
                    {{ $conversation->sender->name }}
                @endif
            )
            </h5>
        </div>

        <div class="card-body">
            <ul class="list-group list-group-flush">
                @if($conversation->complain_id)
                <div class="row">

                    <div class="col-6">
                        <span class="d-block mt-0"></span>
                        <span class="h6 text-muted mb-1 d-block">Alasan Komplain : </span>
                        <p class="d-block">{{$conversation->complain()->first()->reason}}</p>
                    </div>
                    <div class="col-6 h6 d-block text-muted d-block">Status : <span class="fw-700 text-uppercase text-danger">{{$conversation->complain->first()->status}}</span></div>
                    <div class="col-12">
                        <!-- <span class="h6 text-muted mb-1 d-block">Bukti : </span> -->
                        @foreach($conversation->complain->images()->get() as $image)
                        <img class="col-2 p-2" src="{{url('public/'.$image->detail()->file_name)}}" alt="{{$image->detail()->file_original_name}}">
                        @endforeach
                    </div>
                </div>
                    @endif

                    @foreach($conversation->messages as $message)
                    <li class="list-group-item px-0">
                        <div class="media mb-2">
                          <img class="avatar avatar-xs mr-3" @if($message->user != null) src="{{ uploaded_asset($message->user->avatar_original) }}" @endif onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                          <div class="media-body">
                            <h6 class="mb-0 fw-600">
                                @if ($message->user != null)
                                    {{ $message->user->name }}
                                @endif
                            </h6>
                            <p class="opacity-50">{{$message->created_at}}</p>
                          </div>
                        </div>
                        @if($loop->index === 0 && $conversation->product)
                            <div class="form-group">
                                <div>
                                    <div class="row gutters-10 align-items-start">
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
                                        <div class="col-4 text-left">
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

                                        @if(!empty($conversation->complain_id) || $conversation->complain_id!=null || $conversation->complain_id!=0)
                                        <div class="col-3 text-left">
                                            <span class="d-block mt-0"></span>
                                            <span class="text-muted mb-2 d-block font-weight-bold">Alasan Komplain</span>
                                            <p class="d-block">{{$conversation->complain()->first()->reason}}</p>
                                            @foreach($conversation->complain()->first()->images()->get() as $image)
                                                <img class="w-50 p-2" src="{{url('public/'.$image->detail()->file_name)}}" alt="{{$image->detail()->file_original_name}}">
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        <p>
                            {{ $message->message }}
                        </p>
                    </li>
                @endforeach
            </ul>
            <form class="pt-4" action="{{ route('messages.store') }}" method="POST">
                @csrf
                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                <div class="form-group">
                    <textarea class="form-control" rows="4" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
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
