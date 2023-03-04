@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">Complain Product</h5>
        </div>

        <form action="{{route('complain.store', ['product_id' => $order_detail->product->id, 'order_id' => $order_detail->order()->first()->code])}}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card-body">
            <div class="row align-items-start" >
                <div class="col-3">
                    <img
                        class="img-fluid lazyload"
                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                        data-src="{{ uploaded_asset($order_detail->product->thumbnail_img) }}"
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                        style="border: 1px solid #e2e5ec; opacity: 1; "
                    >
                </div>
                <div class="col-9 text-left">
                    <div class="col-12 mt-3}}">
                        <div class="d-flex p-2 d-block-flex bd-highlight align-items-center">
                            <div class="flex-row flex-fill">
                                <h6>{{ $order_detail->product->getTranslation('name') }}</h6>

                                <div class="mb-2" style="color:gold">
                                    {{renderStarRating(5)}}
                                </div>
                                
                                <div class="h4 fw-700 text-left text-primary">{{ single_price($order_detail->price) }}</div>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="form-group mt-2">
                            <label>{{translate('Alasan Komplain')}}</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" name="reason" rows="3"></textarea>
                        </div>
                        
                        <!-- <div class="form-group">
                            <label class="col-label">Upload Bukti</label> 
                            <input type="file" class="form-control-file" multiple="multiple" />
                        </div> -->

                        
                        <div class="input-group mb-3">
                            <div class="form-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                <label class="col-label">Upload Bukti</label> 
                                <div class="form-control-file">
                                    <button class="btn btn-outline-primary btn-xs" type="button">
                                        {{ translate('Browse') }}
                                    </button>
                                </div>
                                <input type="hidden" name="images" multiple="multiple" class="selected-files">
                            </div>
                            
                            <div class="ml-2 file-preview box sm"></div>
                        </div>
                            
                        <!-- <span class="d-block text-muted mt-1">{{translate('Order akan selesai secara otomatis pada ')}} {{'timer'}}</span> -->

                    <button type="submit" class="btn btn-primary btn-xs mb-2">Kirim</button>
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
