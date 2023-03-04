@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class=" align-items-center">
       <h1 class="h3">{{translate('Inhouse Product sale report')}}</h1>
	</div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('in_house_sale_report.index') }}" method="GET">
                    <div class="form-group row">  
                        <div class="col-md-11"><h1 class="h6">Produk Internal</h1></div>                      
                        <div class="col-md-1">
                            <a class="btn btn-success btn-sm" href="{{ route('in_house_sale_report.download') }}" target="_blank">
                                <i class="la la-download"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered table-striped mb-0 in_house_sale_datatable">
                    <thead>
                        <tr>
                            <th>{{ translate('Product Name') }}</th>
                            <th>{{ translate('Num of Sale') }}</th>
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
    function load_data(){
        var table = $('.in_house_sale_datatable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                    url: "{{ route('in_house_sale_report.index') }}",
                },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'num_of_sale', name: 'num_of_sale'}
            ]
        });
    }
  });
</script>
@endsection