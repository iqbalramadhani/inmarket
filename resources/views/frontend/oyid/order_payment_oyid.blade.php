@extends('frontend.layouts.app')

@section('content')
<section class="pt-5 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-shopping-cart"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart')}}</h3>
                        </div>
                    </div>
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-map"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('2. Shipping info')}}</h3>
                        </div>
                    </div>
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-truck"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('3. Delivery info')}}</h3>
                        </div>
                    </div>
                    <div class="col active">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('4. Payment')}}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('5. Confirmation')}}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div>
    <section class="gry-bg py-4">
        <div class="container">
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="align-items-center card-header d-flex justify-content-center text-center" >
                            <h3 class="d-inline-block heading-4 mb-0 mr-3 strong-600" >{{translate('Payment Details')}}</h3>
                            <img loading="lazy"  class="img-fluid" srcna="http://i76.imgup.net/accepted_c22e0.png" height="30">
                        </div>
                        <div class="card-body text-center">
                            <div class="pg_proccess mb">


                                <span class="alert alert-success d-block">{{translate('Pembayaran '.$sub_type.' berhasil dibuat, silahkan dilanjutkan')}}</span>
                                <a id="continue_oyid_link" class="showModalPayment btn btn-xs btn-primary">{{translate('Bayar')}}</a>
                                <!-- <a id="continue_oyid_link" href="{{$result->url ?? route('wallet.index')}}" {{(isset($result->url)) ? "target='_blank'" : ""}} class="btn btn-sm btn-primary">{{translate('Lanjutkan')}}</a> -->
                                <!-- <span class="alert alert-success ">{{translate('Payment has been created successfully, you will be redirected into payment page')}}</span> -->
                            
                                <span class="d-block mt-5">{{translate('Jika sudah melakukan pembayaran, silahkan melakukan konfirmasi pembayaran')}}</span>
                                <p class="mt-2">
                                    <div class="button-confirm">
                                        <button class="btn btn-sm btn-warning" onClick="check_payment()">{{translate('Konfirmasi Pembayaran')}} </button>
                                    </div>
                                    <img width="50px" class="spinner d-none" src="/public/assets/img/spinner.svg" alt="" srcset="">
                                </p>
                            </div>

                            <div class="pg_success d-none">
                                <span class="alert alert-success d-block">{{translate('Pembayaran Telah Sukses, silahkan dilanjutkan')}}</span>
                                <a id="continue_oyid_link" href="{{route('oy.payment.success')}}" class="btn btn-sm btn-primary">{{translate('Continue')}}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded">
            <div class="card-header">
        <h3 class="fs-16 fw-600 mb-0">{{translate('Summary')}}</h3>
                <div class="text-right">
                        <span class="badge badge-inline badge-primary">
                            @php 
                                $totalItem = 0;
                                foreach($orders as $order) {
                                     $totalItem += (int)$order->orderDetails->count();
                                }
                            @endphp
                            {{ $totalItem }}
                            {{translate('Items')}}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="text-left">{{ translate('Product')}}</th>
                                <th class="text-right">{{ translate('Total')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $price = 0;
                                $tax = 0;
                                $shipping_cost = 0;
                                $coupon_discount = $orders->sum('coupon_discount');
                                $grand_total = $orders->sum('grand_total');
                            @endphp
                            
                            @foreach ($orders as $order)
                                @php
                                    $price += $order->orderDetails->sum('price');
                                    $tax += $order->orderDetails->sum('tax');
                                    $shipping_cost += $order->orderDetails->sum('shipping_cost');
                                @endphp

                                @foreach ($order->orderDetails as $key => $orderDetail)
                                    <tr class="cart_item">
                                        <td class="product-name">
                                            {{ $orderDetail->product->getTranslation('name') }}
                                            <strong class="product-quantity">
                                                × {{ $orderDetail->quantity }}
                                            </strong>
                                        </td>
                                        <td class="product-total text-right">
                                            <span class="pl-4 pr-0">{{ single_price($orderDetail['price']) }}</span>
                                        </td>
                                        
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>

                    <table class="table">
                        <tfoot>
                            <tr class="cart-subtotal">
                                <th>{{translate('Subtotal')}}</th>
                                <td class="text-right">
                                <span class="fw-600">{{ single_price($price) }}</span>
                                </td>
                            </tr>


                            <tr class="cart-shipping">
                                <th>{{translate('Total Shipping')}}</th>
                                <td class="text-right">
                                <span class="font-italic">{{ format_price($shipping_cost) }}</span>
                                </td>
                            </tr>

                            <tr class="cart-shipping">
                                <th><span class="fw-600">{{ translate('Total')}}</span></th>
                                <td class="text-right">
                                    <strong><span>{{ format_price($grand_total) }}</span></strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
            </div>
        </div>
    <section>

    <div class="modal fade" id="myModalOy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width:350px!important;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">OY! Indonesia Payment Link</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <iframe id="payment-popup"
                    height="500"
                    src="{{$result->url}}"
                    title="OY! Indonesia Payment Link">
            </iframe>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        var oyid_form =  document.getElementById('continue_oyid_link');
        setTimeout(() => {
            oyid_form.click();
        }, 3000);

        $(".showModalPayment").click(function(e) {
            e.preventDefault();
            $("#myModalOy").modal("show");
        });

        function check_payment() {
            $('.button-confirm').addClass(['d-none']);
            $('.spinner').removeClass(['d-none']);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('checkout.payment.status')}}",
                type: 'POST',
                data: {
                    partner_trx_id : '{{$partner_trx_id}}',
                },
                success: function (response) {
                    console.log(response);
                    if(response.status_code==200 && response.data.status=='complete') {
                        AIZ.plugins.notify('success', 'Payment Success, Please Continue');
                        $('.pg_proccess').addClass(['d-none']);
                        $('.pg_success').removeClass(['d-none']);
                    } else {
                        AIZ.plugins.notify('warning', 'Payment has not completed yet. Please procceed the payment');
                    }
                    
                    $('.button-confirm').removeClass(['d-none']);
                    $('.spinner').addClass(['d-none']);
                },
                error : function(error) {
                    console.log(error);
                    $('.button-confirm').removeClass(['d-none']);
                    $('.spinner').addClass(['d-none']);
                }
            });
        }
    </script>
@endsection
