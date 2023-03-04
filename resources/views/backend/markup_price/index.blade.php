@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3">{{ translate('Markup Price Setting') }}</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ translate('Markup Price Setting') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('markup_price.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">Enable Setting</label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success">
                                    <input type="checkbox" name="enable" id="checkbox_enable" @if((boolean)json_decode($setting->value)->enable) checked @endif>
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Markup Price') }}</label>
                            <div class="col-md-8 input-group">
                                <input type="text" name="markup_price" aria-label="" class="form-control" value="{{json_decode($setting->value)->value}}">
                                <div class="input-group-append" id="percent_label">
                                    <span class="input-group-text">%</span>
                                </div>
                                <select name="type" id="select_type_option">
                                    <option value="persentase" @if(json_decode($setting->value)->type === 'persentase') selected @endif >Persentase</option>
                                    <option value="value" @if(json_decode($setting->value)->type === 'value') selected @endif >Nominal</option>
                                </select>
                            </div>
                        </div>

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
