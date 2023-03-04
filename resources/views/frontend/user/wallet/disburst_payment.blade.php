@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="aiz-titlebar mt-0 mb-4">
    <!-- <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Waiting Payment') }}</h1>
        </div>
    </div> -->

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Waiting Payment')}}</h5>
            <!-- <h5 class="mb-0 h6">{{ translate('Please do your payment with this link')}}</h5> -->
        </div>
        <div class="card-body text-center">
            <span class="alert alert-success d-block">{{translate('Pembayaran '.$sub_type.' berhasil dibuat, silahkan dilanjutkan')}}</span>
            <a href="{{$result->url ?? route('wallet.index')}}" {{(isset($result->url)) ? "target='_blank'" : ""}} class="btn btn-sm btn-primary">{{translate('Lanjutkan')}}</a>
            <!-- <span class="alert alert-success ">{{translate('Payment has been created successfully, you will be redirected into payment page')}}</span> -->
            
            <span class="d-block mt-5">{{translate('Jika sudah melakukan pembayaran, silahkan melakukan konfirmasi pembayaran')}}</span>
            <p class="mt-2">
                <a href="{{$result->url ?? route('wallet.index')}}" {{(isset($result->url)) ? "target='_blank'" : ""}} class="btn btn-sm btn-warning">{{translate('Konfirmasi Pembayaran')}}</a>
            </p>
        </div>
    </div>
</div>
@endsection