@extends('frontend.layouts.user_panel')
@section('panel_content')
    @php
        $status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->delivery_status;
    @endphp
    <div class="card">
        <div class="card-header">
            @if($status !== 'on_delivery')
                <h1 class="h2 fs-16 mb-0">Input Nomor Resi</h1>
            @elseif($status === 'on_delivery')
                <h1 class="h2 fs-16 mb-0">Edit Nomor Resi</h1>
            @endif
        </div>

        <div class="card-body">
            <div class="row gutters-5">
                <form action="{{route('seller.order.update_delivery_status_action', encrypt($order->id))}}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="form-group">
                        <select name="resi_courier" id="select-courier" class="form-control">
                            <option value="">Pilih Kurir</option>
                            @if($shop->couriers)
                                @foreach($couriers as $courier)
                                    @if(in_array($courier->code, (array)json_decode($shop->couriers) ))
                                        <option value="{{$courier->code}}" @if($order->resi_courier===$courier->code) selected @endif >{{$courier->name}}</option>
                                    @endif
                                @endforeach
                            @else
                                @foreach($couriers as $courier)
                                    <option value="{{$courier->code}}" @if($order->resi_courier===$courier->code) selected @endif>{{$courier->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="resi" placeholder="Nomor Resi" required value="{{$order->resi_number}}">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="resi_courier_service" placeholder="Layanan" value="{{$order->resi_courier_service}}">
                    </div>
                    <a href="{{ url()->previous() }}"  class="btn btn-primary">Kembali</a>
                    @if($status !== 'on_delivery')
                        <button type="submit" class="btn btn-primary">Update</button>
                    @elseif($status === 'on_delivery')
                        <button type="submit" class="btn btn-primary">Update status menjadi pengiriman</button>
                    @endif
                </form>
            </div>
        </div>
    </div>

@endsection
