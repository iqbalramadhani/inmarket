@extends('frontend.layouts.user_panel')
@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">Lacak</h1>
        </div>

        <div class="card-body">
            <div class="row gutters-5">
                @if($track_order->valid)
                <table class="table">
                    <thead>
                    <th>Kurir</th>
                    <th>Layanan</th>
                    <th>Nomor Resi</th>
                    <th>Pengirim</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{$track_order->courier_name}}</td>
                        <td>{{$order->resi_courier_service}}</td>
                        <td>{{$order->resi_number}}</td>
                        <td>{{$order->seller->name}}</td>
                    </tr>
                    </tbody>
                </table>

                <div>
                    <div>
                        Nomor Resi : {{$track_order->resi_number}}
                    </div>
                    <div>
                        Kurir : {{$track_order->courier_name}}
                    </div>
                    <div>
                        Layanan : {{$track_order->service_code}}
                    </div>
                    <div>
                        Pengirim : {{$track_order->shipper_name}}
                    </div>
                    <div>

                        Alamat : {{$track_order->shipper_address1}}
                    </div>
                    <div>
                        Penerima : {{$track_order->receiver_name}}
                    </div>
                    <div>

                        Alamat : {{$track_order->receiver_address1}}
                    </div>
                    <div>
                        Status : {{$track_order->status}}
                    </div>
                    <table class="table table-striped table">
                        <thead>
                        <th>
                            Deskripsi
                        </th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Lokasi</th>
                        </thead>
                        <tbody>
                        @foreach($track_order->manifest as $manifest)
                            <tr>
                                <td>{{$manifest['manifest_description']}}</td>
                                <td>{{$manifest['manifest_date']}}</td>
                                <td>{{$manifest['manifest_time']}}</td>
                                <td>{{$manifest['city_name']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    Nomor resi tidak valid / belum diinput seller
                    @endif


            </div>
        </div>
    </div>
    <a href="{{ url()->previous() }}"  class="btn btn-primary">Kembali</a>

@endsection
