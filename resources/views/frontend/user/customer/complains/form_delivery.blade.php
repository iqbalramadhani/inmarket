@extends('frontend.layouts.user_panel')
@section('panel_content')

    <div class="card">
        <div class="card-header">
            @if($complain->status !== 'requested')
                <h1 class="h2 fs-16 mb-0">Input Nomor Resi</h1>
            @elseif($complain->status === 'requested')
                <h1 class="h2 fs-16 mb-0">Edit Nomor Resi</h1>
            @endif
        </div>

        <div class="card-body">
            <div class="row gutters-5">
                <form action="{{route('complain.store_delivery', encrypt($complain->id))}}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="form-group">
                        <select name="resi_courier" id="select-courier" class="form-control">
                            <option value="">Pilih Kurir</option>
                            @foreach($couriers as $courier)
                                <option value="{{$courier->code}}" @if($complain->resi_courier===$courier->code) selected @endif>{{$courier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="resi" placeholder="Nomor Resi" required value="{{$complain->resi_number}}">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="resi_courier_service" placeholder="Layanan" value="{{$complain->resi_courier_service}}">
                    </div>
                    <a href="{{ url()->previous() }}"  class="btn btn-primary">Kembali</a>
                    @if($complain->status !== 'requested')
                        <button type="submit" class="btn btn-primary">Update</button>
                    @elseif($complain->complain === 'requested')
                        <button type="submit" class="btn btn-primary">Update status menjadi pengiriman</button>
                    @endif
                </form>
            </div>
        </div>
    </div>

@endsection
