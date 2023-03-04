@extends('frontend.layouts.user_panel')

@section('panel_content')

    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Shop Settings')}}
                <a href="{{ route('shop.visit', $shop->slug) }}" class="btn btn-link btn-sm" target="_blank">({{ translate('Visit Shop')}})<i class="la la-external-link"></i>)</a>
            </h1>
        </div>
      </div>
    </div>

    {{-- Basic Info --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Basic Info') }}</h5>
        </div>
        <div class="card-body">
            <form class="" action="{{ route('shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PATCH">
                @csrf
                <div class="row">
                    <label class="col-md-2 col-form-label">{{ translate('Shop Name') }}<span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="{{ translate('Shop Name')}}" name="name" value="{{ $shop->name }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">{{ translate('Shop Logo') }}</label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="logo" value="{{ $shop->logo }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-2 col-form-label">
                        {{ translate('Shop Phone') }} <span class="text-danger text-danger">*</span>
                    </label>
                    <div class="col-md-10 mb-3">
                        <input type="text"  class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" placeholder="{{ translate('Phone')}}" name="phone" value="{{ $shop->phone }}" required>
                        @if ($errors->has('phone'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-2 col-form-label">{{ translate('Shop Address') }} <span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="{{ translate('Address')}}" name="address" value="{{ $shop->address }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">{{ translate('Province') }}</label>
                    <div class="col-md-10">
                        <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{translate('Select your province')}}" name="province" required>
                            <option selected disabled>Select Province</option>
                            @foreach($locations['provinces'] as $key => $item)
                            <option value="{{$item->province_id}}"
                                @if($shop->province_id == $item->province_id) selected
                                @endif
                                >{{$item->province_name}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">{{ translate('City')}}</label>
                    <div class="col-md-10">
                        <select class="form-control aiz-selectpicker" data-live-search="true" name="city">
                            @foreach($locations['cities']  as $key => $item)
                                <option value="{{$item->city_id}}"
                                    @if($shop->city_id == $item->city_id) selected
                                    @endif
                                    >{{$item->city_name}}
                                </option>
                                @endforeach

                        </select>
                    </div>
                </div>


                <div class="form-group row">
                <label class="col-md-2 col-form-label">{{ translate('Sub District')}}</label>
                    <div class="col-md-10">
                        <select class="form-control aiz-selectpicker" name="sub_district">
                        @if($locations['cities']!=null)
                            @foreach($locations['sub_districts'] as $key => $item)
                                <option value="{{$item->subdistrict_id}}"
                                    @if($shop->subdistrict_id == $item->subdistrict_id) selected
                                    @endif
                                    >{{$item->subdistrict_name}}
                                </option>
                            @endforeach
                        @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">{{ translate('Postal Code')}} </label>
                    <div class="col-md-10">
                                <input type="text" value="{{$shop->postal_code}}" class="form-control" name="postal_code">
                    </div>
                </div>
                @if (get_setting('shipping_type') == 'seller_wise_shipping')
                    <div class="row">
                        <div class="col-md-2">
                            <label>{{ translate('Shipping Cost')}} <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" lang="en" min="0" class="form-control mb-3" placeholder="{{ translate('Shipping Cost')}}" name="shipping_cost" value="{{ $shop->shipping_cost }}" required>
                        </div>
                    </div>
                @endif
                @if (get_setting('pickup_point') == 1)
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">{{ translate('Pickup Points') }}</label>
                    <div class="col-md-10">
                        <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Select Pickup Point') }}" id="pick_up_point" name="pick_up_point_id[]" multiple>
                            @foreach (\App\PickupPoint::all() as $pick_up_point)
                                @if (Auth::user()->shop->pick_up_point_id != null)
                                    <option value="{{ $pick_up_point->id }}" @if (in_array($pick_up_point->id, json_decode(Auth::user()->shop->pick_up_point_id))) selected @endif>{{ $pick_up_point->getTranslation('name') }}</option>
                                @else
                                    <option value="{{ $pick_up_point->id }}">{{ $pick_up_point->getTranslation('name') }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <!-- <div class="row">
                    <label class="col-md-2 col-form-label">{{ translate('Meta Title') }}<span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="{{ translate('Meta Title')}}" name="meta_title" value="{{ $shop->meta_title }}" required>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-2 col-form-label">{{ translate('Meta Description') }}<span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <textarea name="meta_description" rows="3" class="form-control mb-3" required>{{ $shop->meta_description }}</textarea>
                    </div>
                </div> -->
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Banner Settings --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Banner Settings') }}</h5>
        </div>
        <div class="card-body">
            <form class="" action="{{ route('shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="_validate" value="0">
                @csrf

                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">{{ translate('Banners') }} (1500x450)</label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="sliders" value="{{ $shop->sliders }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                        <small class="text-muted">{{ translate('We had to limit height to maintian consistancy. In some device both side of the banner might be cropped for height limitation.') }}</small>
                    </div>
                </div>

                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>

    <form class="" id="shop"  action="{{ route('shop.verify.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0 h6">{{ translate('Shop Details')}}</h4>
            </div>
            @php
                $verification_form = get_setting('verification_form');

            @endphp
            <div class="card-body">
                @foreach (json_decode($verification_form) as $key => $element)
                @php
                    $kuncibaru = array_search($element->label, array_column($seller ?? [], 'label'));
                @endphp
                    @if ($element->type == 'text')
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ $element->label }} <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-8">
                            <input
                                value="{{isset($seller[$kuncibaru]) ? $seller[$kuncibaru]->value : null}}"
                            type="{{ $element->type }}" class="form-control mb-3" placeholder="{{ $element->label }}" name="element_{{ $key }}" value="" disabled required>
                            </div>
                        </div>
                    @elseif($element->type == 'file')
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ $element->label }} <span class="text-primary">*</span>
                                    <span class="d-block text-muted"><small>Maximum File 2MB</small></span>
                                </label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="{{ $element->type }}" name="element_{{ $key }}"
                                    id="file-{{ $key }}" class="form-control-file form-control">
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{$element->label}} {{translate('cannot be empty')}}</strong>
                                    </span>
                                    <img height="70px" src="public/{{isset($seller[$kuncibaru]) ? $seller[$kuncibaru]->value : null}}" alt="" class="mt-2" style="border: 1px solid #e2e5ec; opacity: 1; padding: 5px 5px 5px 5px;">
                                </div>
                            </div>
                        </div>
                    @elseif ($element->type == 'select' && is_array(json_decode($element->options)))
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ $element->label }}</label>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}" required>
                                        @foreach (json_decode($element->options) as $value)
                                            <option value="{{ $value }}"

                                            @if(isset($seller[$kuncibaru]) && $value == $seller[$kuncibaru]->value)
                                                    selected
                                                @endif

                                            >{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @elseif ($element->type == 'multi_select' && is_array(json_decode($element->options)))
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ $element->label }}</label>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}[]" multiple required>
                                        @foreach (json_decode($element->options) as $value)
                                            <option value="{{ $value }}"

                                                @if(isset($seller[$kuncibaru]) && in_array($value, (array)json_decode($seller[$kuncibaru]->value)))
                                                    selected
                                                @endif

                                            >{{ $value }}  </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @elseif ($element->type == 'radio')
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ $element->label }}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    @foreach (json_decode($element->options) as $value)
                                        <div class="radio radio-inline">
                                            <input type="radio" name="element_{{ $key }}" value="{{ $value }}" id="{{ $value }}" required>
                                            <label for="{{ $value }}">{{ $value }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-primary">{{ translate('Save')}}</button>
                </div>
            </div>
        </div>
    </form>

    {{-- Social Media Link --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Social Media Link') }}</h5>
        </div>
        <div class="card-body">
            <form class="" action="{{ route('shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="_validate" value="0">
                @csrf
                <div class="form-box-content p-3">
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label">{{ translate('Facebook') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="{{ translate('Facebook')}}" name="facebook" value="{{ $shop->facebook }}">
                            <small class="text-muted">{{ translate('Insert link with https ') }}</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label">{{ translate('Twitter') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="{{ translate('Twitter')}}" name="twitter" value="{{ $shop->twitter }}">
                            <small class="text-muted">{{ translate('Insert link with https ') }}</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label">{{ translate('Google') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="{{ translate('Google')}}" name="google" value="{{ $shop->google }}">
                            <small class="text-muted">{{ translate('Insert link with https ') }}</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label">{{ translate('Youtube') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="{{ translate('Youtube')}}" name="youtube" value="{{ $shop->youtube }}">
                            <small class="text-muted">{{ translate('Insert link with https ') }}</small>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Pengaturan Kurir
        </div>
        <div class="card-body">
                    <form action="{{route('seller.setting.courier.update', $shop->id)}}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
            <div class="form-box-content p-3">
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">Pilihan Kurir</label>
                        <div class="col-md-10">
                            <select class="form-control" id="selectCourier" name="couriers[]" multiple="multiple">
                                @foreach($couriers as $courier)
                                    <option value="{{$courier->code}}" @if(in_array($courier->code, (array)json_decode($shop->couriers))) selected @endif>{{$courier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
            </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Rekening Bank
        </div>
        <div class="card-body">
            <div class="mb-2 text-right">
                <a href="{{route('bank.create')}}" class="btn btn-sm btn-primary">{{translate('Create New')}}</a>
            </div>
            <table class="table">
                <tbody>
                    <tr>
                        <th>No</th>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <td></td>
                    </tr>
                    @php
                        $i = 1;
                    @endphp
                    @foreach(\App\Models\UserBankAccount::where('user_id', $shop->user->id)->get() as $account)
                        <tr>
                            <td>
                                {{$i}}
                            </td>
                            <td>
                                {{$account->bank_name}}
                            </td>
                            <td>
                                {{$account->account_number}}
                            </td>
                            <td>
                                {{$account->account_name}}
                            </td>
                            <td>
                                <a href="{{route('bank.edit', ['id' => $account->id])}}" class="btn btn-xs btn-warning">Edit</a>
                                <a href="{{route('bank.destroy', ['id' => $account->id])}}" class="btn btn-xs btn-danger">Delete</a>
                            </td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                    <!-- <tr>
                        <td>
                            {{$shop->user}}
                        </td>
                    </tr> -->
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('style')
    <script
        src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection


@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $("#shop").on("submit", function (evt) {

                let ktp = $('#file-7');
                let pemilik_ktp = $('#file-8');

                if(!!ktp.val()) {
                    ktp.removeClass('is-invalid');
                } else {
                    ktp.addClass('is-invalid');
                    AIZ.plugins.notify('danger', 'Photo KTP {{translate("cannot be Empty")}}');
                    evt.preventDefault();
                }

                if(!!pemilik_ktp.val()) {
                    pemilik_ktp.removeClass('is-invalid');
                } else {
                    pemilik_ktp.addClass('is-invalid');
                    AIZ.plugins.notify('danger', 'Photo KTP + Selfie Penanggung Jawab {{translate("cannot be Empty")}}');
                    evt.preventDefault();
                }
                $("#reg-form").submit();
            });
        });

        $(document).on('change', 'select[name=element_13]', function() {
            if(this.value.toLowerCase()=="belum pernah") {
                let _platforms = $("select[name='element_14[]']");
                _platforms.attr('disabled', true);
                _platforms.val([]);
            } else {
                $("select[name='element_14[]']").attr('disabled', false);
            }
            AIZ.plugins.bootstrapSelect('refresh');
            $("select[name='element_14[]']").selectpicker('refresh');
        });

        if($('select[name=element_13]')[0].value.toLowerCase()=="belum pernah") {
            let _platforms = $("select[name='element_14[]']");
            _platforms.attr('disabled', true);
            _platforms.val([]);
        } else {
            $("select[name='element_14[]']").attr('disabled', false);
        }

$(document).on('change', '[name=country]', function() {
        var country = $(this).val();
        getProvince(country);
    });
    $(document).on('change', 'select[name=province]', function() {
        var item = $(this).val();
        getCity(item);
    });
    $(document).on('change', 'select[name=city]', function() {
        var item = $(this).val();
        getSubDistrict(item);
    });
    $(document).on('change', 'select[name=sub_district]', function() {
        var item = $(this).val();
        getPostalCode(item);
    });
    $(document).on('change', 'select[name=postal_code]', function() {
        var item = $(this).val();
        if(item!="") {
            $('[name="addSubmit"]').attr('disabled', false);
        }
    });

    // $(document).on('change', '[name=country]', function() {
    //     var country = $(this).val();
    //     getProvince(country);
    // });

    function getProvince(country) {
        $('select[name="district"]').html($('<option/>').text('Select District'));
        $('select[name="district"]').attr('disabled', true);
        $('select[name="sub_district"]').html($('<option/>').text('Select Sub District'));
        $('select[name="sub_district"]').attr('disabled', true);
        $('select[name="postal_code"]').html($('<option/>').text('Select Postal Code'));
        $('select[name="postal_code"]').attr('disabled', true);
        $('select[name="city"]').html($('<option/>').text('Select City'));
        $('select[name="city"]').attr('disabled', true);


        if(country!='Indonesia') {
            $('select[name="province"]').html($('<option/>').text('Select Province'));
            $('select[name="province"]').attr('disabled', true);

            $('#no-overseas').addClass('d-none')
            $('#overseas').removeClass('d-none')

            $('input[name="addSubmit"]').attr('disabled', false);
            AIZ.plugins.bootstrapSelect('refresh');
            return false;
        } else {
            $('select[name="province"]').html($('<option/>').text('Select Province'));
            $('select[name="province"]').attr('disabled', false);

            $('#no-overseas').addClass('d-none')
            $('#overseas').removeClass('d-none')

            $('[name="addSubmit"]').attr('disabled', true);
            AIZ.plugins.bootstrapSelect('refresh');
        }

        console.log('halo')

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('seller.ajax.get-provinces')}}",
            type: 'GET',
            success: function(response) {
                $.each(response, function(i, data){
                    $('[name="province"]').append($('<option/>').text(data.province_name).val(data.province_id));
                });
                AIZ.plugins.bootstrapSelect('refresh');
            }
        })
    }
    function getCity(id) {
        $('select[name="city"]').html($('<option/>').text('Select City'));
        $('select[name="city"]').attr('disabled', false);

        $('select[name="district"]').html($('<option/>').text('Select District'));
        $('select[name="district"]').attr('disabled', true);
        $('select[name="sub_district"]').html($('<option/>').text('Select Sub District'));
        $('select[name="sub_district"]').attr('disabled', true);
        $('select[name="postal_code"]').html($('<option/>').text('Select Postal Code'));
        $('select[name="postal_code"]').attr('disabled', true);

        $('input[type="submit"]').attr('disabled', true);
        AIZ.plugins.bootstrapSelect('refresh');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('seller.ajax.get-cities')}}",
            data: {
                province_id: id
            },
            type: 'GET',
            success: function(response) {
                $.each(response, function(i, data){
                    $('[name="city"]').append($('<option/>').text(data.city_name).val(data.city_id));
                });
                AIZ.plugins.bootstrapSelect('refresh');
            }
        })
    }


    function getSubDistrict(id) {
        $('select[name="sub_district"]').html($('<option/>').text('Select Sub District'));
        $('select[name="sub_district"]').attr('disabled', false);
        $('select[name="postal_code"]').html($('<option/>').text('Select Postal Code'));
        $('select[name="postal_code"]').attr('disabled', true);

        $('input[type="submit"]').attr('disabled', true);
        AIZ.plugins.bootstrapSelect('refresh');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('seller.ajax.get-subdistricts')}}",
            data: {
                city_id: id
            },
            type: 'GET',
            success: function(response) {
                $.each(response, function(i, data){
                    $('[name="sub_district"]').append($('<option/>').text(data.subdistrict_name).val(data.subdistrict_id));
                });
                AIZ.plugins.bootstrapSelect('refresh');
            }
        })
    }

    function getPostalCode(id) {
        $('[name="postal_code"]').html($('<option/>').text('Select Postal Code'));
        $('[name="postal_code"]').attr('disabled', false);

        AIZ.plugins.bootstrapSelect('refresh');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{url('api/v1/location/postal-code')}}/"+id,
            type: 'GET',
            success: function(response) {
                $.each(response, function(i, data){
                    $('[name="postal_code"]').append($('<option/>').text(data.kodepos).val(data.id));
                });
                AIZ.plugins.bootstrapSelect('refresh');
            }
        })
    }

    function get_city(country) {
        $('[name="city"]').html("");
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('get-city')}}",
            type: 'POST',
            data: {
                country_name: country
            },
            success: function (response) {
                var obj = JSON.parse(response);
                console.log(obj);
                if(obj != '') {
                    $('[name="city"]').html(obj);
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            }
        });
    }

$("#selectCourier").select2({
    tags: true
});
</script>

@endsection
