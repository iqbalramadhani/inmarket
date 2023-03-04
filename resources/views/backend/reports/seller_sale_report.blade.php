@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class=" align-items-center">
       <h1 class="h3">{{translate('Seller Based Selling Report')}}</h1>
	</div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('seller_sale_report.index') }}" method="GET">
                    <div class="form-group row">
                        <label class="col-md-6 col-form-label">{{translate('Sort by verificarion status')}} :</label>
                        <div class="col-md-5">
                            <select class="custom-select custom-select-sm filter-type" id="status_filter" name="status_filter" >
                                <option value="">Semua</option>
                                <option value="1">{{ translate('Approved') }}</option>
                                <option value="0">{{ translate('Non Approved') }}</option>
                            </select>
                        </div>
                        <div class="col-md-1" style="align: right">
                            <a class="btn btn-success btn-sm" href="{{ route('seller_sale_report.download') }}" target="_blank">
                                <i class="la la-download"></i>
                            </a>
                        </div>
                        
                    </div>
                </form>

                <table class="table table-bordered table-striped mb-0 seller_sale_datatable">
                    <thead>
                        <tr>
                            <th>{{ translate('Seller Name') }}</th>
                            <th data-breakpoints="lg">{{ translate('Shop Name') }}</th>
                            <th data-breakpoints="lg">{{ translate('Number of Product Sale') }}</th>
                            <th>{{ translate('Order Amount') }}</th>
                            <th>{{ translate('Verification Status') }}</th>
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
    function load_data(verification_status = ''){
        var table = $('.seller_sale_datatable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                    url: "{{ route('seller_sale_report.index') }}",
                    data:{verification_status: verification_status}
                },
            columns: [
                {data: 'seller_name', name: 'seller_name'},
                {data: 'shop_name', name: 'shop_name'},
                {data: 'num_of_sale', name: 'num_of_sale'},
                {data: 'order_amount', name: 'order_amount', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' )},
                {data: 'verification_status', name: 'verification_status', orderable: false},
            ],
        });
    }

    $('#status_filter').change(function(){
        var verification_status = $('#status_filter').val();
        $('#seller_sale_datatable').DataTable().destroy();
        load_data(verification_status);
    });
  });
</script>

@endsection
