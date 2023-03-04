<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Order id')}}: {{ $order->code }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>

@php
    $status = $order->orderDetails->first()->delivery_status;
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp

<div class="modal-body gry-bg px-3 pt-3">
    <div class="card mt-1">
        <div class="card-header">
          <b class="fs-15">{{ translate('Order Summary') }}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order Code')}}:</td>
                            <td>{{ $order->code }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Customer')}}:</td>
                            <td>{{ json_decode($order->shipping_address)->name }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Email')}}:</td>
                            @if ($order->user_id != null)
                                <td>{{ $order->user->email }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Shipping address')}}:</td>
                            <td>{{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->city }}, {{ json_decode($order->shipping_address)->postal_code }}, {{ json_decode($order->shipping_address)->country }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order date')}}:</td>
                            <td>{{ date('d-m-Y H:i A', $order->date) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order status')}}:</td>
                            <td>{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Total order amount')}}:</td>
                            <td>{{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax')) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment method')}}:</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment status')}}:</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', translate($order->payment_status))) }}
                            </td>
                        </tr>
                        @if($order->payment_status=='unpaid')
                            <tr>
                                <td class="w-50 fw-600"></td>
                                <td>
                                <div class="">
                                    <a href="{{$order->oy_payment()->first()->url}}" target="_blank" class="btn btn-xs btn-primary">{{translate('Bayar')}}</a>
                                    <button class="btn btn-xs btn-warning" onClick="check_payment()">{{translate('Konfirmasi Pembayaran')}} </button>
                                    <img width="50px" class="spinner_payment d-none" src="/public/assets/img/spinner.svg" alt="" srcset="">
                                    <!-- <span class="alert alert-success ">{{translate('Payment has been created successfully, you will be redirected into payment page')}}</span> -->
                                </div>
                                </td>
                            </tr>
                        @endif

                        @if(get_setting('proxypay') == 1 && !$order->proxy_cart_reference_id->isEmpty())
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Proxypay Reference')}}:</td>
                                <td>{{ $order->proxy_cart_reference_id->first()->reference_id }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-9">
            <div class="">
                <div class="col-12 card mt-4">
                    <div class="card-header">
                    <b class="fs-15">{{ translate('Order Details') }}</b>
                    </div>
                    <div class="card-body pb-0">
                        <table class="table table-borderless table-responsive">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th width="30%">{{ translate('Product')}}</th>
                                    <th>{{ translate('Variation')}}</th>
                                    <th>{{ translate('Quantity')}}</th>
                                    <!-- <th>{{ translate('Delivery Type')}}</th> -->
                                    <th>{{ translate('Price')}}</th>
                                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                        <th>{{ translate('Refund')}}</th>
                                    @endif
                                    <th>{{translate('Status')}}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $key => $orderDetail)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">{{ $orderDetail->product->getTranslation('name') }}</a>
                                            @elseif($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                                <a href="{{ route('auction-product', $orderDetail->product->slug) }}" target="_blank">{{ $orderDetail->product->getTranslation('name') }}</a>
                                            @else
                                                <strong>{{  translate('Product Unavailable') }}</strong>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $orderDetail->variation }}
                                        </td>
                                        <td>
                                            {{ $orderDetail->quantity }}
                                        </td>
                                        <td class="d-none">
                                            @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                                {{  translate('Home Delivery') }}
                                            @elseif ($orderDetail->shipping_type == 'pickup_point')
                                                @if ($orderDetail->pickup_point != null)
                                                    {{ $orderDetail->pickup_point->name }} ({{  translate('Pickip Point') }})
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ single_price($orderDetail->price) }}</td>
                                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                            @php
                                                $no_of_max_day = get_setting('refund_request_time');
                                                $last_refund_date = $orderDetail->created_at->addDays($no_of_max_day);
                                                $today_date = Carbon\Carbon::now();
                                            @endphp
                                            <td>
                                                @if ($orderDetail->product != null &&
                                                $orderDetail->product->refundable != 0 &&
                                                $orderDetail->refund_request == null &&
                                                $today_date <= $last_refund_date &&
                                                $orderDetail->payment_status == 'paid' &&
                                                $orderDetail->delivery_status == 'delivered')
                                                    <a href="{{route('refund_request_send_page', $orderDetail->id)}}" class="btn btn-primary btn-sm">{{  translate('Send') }}</a>
                                                @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 0)
                                                    <b class="text-info">{{  translate('Pending') }}</b>
                                                @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 2)
                                                    <b class="text-success">{{  translate('Rejected') }}</b>
                                                @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 1)
                                                    <b class="text-success">{{  translate('Approved') }}</b>
                                                @elseif ($orderDetail->product->refundable != 0)
                                                    <b>{{  translate('N/A') }}</b>
                                                @else
                                                    <b>{{  translate('Non-refundable') }}</b>
                                                @endif
                                            </td>
                                        @endif

                                        <td class="col-1">
                                            @if($orderDetail->delivery_status=='delivered')
                                                @if($orderDetail->complain()->get()->isNotEmpty())
                                                <div class="" id="button-proceed">
                                                    <a class="text-capitalize badge badge-danger badge-pill badge-inline badge text-white" >
                                                        {{translate('complained')}}
                                                    </a>
                                                </div>
                                                @endif

                                                <div class="{{($orderDetail->is_confirmed && $orderDetail->complain()->get()->isEmpty()) ? '' : 'd-none'}} is_confirm"  >
                                                    <a class="text-capitalize badge badge-success badge-pill badge-inline badge text-white" >
                                                        {{translate('confirmed')}}
                                                    </a>
                                                </div>
                                            @else
                                                {{'-'}}
                                            @endif
                                        </td>

                                        <td class="col-1">
                                            @if($orderDetail->delivery_status=='confirmed')
                                                @if (get_setting('conversation_system') == 1)
                                                    <!-- <a href="{{route('conversations.create', ['seller_id' => $orderDetail->seller_id, 'product_id' => $orderDetail->product_id])}}" class="badge badge-pill badge-secondary badge-inline " onclick="">{{translate('Message Seller')}}</a> -->
                                                @endif
                                            @endif
                                            @if($orderDetail->delivery_status=='on_delivery')
                                                <!-- <div class="" id="button-delivered">
                                                    <a href="javascript:void(0)" onclick="ajax_order_delivered({{$orderDetail->order_id}}, {{$orderDetail->product_id}})" class="badge badge-inline badge-pill badge-success ">{{translate('Terima Barang')}}</a>
                                                </div> -->
                                                <!-- <a href="{{route('conversations.create', ['seller_id' => $orderDetail->seller_id, 'product_id' => $orderDetail->product_id])}}" class="badge  badge-pill badge-secondary badge-inline" onclick="">{{translate('Message Seller')}}</a> -->
                                                <!-- <a href="{{route('customers.track-order.index', ['order_code' => $order->code])}}" class="badge badge-warning badge-pill badge-inline">{{translate('Trace')}}</a> -->
                                            @endif
                                            @if($orderDetail->complain()->get()->isEmpty())
                                                @if($orderDetail->delivery_status=='delivered')
                                                    <a href="{{route('conversations.create', ['seller_id' => $orderDetail->seller_id, 'customer_id' => Auth()->user()->id, 'product_id' => $orderDetail->product_id])}}" class="badge  badge-pill badge-secondary badge-inline" onclick="">{{translate('Message Seller')}}</a>
                                                    <!-- <a href="{{route('customers.track-order.index', ['order_code' => $order->code])}}" class="badge badge-warning badge-pill badge-inline">{{translate('Trace')}}</a> -->
                                                    <!-- <a class="badge badge-warning badge-pill badge-inline" href="{{route('post-review.create', ['order_code' => $order->code])}}">Beri Ulasan</a> -->
                                                @endif
                                                @if($orderDetail->delivery_status =='confirmed' || $orderDetail->delivery_status=='on_delivery')
                                                    @if (get_setting('conversation_system') == 1)
                                                        <a href="{{route('conversations.create', ['seller_id' => $orderDetail->seller_id, 'customer_id' => Auth()->user()->id, 'product_id' => $orderDetail->product_id])}}" class="badge badge-pill badge-secondary badge-inline " onclick="">{{translate('Message Seller')}}</a>
                                                    @endif
                                                @endif
                                            @endif



                                            <!-- <div class="{{(!$orderDetail->is_confirmed) ? 'd-none' : ''}} is_confirmed"> -->
                                                <!-- <a class="badge badge-warning badge-pill badge-inline" href="{{route('post-review.create', ['order_code' => $order->code])}}">Beri Ulasan</a> -->
                                                <!-- <a href="{{url('complain/create?product_id='.$orderDetail->product_id.'&order_id='.$order->code)}}"  class="badge badge-pill badge-inline badge-danger {{($orderDetail->complain()->get()->isEmpty()) ? '' : 'd-none'}}">Terima & Komplain</a> -->
                                            <!-- </div> -->
                                            @if($orderDetail->complain()->get()->isNotEmpty())
                                            <div class="{{($orderDetail->complain()->get()->isNotEmpty()) ? '' : 'd-none'}}">
                                                <a href="{{route('complain.show', ['complain_slug' => encrypt($orderDetail->complain()->first()->id)])}}"  class="badge badge-pill badge-inline badge-danger">Lihat Komplain</a>
                                            </div>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @foreach($order->orderDetails as $orderDetail)
                    @if($orderDetail->delivery_status=='delivered')
                        <!-- <div  class="col-12 border rounded">
                            <div class="row my-2">
                                <strong class="col-12">Konfirmasi Barang Jika Sudah Selesai</strong>
                                <div class="col-12">
                                    <div class="d-flex bd-highlight align-items-center">
                                        <div class="flex-left flex-fill">
                                            <span class="d-block text-muted">{{$orderDetail->product->name}}</span>
                                        </div>
                                        @if(!$orderDetail->is_confirmed)
                                        <div class="flex-right" id="button-proceed">
                                            <button onclick="ajax_order_complete({{$orderDetail->order_id}})" class="btn btn-success btn-xs">Selesai</button>
                                            <button onclick="ajax_order_refund({{$orderDetail->order_id}})" class="btn btn-outline-danger btn-xs">Komplain</button>
                                        </div>
                                        @endif
                                        <div class="flex-right {{($orderDetail->is_confirmed) ? '' : 'd-none'}}" id="is_confirmed">
                                            <button class="text-uppercase btn btn-success btn-xs disabled" >
                                                {{translate('done')}} <i class="las la-check-circle"></i>
                                            </button>
                                        </div>
                                        <div class="flex-right spinner d-none" id="spinner">
                                            <img width="50px" class="d-block" src="/public/assets/img/spinner.svg" alt="" srcset="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    @endif
                @endforeach

                <div class="col-12 spinner d-flex justify-content-center" >
                    <div id="spinner" class="d-none">
                        <img width="50px" class="d-block" src="/public/assets/img/spinner.svg" alt="" srcset="">
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-center">
                    <!-- @if($order->is_done)
                        <a class="btn btn-primary btn-sm " href="{{route('post-review.create', ['order_code' => $order->code])}}">
                            {{translate('Done')}}
                        </a>
                    @endif -->
                    @if($order->delivery_status === 'on_delivery' || $order->delivery_status === 'delivered' && !$order->is_confirmed)
                    <div class="" id="button-proceed" style="margin-right: 5px;">
                        @if($order->complain()->get()->isEmpty())
                            @if(!$order->is_confirmed)
                                <a href="javascript:void(0)" onclick="ajax_order_complete({{$orderDetail->order_id}})" class="btn btn-sm btn-success ">{{translate('Terima & Selesai')}}</a>

                                <a href="{{url('complain/create?order_id='.$order->code)}}"  class="btn btn-sm btn-primary {{($orderDetail->complain()->get()->isEmpty()) ? '' : 'd-none'}}">Terima & Komplain</a>
                            @endif
                        @else
                                <a href="{{route('complain.show', ['complain_slug' => encrypt($order->complain()->first()->id)])}}"  class="btn btn-sm btn-primary {{($orderDetail->complain()->get()->isEmpty()) ? '' : 'd-none'}}">Lihat Komplain</a>
                        @endif

                        <!-- <a href="{{url('complain/create?product_id='.$orderDetail->product_id.'&order_id='.$order->code)}}"  class="btn btn-sm btn-primary {{($orderDetail->complain()->get()->isEmpty()) ? '' : 'd-none'}}">Terima & Komplain</a> -->
                    </div>
                    @endif
                    <div class="{{(!$orderDetail->is_confirmed) ? 'd-none' : ''}} is_confirmed"  style="margin-right: 5px;">
                        <a class="btn btn-warning btn-sm" href="{{route('post-review.create', ['order_code' => $order->code])}}">Beri Ulasan</a>
                    </div>
                    @if($order->delivery_status=='on_delivery' || $order->delivery_status=='delivered')
                        <!-- <span class="mx-2"></span> -->
                        <a href="{{route('customers.track-order.index', ['order_code' => $order->code])}}" class="btn btn-outline-primary btn-sm ">
                            Lacak
                        </a>
                    @endif
                    @if($status === 'confirmed')
                        {{-- <button type="button" id="buttonCancelOrder" class="btn btn-primary">Batalkan Pesanan</button> --}}
                    @endif
                </div>

            </div>
        </div>
        <div class="col-lg-3">
            <div class="card mt-4">
                <div class="card-header">
                    <b class="fs-15">{{ translate('Order Ammount') }}</b>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Subtotal')}}</td>
                                <td class="text-right">
                                    <span class="strong-600">{{ single_price($order->orderDetails->sum('price')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Shipping')}}</td>
                                <td class="text-right">
                                    <span class="text-italic">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Tax')}}</td>
                                <td class="text-right">
                                    <span class="text-italic">{{ single_price($order->orderDetails->sum('tax')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Coupon')}}</td>
                                <td class="text-right">
                                    <span class="text-italic">{{ single_price($order->coupon_discount) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Total')}}</td>
                                <td class="text-right">
                                    <strong><span>{{ single_price($order->grand_total) }}</span></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($order->manual_payment && $order->manual_payment_data == null)
                <button onclick="show_make_payment_modal({{ $order->id }})" class="btn btn-block btn-primary">{{ translate('Make Payment')}}</button>
            @endif
        </div>
    </div>
        <!-- <div class="row">
            <div class="col-lg-8">
                <div class="float-right">
                    <a href="{{route('post-review.create', ['order_code' => $order->code])}}" class="btn btn-primary">
                        Beri ulasan
                    </a>
                    <a href="{{route('customers.track-order.index', ['order_code' => $order->code])}}" class="btn btn-primary">
                        Lacak
                    </a>
                </div>
            </div>
        </div> -->
</div>


<script type="text/javascript">

    $('#buttonCancelOrder').on('click', function(){
        $('#order_details').modal('hide');
        $.ajax({
            method: 'GET',
            url: "{{route('customer.cancel-order-modal', ['order_code' => $order->code])}}",
            success: function(data){
                $('#cancelOrderModal').html(data)
                $('#cancelOrderModal').modal('show');
            }
        })
    })

    function show_make_payment_modal(order_id){
        $.post('{{ route('checkout.make_payment') }}', {_token:'{{ csrf_token() }}', order_id : order_id}, function(data){
            $('#payment_modal_body').html(data);
            $('#payment_modal').modal('show');
            $('input[name=order_id]').val(order_id);
        });
    }

    function ajax_order_complete(order_id) {
        $("#button-proceed").addClass(['d-none'])
        $("#spinner").removeClass(['d-none'])
        $.post('{{ route('ajax.orders.complete') }}',
            {
                _token:'{{ csrf_token() }}',
                order_id : order_id
            },
            function(response) {
                setTimeout(() => {
                    AIZ.plugins.notify('success', response.description);

                    $("#is_reviewed").removeClass(['d-none']);
                    $(".is_confirm").removeClass(['d-none']);
                    $(".is_confirmed").removeClass(['d-none']);
                    $("#button-proceed").addClass(['d-none']);
                }, 3000);
            }
        ).done(function(response) {
        }).fail(function(e) {
            AIZ.plugins.notify('danger', "Error");
        }).always(function() {
            setTimeout(() => {
                $("#spinner").addClass(['d-none'])
            }, 3000);
        });
    }

    function check_payment() {
            $('.spinner_payment').removeClass(['d-none']);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('checkout.payment.status')}}",
                type: 'POST',
                data: {
                    partner_trx_id : '{{$order->oy_trx_id}}',
                },
                success: function (response) {
                    if(response.status_code==200 && response.data.status=='complete') {
                        AIZ.plugins.notify('success', 'Payment Success');
                    } else {
                        AIZ.plugins.notify('warning', 'Payment has not completed yet. Please procceed the payment');
                    }

                    $('.spinner_payment').addClass(['d-none']);
                },
                error : function(error) {
                    console.log(error);
                    $('.spinner_payment').addClass(['d-none']);
                }
            });
        }

    function ajax_order_delivered(order_id, product_id) {
        $("#button-delivered").addClass(['d-none'])
        $("#spinner").removeClass(['d-none'])
        $.post('{{ route('ajax.orders.delivered') }}',
            {
                _token:'{{ csrf_token() }}',
                order_id : order_id,
                product_id : product_id
            },
            function(response) {
                setTimeout(() => {
                    AIZ.plugins.notify('success', response.description);
                    $("#button-delivered").addClass(['d-none']);

                }, 3000);
                location.reload();
            }
        ).done(function(response) {
        }).fail(function(e) {
            AIZ.plugins.notify('danger', "Error");
        }).always(function() {
            setTimeout(() => {
                $("#spinner").addClass(['d-none'])
            }, 3000);
        });
    }

    function ajax_order_refund(order_id) {
        $("#button-proceed").addClass(['d-none'])
        $("#spinner").removeClass(['d-none'])
        $.post('{{ route('ajax.orders.refund') }}',
            {
                _token:'{{ csrf_token() }}',
                order_id : order_id
            },
            function(response) {
                // setTimeout(() => {
                //     AIZ.plugins.notify('success', response.description);
                // }, 3000);
            }
        ).done(function(response) {
        }).fail(function(e) {
            AIZ.plugins.notify('danger', "Error");
        }).always(function() {
            setTimeout(() => {
                $("#button-proceed").removeClass(['d-none'])
                $("#spinner").addClass(['d-none'])
            }, 3000);
        });
    }
</script>
