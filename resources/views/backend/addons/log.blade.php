@extends('backend.layouts.app')

@section('content')
    <div class="">
        <div class="row ">
            <div class="col-md-6">
                <div class="nav border-bottom aiz-nav-tabs">
                    <a class="p-3 fs-16 text-reset show active" data-toggle="tab" href="#installed">{{ translate('File Changes Log') }} ({{$addon->name}})</a>
                    <!-- <a class="p-3 fs-16 text-reset" data-toggle="tab" href="#available">{{ translate('Available Addon')}}</a> -->
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="tab-content">
        <div class="tab-pane fade in active show" id="installed">
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>{{ translate('File Changes Directory')}}</th>
                                <th>{{ translate('File Before Changes')}}</th>
                                <th>{{ translate('Status')}}</th>
                                <th>{{ translate('Created At')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @forelse($addon_logs as $log)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $log->file_changes_directory }}</td>
                                    <td>{{ $log->file_before_changes }}</td>
                                    <td>{{ $log->status }}</td>
                                    <td>{{ $log->created_at }}</td>
                                </tr>
                                @php $i++; @endphp
                            @empty
                                <tr>
                                    <td colspan="4" align="center">{{ translate('No File Changes')}}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>               
            </div>
        </div>
        <div class="tab-pane fade" id="available">
            <div class="row" id="available-addons-content">

            </div>
        </div>
    </div>
@endsection