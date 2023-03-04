@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class=" align-items-center">
       <h1 class="h3">{{translate('Product Wish Report')}}</h1>
	</div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('wish_report.index') }}" method="GET">
                    <div class="form-group row">
                        <label class="col-md-6 col-form-label">{{ translate('Sort by Category') }}:</label>
                        <div class="col-md-5">
                            <select class="custom-select custom-select-sm filter-type" id="category_filter" name="category_filter" >
                                <option value="">Semua</option>
                                @foreach (\App\Models\Category::all() as $key => $category)
                                    <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
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

                <table class="table table-bordered table-striped mb-0 wishlist_datatable">
                    <thead>
                        <tr>
                            <th>{{ translate('Product Name') }}</th>
                            <th>{{ translate('Number of Wish') }}</th>
                            <th> {{ translate('Category') }}</th>
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
    function load_data(category_id = ''){
        var table = $('.wishlist_datatable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                    url: "{{ route('wish_report.index') }}",
                    data:{category_id: category_id}
                },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'wishlist', name: 'wishlist'},
                {data: 'category.name', name: 'category_id', orderable: false},
            ]
        });
    }

    $('#category_filter').change(function(){
        var category_id = $('#category_filter').val();
        $('#wishlist_datatable').DataTable().destroy();
        load_data(category_id);
    });
  });
</script>

@endsection