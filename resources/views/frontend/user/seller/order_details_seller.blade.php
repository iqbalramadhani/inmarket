<div class="modal-header">
    <h5 class="modal-title strong-600 heading-5">{{ translate('Order id')}}: {{ $order->code }}</h5>
</div>

@php
    $status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->delivery_status;
    $payment_status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->payment_status;
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp

<div class="modal-body gry-bg px-3 pt-0">

    @if (get_setting('product_manage_by_admin') == 0)
        <div class="row mt-0">
            @if($order->payment_type == 'cash_on_delivery')
                <div class="offset-lg-2 col-lg-4 col-sm-6">
                    <div class="form-group">
                        <select class="form-control aiz-selectpicker form-control-sm"  data-minimum-results-for-search="Infinity" id="update_payment_status">
                            <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>{{ translate('Unpaid')}}</option>
                            <option value="paid" @if ($payment_status == 'paid') selected @endif>{{ translate('Paid')}}</option>
                        </select>
                        <label>{{ translate('Payment Status')}}</label>
                    </div>
                </div>
            @endif
            <!-- <div class="col-lg-4 col-sm-6">
                <div class="form-group">
                    <select disabled class="form-control aiz-selectpicker form-control-sm"  data-minimum-results-for-search="Infinity" id="update_delivery_status">
                        <option value="pending" @if ($status == 'pending') selected @endif>{{ translate('Pending')}}</option>
                        <option value="confirmed" @if ($status == 'confirmed') selected @endif>{{ translate('Confirmed')}}</option>
                        <option value="on_delivery" @if ($status == 'on_delivery') selected @endif>{{ translate('On delivery')}}</option>
                        <option value="delivered" @if ($status == 'delivered') selected @endif>{{ translate('Delivered')}}</option>
                    </select>
                    <label>{{ translate('Delivery Status')}}</label>
                </div>
            </div> -->
        </div>
    @endif

    <div class="card mt-2">
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
                            <td>{{ translate($status) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Total order amount')}}:</td>
                            <td>{{ single_price($order->grand_total) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Contact')}}:</td>
                            <td>{{ json_decode($order->shipping_address)->phone }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment method')}}:</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-lg-6">
                    <span class="fw-700 fs-15">{{translate('Delivery Method')}}</span>
                    <table class="table table-borderless ">
                        <tbody class="{{($order->resi_number==null) ? 'd-none' : ''}}">
                            <tr>
                                <td class="w-50 fw-600">Kurir</td>
                                <td class="text-left">
                                    <span class="strong-600">{{strtoupper($order->resi_courier)}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Nomor Resi</td>
                                <td class="text-left">
                                    <span class="strong-600">{{$order->resi_number}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Layanan Kurir</td>
                                <td class="text-left">
                                    <span class="strong-600">{{$order->resi_courier_service}}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="col-lg-6">
                        @if($status === 'confirmed')
                            <a href="{{route('seller.order.update_delivery_status', encrypt($order->id))}}"><button class="btn btn-primary btn-xs">Input Resi</button> </a>
                        @elseif($status === 'delivered')
                        @elseif($status === 'on_delivery')
                            <a href="{{route('seller.order.update_delivery_status', encrypt($order->id))}}"><button class="btn btn-primary btn-xs">Edit Resi</button> </a>
                            <a href="{{route('customers.track-order.index', ['order_code' => $order->code])}}" class="btn btn-outline-primary btn-xs ">Lacak</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-lg-12">
            <div class="card mt-2">
                <div class="card-header">
                    Pengiriman
                </div>
                <div class="card-body pb-0">
                    <table class="table table-borderless">
                        <tbody>
                        <tr>
                            <td class="w-50 fw-600">Kurir</td>
                            <td class="text-right">
                                <span class="strong-600">{{strtoupper($order->resi_courier)}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Nomor Resi</td>
                            <td class="text-right">
                                <span class="strong-600">{{$order->resi_number}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Layanan Kurir</td>
                            <td class="text-right">
                                <span class="strong-600">{{$order->resi_courier_service}}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> -->

    <div class="row">
        <div class="col-lg-9">
            <div class="col-12 card mt-4">
                <div class="card-header">
                    <b class="fs-15">{{ translate('Order Details') }}</b>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-borderless table-responsive">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="40%">{{ translate('Product')}}</th>
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
                        @foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>
                                    @if ($orderDetail->product != null)
                                        <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">{{ $orderDetail->product->getTranslation('name') }}</a>
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
                                <!-- <td>
                                    @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                        {{  translate('Home Delivery') }}
                                    @elseif ($orderDetail->shipping_type == 'pickup_point')
                                        @if ($orderDetail->pickup_point != null)
                                            {{ $orderDetail->pickup_point->getTranslation('name') }} ({{  translate('Pickip Point') }})
                                        @endif
                                    @endif
                                </td> -->
                                <td>{{ $orderDetail->price }}</td>
                                @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                    <td>
                                        @if ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 0)
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
                                        @elseif(!$orderDetail->is_confirmed)
                                            <div class="" id="button-proceed">
                                                <a href="javascript:void(0)" onclick="ajax_order_complete({{$orderDetail->order_id}})" class="badge badge-inline badge-pill badge-success ">{{translate('confirm')}}</a>
                                            </div>
                                        @endif
                                        <div class="{{($orderDetail->is_confirmed && $orderDetail->complain()->get()->isEmpty()) ? '' : 'd-none'}}" id="is_confirm" >
                                            <a class="text-capitalize badge badge-success badge-pill badge-inline badge text-white" >
                                                {{translate('confirmed')}}
                                            </a>
                                        </div>
                                        <div class="spinner d-none" id="spinner">
                                            <img width="50px" class="d-block" src="/public/assets/img/spinner.svg" alt="" srcset="">
                                        </div>
                                    @else
                                        {{'-'}}
                                    @endif
                                </td>

                                <td class="col-1">
                                    @if($orderDetail->complain()->get()->isNotEmpty())
                                        <!-- <div class="{{($orderDetail->complain()->get()->isNotEmpty()) ? '' : 'd-none'}}">
                                            <a href="{{route('complain.show', ['complain_slug' => encrypt($orderDetail->complain()->first()->id)])}}"  class="badge badge-pill badge-inline badge-danger">Lihat Komplain</a>
                                        </div> -->
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 spinner d-flex justify-content-center" >
                <div id="spinner" class="d-none">
                    <img width="50px" class="d-block" src="/public/assets/img/spinner.svg" alt="" srcset="">
                </div>
            </div>

            <div class="col-12 d-flex justify-content-center">
                @if($order->delivery_status === 'on_delivery' || $order->delivery_status === 'delivered' && !$order->is_confirmed)
                <div class="" id="button-proceed" style="margin-right: 5px;">
                    @if($order->complain()->get()->isNotEmpty())
                        <a href="{{route('complain.show', ['complain_slug' => encrypt($order->complain()->first()->id)])}}"  class="btn btn-sm btn-primary {{($orderDetail->complain()->get()->isEmpty()) ? '' : 'd-none'}}">Lihat Komplain</a>
                    @endif

                    <!-- <a href="{{url('complain/create?product_id='.$orderDetail->product_id.'&order_id='.$order->code)}}"  class="btn btn-sm btn-primary {{($orderDetail->complain()->get()->isEmpty()) ? '' : 'd-none'}}">Terima & Komplain</a> -->
                </div>
                @endif
                <div class="{{(!$orderDetail->is_confirmed) ? 'd-none' : ''}} is_confirmed"  style="margin-right: 5px;">
                    <a class="btn btn-warning btn-sm" href="{{route('post-review.create', ['order_code' => $order->code])}}">Beri Ulasan</a>
                </div>
                @if($orderDetail->delivery_status=='on_delivery' || $orderDetail->delivery_status=='delivered')
                    <!-- <span class="mx-2"></span> -->
                    <a href="{{route('customers.track-order.index', ['order_code' => $order->code])}}" class="btn btn-outline-primary btn-sm ">
                        Lacak
                    </a>
                @endif
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
                            <td class="w-50 fw-600">{{ translate('Subtotal')}}</th>
                            <td class="text-right">
                                <span class="strong-600">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('price')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Shipping')}}</th>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('shipping_cost')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Tax')}}</th>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('tax')) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Coupon')}}</th>
                            <td class="text-right">
                                <span class="text-italic">{{ single_price($order->coupon_discount) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Total')}}</th>
                            <td class="text-right">
                                <strong>
                                        <span>{{ single_price($order->grand_total) }}
                                        </span>
                                </strong>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $('#update_delivery_status').on('change', function(){
        var order_id = {{ $order->id }};
        var status = $('#update_delivery_status').val();
        $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
            $('#order_details').modal('hide');
            AIZ.plugins.notify('success', '{{ translate('Order status has been updated') }}');
            location.reload().setTimeOut(500);
        });
    });

    $('#update_payment_status').on('change', function(){
        var order_id = {{ $order->id }};
        var status = $('#update_payment_status').val();
        $.post('{{ route('orders.update_payment_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
            $('#order_details').modal('hide');
            //console.log(data);
            AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
            location.reload().setTimeOut(500);
        });
    });
</script>
