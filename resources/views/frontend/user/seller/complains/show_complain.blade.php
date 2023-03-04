@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">Complain Order - #{{$complain->order()->first()->code}}</h5>
        </div>

        <form action="{{-- route('complain.store', ['product_id' => $order_detail->product->id, 'order_id' => $order_detail->order()->first()->code])--}}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card-body">
            <!-- <div class="row align-items-start" >
                <div class="col-3">
                    <img
                        class="img-fluid lazyload"
                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                        data-src="{{-- uploaded_asset($order_detail->product->thumbnail_img) --}}"
                        onerror="this.onerror=null;this.src='{{-- static_asset('assets/img/placeholder.jpg') --}}';"
                        style="border: 1px solid #e2e5ec; opacity: 1; "
                    >
                </div>
                <div class="col-9 text-left">
                    <div class="col-12 mt-3}}">
                        <div class="d-flex p-2 d-block-flex bd-highlight align-items-center">
                            <div class="flex-row flex-fill">
                                <h6>{{-- $order_detail->product->getTranslation('name') --}}</h6>

                                <div class="mb-2" style="color:gold">
                                    {{renderStarRating(5)}}
                                </div>
                                
                                <div class="h4 fw-700 text-left text-primary">{{-- single_price($order_detail->price) --}}</div>
                                
                                <div class="h6 mt-5 d-block text-muted">Status : <span class="fw-700 text-uppercase text-danger">{{$complain->status}}</span></div>
                            </div>
                        </div>      
                    </div>
                </div>
            </div> -->
            <div class="row mt-1">
                <div class="col-12">
                <span class="d-block mt-0"></span>
                    <span class="text-muted mb-2 d-block">Alasan Komplain</span>
                    <p class="d-block">{{$complain->reason}}</p>
                    <div class="h6 mt-5 d-block text-muted">Status : <span class="fw-700 text-uppercase text-danger">{{$complain->status}}</span></div>
                    
                </div>
                <div class="col-12">
                    <!-- {{$complain->images()->first()}} -->
                    @foreach($complain->images()->get() as $image)
                        @if($image->detail())
                            <img class="col-2 p-2" src="{{url('public/'.$image->detail()->file_name)}}" alt="{{$image->detail()->file_original_name}}">
                        @endif
                    @endforeach
                </div>
                <div class="col-12 mt-3 text-center">
                    <a href="{{route('complain.update.approve', ['slug' => encrypt($complain->id) ])}}" class="btn btn-xs btn-primary mb-2 {{($complain->status=='requested') ? '' : 'd-none'}}" >Setujui</a>                    
                    <a href="{{route('complain.complete', ['slug' => encrypt($complain->id) ])}}" class="btn btn-xs btn-primary mb-2 {{($complain->status=='procceeded') ? '' : 'd-none'}}" >Selesai</a>                    
                    <a href="{{route('customers.track-order.complain', ['complain_slug' => encrypt($complain->id) ])}}" class="btn btn-xs btn-outline-primary mb-2 {{($complain->resi_courier!=null) ? '' : 'd-none'}}" >Lacak</a>                    
                    <a href="{{ route('conversations.create', ['seller_id' => $complain->seller_id, 'customer_id' => $complain->user_id, 'complain_id' => $complain->id])}}" class="btn btn-xs btn-warning mb-2" >{{translate('Message Customer')}}</a>                    
                    <!-- <a href="#" class="btn btn-xs btn-secondary mb-2" >Chat Seller</a>                     -->
                </div>
            </div>
            
            <!-- <button type="submit" class="btn btn-primary">Kirim</button> -->
        </form>

    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">

    <style>

        .rating {
            position: relative;
            width: 180px;
            background: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: .3em;
            padding: 5px;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 0 2px #b3acac;
        }


        .rating__star {
            font-size: 1.3em;
            cursor: pointer;
            color: #dabd18b2;
            transition: filter linear .3s;
        }

        .rating__star:hover {
            filter: drop-shadow(1px 1px 4px gold);
        }

    </style>
    @endsection

@section('script')
    <script>
        
    </script>
@endsection
