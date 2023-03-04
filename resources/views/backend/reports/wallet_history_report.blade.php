@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Wallet Transaction Report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <form action="{{ route('wallet-history.index') }}" method="GET">
                <div class="card-header row gutters-5">
                    <div class="col text-center text-md-left">
                        <h5 class="mb-md-0 h6">{{ translate('Wallet Transaction') }}</h5>
                    </div>
                    @if(Auth::user()->user_type != 'seller')
                    <div class="col-md-4 ml-auto">
                        <select class="form-control form-control-sm filter-type" id="user_filter" name="user_filter">
                            <option disabled selected>{{ translate('Choose User') }}</option>
                            <option value="">Semua</option>
                            @foreach ($users_with_wallet as $key => $user)
                                <option value="{{ $user->id }}"> {{ $user->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm" id="date_range" name="date_range" placeholder="{{ translate('Daterange') }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <a class="btn btn-success btn-sm" href="{{ route('wallet-history.download') }}" target="_blank">
                            <i class="la la-download"></i>
                        </a>
                    </div>
                </div>
            </form>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0 withdrawal_datatable">
                    <thead>
                        <tr>
                            <th>{{ translate('Customer')}}</th>
                            <th data-breakpoints="lg">{{  translate('Date') }}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th data-breakpoints="lg">{{ translate('Payment Method')}}</th>
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
    function load_data(user_filter = '', min_date = '', max_date = ''){
        var table = $('.withdrawal_datatable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                    url: "{{ route('wallet-history.index') }}",
                    data:{user_id: user_filter, min_date: min_date, max_date : max_date}
                },
            columns: [
                {data: 'customer', name: 'customer'},
                {data: 'date', name: 'date'},
                {data: 'amount', name: 'amount', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' )},
                {data: 'payment_method', name: 'payment_method'}
            ]
        });
    }

    $('#user_filter').change(function(){
        var user_filter = $('#user_filter').val();
        $('#withdrawal_datatable').DataTable().destroy();
        load_data(user_filter, '', '');
    });

    $('input[name="date_range"]').daterangepicker({
        opens: 'left',
        locale: { cancelLabel: 'Hapus' , applyLabel: 'Terapkan' , format: 'YYYY/MM/DD' } 
    }, function(start, end, label) {
        var min_date = start.format('YYYY-MM-DD');
        var max_date = end.format('YYYY-MM-DD');
        $('#withdrawal_datatable').DataTable().destroy();
        load_data('', min_date, max_date);
    });

    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#withdrawal_datatable').DataTable().destroy();
        load_data('', '', '');
    });
  });
</script>

@endsection