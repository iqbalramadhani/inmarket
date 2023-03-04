<form action="{{ route('commission-log.index') }}" method="GET">
    <div class="card-header row gutters-5">
        <div class="col text-center text-md-left">
            <h5 class="mb-md-0 h6">{{ translate('Commission History') }}</h5>
        </div>
        @if(Auth::user()->user_type != 'seller')
        <div class="col-md-3 ml-auto">
            <select id="user_filter" class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="user_filter">
                <option disabled selected>{{ translate('Choose Seller') }}</option>
                <option value="">Semua</option>
                @foreach (\App\Models\Seller::all() as $key => $seller)
                    @if(isset($seller->user->id))
                    <option value="{{ $seller->user->id }}" @if($seller->user->id == $seller_id) selected @endif >
                        {{ $seller->user->name }}
                    </option>
                    @endif
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-md-3">
            <div class="form-group mb-0">
                <input type="text" class="form-control form-control-sm aiz-date-range" id="date_range" name="date_range" placeholder="{{ translate('Daterange') }}">
            </div>
        </div>
        @if(Auth::user()->user_type != 'seller')
        <div class="col-md-1">
            <a class="btn btn-success btn-sm" href="{{ route('commission-log.download') }}" target="_blank">
                <i class="la la-download"></i>
            </a>
        </div>
        @endif
    </div>
</form>
<div class="card-body">
    <table class="table table-bordered table-striped mb-0 commission_history_datatable">
        <thead>
            <tr>
                <th data-breakpoints="lg">{{ translate('Order Code') }}</th>
                <th>{{ translate('Admin Commission') }}</th>
                <th>{{ translate('Seller Earning') }}</th>
                <th>{{ translate('Shipping Cost') }}</th>
                <th data-breakpoints="lg">{{ translate('Created At') }}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

@section('script')
<script type="text/javascript">
  $(document).ready(function() {
    load_data();
    function load_data(user_filter = '', min_date = '', max_date = ''){
        var table = $('.commission_history_datatable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                    url: "{{ route('commission-log.index') }}",
                    data:{user_id: user_filter, min_date: min_date, max_date : max_date}
                },
            columns: [
                {data: 'order_code', name: 'order_code'},
                {data: 'admin_commission', name: 'admin_commission', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' )},
                {data: 'seller_commission', name: 'seller_commission', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' )},
                {data: 'shipping_cost', name: 'shipping_cost', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' )},
                {data: 'date', name: 'date'}
            ]
        });
    }

    $('#user_filter').change(function(){
        var user_filter = $('#user_filter').val();
        $('#commission_history_datatable').DataTable().destroy();
        load_data(user_filter, '', '');
    });

    $('input[name="date_range"]').daterangepicker({
        opens: 'left',
        locale: { cancelLabel: 'Hapus' , applyLabel: 'Terapkan' , format: 'YYYY/MM/DD' } 
    }, function(start, end, label) {
        var min_date = start.format('YYYY-MM-DD');
        var max_date = end.format('YYYY-MM-DD');
        $('#commission_history_datatable').DataTable().destroy();
        load_data('', min_date, max_date);
    });

    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#commission_history_datatable').DataTable().destroy();
        load_data('', '', '');
    });
  });
</script>

@endsection