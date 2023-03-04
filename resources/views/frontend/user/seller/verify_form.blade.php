@extends('frontend.layouts.user_panel')
@section('panel_content')
    {{-- History Change --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('History') }}</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <tbody>
                    <tr >
                        <th class="text-center">No.</th>
                        <th>Detail Perubahan Data</th>
                        <th>Tanggal</th>
                    </tr>
                    @foreach($verify_history as $i => $row)
                        <tr>
                            <td width="5%" class="text-center">{{$i+1}}</td>
                            <td>
                                @foreach(json_decode($row->details) as $item)
                                        <div class="d-block mb-1">
                                            {{(isset($item->label)) ? $item->label : ''}} :
                                            <span class='d-inline-block text-secondary text-muted'><del>{{(isset($item->old_value)) ? $item->old_value : ''}} :</del></span>
                                            <i class="las la-arrow-right"></i>
                                            <span class='d-inline-block text-success'>{{(isset($item->value)) ? $item->value : ''}}</span>
                                        </div>
                                @endforeach
                            </td>
                            <td width="20%">{{$row->created_at}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{$verify_history->links()}}
        </div>
    </div>
@endsection
