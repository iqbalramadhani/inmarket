@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class=" align-items-center">
       <h1 class="h3">Jumlah Pembeli Per Provinsi</h1>
	</div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('buyer_province.index') }}" method="GET">
                    <div class="form-group row">
                        <label class="col-md-6 col-form-label">Saring berdasarkan Provinsi:</label>
                        <div class="col-md-5">
                            <select class="custom-select custom-select-sm filter-type" id="province_filter" name="province_filter" >
                                <option value="">Semua</option>
                                @foreach(\App\Models\RajaOngkirProvince::get() as $row)
                                    <option value="{{$row->province_id}}">{{$row->province_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <a class="btn btn-success btn-sm" href="{{ route('wish_report.download') }}" target="_blank">
                                <i class="la la-download"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered table-striped mb-0 buyer_province_datatable">
                    <thead>
                        <tr>
                            <th>Provinsi</th>
                            <th>Jumlah Pembeli</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
<script type="text/javascript">
  $(document).ready(function() {
    load_data();
    function load_data(province_id = ''){
        var table = $('.buyer_province_datatable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                    url: "{{ route('buyer_province.index') }}",
                    data:{province_id: province_id}
                },
            columns: [
                {data: 'address_province', name: 'province'},
                {data: 'amount', name: 'amount'},
            ]
        });
    }

    $('#province_filter').change(function(){
        var province_id = $('#province_filter').val();
        $('#buyer_province_datatable').DataTable().destroy();
        load_data(province_id);
    });
  });
</script>

@endsection