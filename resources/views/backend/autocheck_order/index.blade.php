@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3">{{ translate('Pengecekan Order Otomatis') }}</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ translate('Pengecekan Order Otomatis ') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('autocheck-setting.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- <div class="form-group row">
                            <label class="col-md-3 col-from-label">Enable Setting</label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success">
                                    <input type="checkbox" name="enable" id="checkbox_enable" >
                                    <span></span>
                                </label>
                            </div>
                        </div> -->

                        @foreach($settings as $setting)
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ ($setting->type=='autoconfirm_order') ? translate('Pengecekan Otomatis Order Selesai') : (($setting->type=='autocancel_order') ? translate('Pengecekan Otomatis Order Batal') : '')}}</label>
                                <div class="col-md-8 input-group">
                                    <input type="text" name="{{$setting->type}}" aria-label="" class="form-control" value="{{$setting->value}}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Hari</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        changeLabel($("#select_type_option"))


        $("#select_type_option").on('change', function (){
            changeLabel($(this))
        })

        function changeLabel(select_target){
            if(select_target.val() === 'persentase'){
                $('#percent_label').show()
            }else{
                $('#percent_label').hide()
            }
        }

        $(document).ready(function(){
            $('input[type="checkbox"]').click(function(){
                if($(this).is(":checked")){
                    enableField()
                }
                else if($(this).is(":not(:checked)")){
                    disableField()
                }
            });
        });

        if($('input[type="checkbox"]').is(":checked")){
            enableField()
        }else{
            disableField()
        }


        function enableField(){
            $("#select_type_option").prop('disabled', false);
            $("input[name='markup_price']").prop('disabled', false);
        }

        function disableField(){
            $("#select_type_option").prop('disabled', true);
            $("input[name='markup_price']").prop('disabled', true);
        }
    </script>
@endsection
