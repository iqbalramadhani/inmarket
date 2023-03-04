@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="form-group row">
                        <div class="col-md-11">
                            <h1 class="h6">{{translate('User Search Report')}}</h1>
                        </div>

                        <div class="col-md-1">
                            <a class="btn btn-success btn-sm" href="{{ route('user_search_report.download') }}" target="_blank">
                                <i class="la la-download"></i>
                            </a>
                        </div>
                    </div>
                <table class="table table-bordered table-striped mb-0 user_search_datatable">
                    <thead>
                        <tr>
                            <th>{{ translate('Search By') }}</th>
                            <th>{{ translate('Number searches') }}</th>
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
        var table = $('.user_search_datatable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                    url: "{{ route('user_search_report.index') }}",
                },
            columns: [
                {data: 'query', name: 'query'},
                {data: 'count', name: 'count'}
            ]
        });
    }
  });
</script>

@endsection
