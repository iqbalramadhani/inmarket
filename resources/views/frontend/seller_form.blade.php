@extends('frontend.layouts.app')

@section('style')
    <style>
        .error{
            color:red;
        }
    </style>
@endsection

@section('content')
<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">{{ translate('Register your shop')}}</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="{{ route('shops.create') }}">"{{ translate('Register your shop')}}"</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="pt-4 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                @if ($errors->has('is_agree_tos_seller'))
                    <div class="alert alert-danger">
                                            <span  role="alert">
                                                    <strong>Pengguna harus menyetujui syarat dan ketentuan kami </strong>
                                            </span>
                    </div>
                @endif
                <form id="shop" class="" action="{{ route('shops.store') }}" method="POST" enctype="multipart/form-data">
                    <div class="d-none" id="tos-place"></div>
                    @csrf
                    @if (!Auth::check())
                        <div class="bg-white rounded shadow-sm mb-3">
                            <div class="fs-15 fw-600 p-3 border-bottom">
                                {{ translate('Personal Info')}}
                            </div>
                            <div class="p-3">
                                @if ($errors->count() > 0)
                                    <div id="ERROR_COPY" style="display: none;" class="alert alert-danger">
                                            @foreach ($errors->all() as $error)
                                               {{ $error }}<br/>
                                            @endforeach
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>{{ translate('Your Name')}} <span class="text-primary">*</span></label>
                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" placeholder="{{  translate('Name') }}" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Your Email')}} <span class="text-primary">*</span></label>
                                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Your Password')}} <span class="text-primary">*</span></label>
                                    <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{  translate('Password') }}" name="password" id="password" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Repeat Password')}} <span class="text-primary">*</span></label>
                                    <input type="password" class="form-control" placeholder="{{  translate('Confirm Password') }}" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="bg-white rounded shadow-sm mb-4">
                        <div class="fs-15 fw-600 p-3 border-bottom">
                            {{ translate('Basic Info')}}
                        </div>
                        <div class="p-3">
                            <div class="form-group">
                                <label>{{ translate('Shop Name')}} <span class="text-primary">*</span></label>
                                <input type="text" class="form-control" placeholder="{{ translate('Shop Name')}}" name="shop_name" required>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('Shop Logo') }} <span class="text-primary">*</span><span class="d-block text-muted"><small>Maximum File 1MB</small></span></label>
                                <input type="file" class="form-control" name="input-logo" id="inputGroupFile01" required>
                                @error('input-logo')
                                <div class="text-danger">{{ $errors->first('input-logo') }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>{{ translate('Address')}} <span class="text-primary">*</span></label>
                                <input type="text" class="form-control mb-3" placeholder="{{ translate('Address')}}" name="address" required>
                            </div>
                            @php
                                $verification_form_1 = get_setting('verification_form');
                            @endphp
                            @foreach (json_decode($verification_form_1) as $key => $element)
                                @if ($element->type == 'location')
                                <div class="form-group">
                                    <label>{{ translate('Province')}} <span class="text-primary">*</span></label>
                                    <select class="form-control mb-3" id="data-province" placeholder="{{ translate('Province')}}"
                                        name="province" required>
                                        <option value="">Select Province</option>
                                        @foreach (\App\Models\IndonesiaProvince::orderby('name', 'ASC')->get() as $key => $province)
                                            <option value="{{ $province->code }}">{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('City')}} <span class="text-primary">*</span></label>
                                    <select class="form-control mb-3" id="data-city" placeholder="{{ translate('City')}}"
                                        name="city" required>
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('District')}} <span class="text-primary">*</span></label>
                                    <select class="form-control mb-3" id="data-district" placeholder="{{ translate('District')}}"
                                        name="district" required>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Sub District')}} <span class="text-primary">*</span></label>
                                    <select class="form-control mb-3" id="data-sub_district" placeholder="{{ translate('Sub District')}}"
                                        name="sub_district" required>
                                        <option value="">Select Sub District</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Postal Code')}} <span class="text-primary">*</span></label>
                                    
                                    <input type="text" class="form-control" placeholder="{{ translate('Postal Code')}}" name="postal_code" id="postal_code" required disabled>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 h6">{{ translate('Verification info')}}</h4>
                        </div>
                        @php
                        $verification_form_1 = get_setting('verification_form');
                        @endphp
                        <div class="card-body">
                            @foreach (json_decode($verification_form_1) as $key => $element)
                            @if ($element->type == 'text')
                            <div class="row">
                                <div class="col-md-4">
                                    @if (!empty($element->is_required))
                                        @if ($element->is_required == 'true')
                                            <label>{{ $element->label }} <span class="text-danger">*</span></label>
                                        @else
                                            <label>{{ $element->label }}</label>
                                        @endif
                                    @else
                                        <label>{{ $element->label }}</label>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    @if (!empty($element->is_required))
                                        @if ($element->is_required == 'true')
                                            <input type="{{ $element->type }}" class="form-control mb-3" placeholder="{{ $element->label }}" name="element_{{ $key }}" id="element_{{ $key }}" required>
                                        @else  
                                            <input type="{{ $element->type }}" class="form-control mb-3" placeholder="{{ $element->label }}" name="element_{{ $key }}" id="element_{{ $key }}">
                                        @endif
                                    @else
                                        <input type="{{ $element->type }}" class="form-control mb-3" placeholder="{{ $element->label }}" name="element_{{ $key }}" id="element_{{ $key }}">
                                    @endif
                                </div>
                            </div>
                            @elseif($element->type == 'file')
                            <div class="row">
                                <div class="col-md-4">
                                    @if (!empty($element->is_required))
                                        @if ($element->is_required == 'true')
                                            <label>{{ $element->label }} <span class="text-primary">*</span>
                                                <span class="d-block text-muted"><small>Maximum File 2MB</small></span>
                                            </label>
                                        @else
                                            <label>{{ $element->label }}
                                                <span class="d-block text-muted"><small>Maximum File 2MB</small></span>
                                            </label>
                                        @endif
                                    @else
                                        <label>{{ $element->label }}
                                            <span class="d-block text-muted"><small>Maximum File 2MB</small></span>
                                        </label>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        @if (!empty($element->is_required))
                                            @if ($element->is_required == 'true')
                                                <input type="{{ $element->type }}" name="element_{{ $key }}" id="file-{{ $key }}" class="form-control-file form-control" required>
                                            @else
                                                <input type="{{ $element->type }}" name="element_{{ $key }}" id="file-{{ $key }}" class="form-control-file form-control">
                                            @endif
                                        @else
                                            <input type="{{ $element->type }}" name="element_{{ $key }}" id="file-{{ $key }}" class="form-control-file form-control">
                                        @endif
                                        <span class="invalid-feedback" role="alert">
                                          <strong>{{$element->label}} {{translate('cannot be empty')}}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @elseif ($element->type == 'select' && is_array(json_decode($element->options)))
                            <div class="row">
                                <div class="col-md-4">
                                    @if (!empty($element->is_required))
                                        @if ($element->is_required == 'true')
                                            <label>{{ $element->label }} <span class="text-primary">*</span></label>
                                        @else
                                            <label>{{ $element->label }}</label>
                                        @endif
                                    @else
                                        <label>{{ $element->label }}</label>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        @if (!empty($element->is_required))
                                            @if ($element->is_required == 'true')
                                            <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}" required>
                                                @foreach (json_decode($element->options) as $value)
                                                <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            @else
                                            <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}">
                                                @foreach (json_decode($element->options) as $value)
                                                <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            @endif
                                        @else
                                            <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}">
                                                @foreach (json_decode($element->options) as $value)
                                                <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @elseif ($element->type == 'multi_select' && is_array(json_decode($element->options)))
                            <div class="row">
                                <div class="col-md-4">
                                    @if (!empty($element->is_required))
                                        @if ($element->is_required == 'true')
                                            <label>{{ $element->label }} <span class="text-primary">*</span></label>
                                        @else
                                            <label>{{ $element->label }}</label>
                                        @endif
                                    @else
                                        <label>{{ $element->label }}</label>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        @if (!empty($element->is_required))
                                            @if ($element->is_required == 'true')
                                                <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}[]" multiple required>
                                                    @foreach (json_decode($element->options) as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}[]" multiple>
                                                    @foreach (json_decode($element->options) as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        @else
                                            <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_{{ $key }}[]" multiple>
                                                @foreach (json_decode($element->options) as $value)
                                                <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @elseif ($element->type == 'radio')
                            <div class="row">
                                <div class="col-md-2">
                                    @if (!empty($element->is_required))
                                        @if ($element->is_required == 'true')
                                            <label>{{ $element->label }} <span class="text-primary">*</span></label>
                                        @else
                                            <label>{{ $element->label }}</label>
                                        @endif
                                    @else
                                        <label>{{ $element->label }}</label>
                                    @endif
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        @if (!empty($element->is_required))
                                            @if ($element->is_required == 'true')
                                                @foreach (json_decode($element->options) as $value)
                                                <div class="radio radio-inline">
                                                    <input type="radio" name="element_{{ $key }}" value="{{ $value }}" id="{{ $value }}" required>
                                                    <label for="{{ $value }}">{{ $value }}</label>
                                                </div>
                                                @endforeach
                                            @else
                                                @foreach (json_decode($element->options) as $value)
                                                <div class="radio radio-inline">
                                                    <input type="radio" name="element_{{ $key }}" value="{{ $value }}" id="{{ $value }}">
                                                    <label for="{{ $value }}">{{ $value }}</label>
                                                </div>
                                                @endforeach
                                            @endif
                                        @else
                                            @foreach (json_decode($element->options) as $value)
                                            <div class="radio radio-inline">
                                                <input type="radio" name="element_{{ $key }}" value="{{ $value }}" id="{{ $value }}">
                                                <label for="{{ $value }}">{{ $value }}</label>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    @if(get_setting('google_recaptcha') == 1)
                        <div class="form-group mt-2 mx-auto row">
                            <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                        </div>
                    @endif

                    <div class="row register">
                        <div style="display:none !important" class="loader-bar col-12 d-flex justify-content-end">
                            <div class="loading-bar-button d-flex justify-content-center">
                                <img width="50" class="spinner" src="/public/assets/img/spinner.svg" alt="" srcset="">
                                <span class="align-self-center">
                                    {{ translate('Sedang Diproses')}}
                                </span>
                            </div>
                        </div>
                        <div class="button col-12 d-flex  justify-content-end">
                            <button type="submit" class="btn btn-primary fw-600">
                                {{ translate('Register Your Shop')}}
                            </button>
                        </div>
                    </div>

                    
                </form>
            </div>
        </div>
    </div>

    <!-- Modal TOS Customer -->
    <div class="modal fade" id="modalTosSeller" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalTosSellerTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Kebijakan Penggunaan & Privasi</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" style="max-height:50vh">
                                    {!! \App\Models\Page::find(2)->content !!}
                                </div>
                                <div class="modal-footer">
                                    <div class="mb-3">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" name="is_agree_tos_seller" value="1" id="checkbox_tos" required disabled>
                                            <span class=opacity-60>{{ translate('By signing up you agree to our terms and conditions.')}}</span>
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                    <button type="button" class="btn btn-primary" name="SubmitTosSeller" id="SubmitTosSeller" data-dismiss="modal" disabled>Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
</section>

@endsection

@section('script')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>

<script type="text/javascript">
    // making the CAPTCHA  a required field for form submission
    $('#shop').validate({
        rules: {
            email: {
                email: true,
                remote:"{{ route('validate.register') }}"
            },
            password: {
                minlength: 8
            },
            password_confirmation: {
                equalTo: "#password"
            },
        },
          
        messages: {
            email: {
                remote:"Email already exist"
            },
            password: {
                required: "Please enter password",
            },
            password_confirmation: {
                required: "Please enter confirm password",
                equalTo: "Password not match"
            }
        },
   
        submitHandler: function(form) {
            $('#modalTosSeller').modal('show');
            var test = document.getElementById("SubmitTosSeller");

            test.onclick = function(){
                form.submit();
            }
        }
    });
    
            $('#modalTosSeller .close').css('display', 'none');
            $('#modalTosSeller').on('shown.bs.modal', function () {
                let elementModal = document.getElementById('modalTosSeller')
                if(elementModal.getElementsByClassName('modal-body')[0].clientHeight <  elementModal.getElementsByClassName('modal-body')[0].scrollHeight){
                    $('#checkbox_tos').prop('disabled', true)
                    jQuery(function($) {
                        $('.modal-body').on('scroll', function(event) {
                            var element = event.target;
                            console.log('scrollHeight : ' + element.scrollHeight)
                            console.log('scrollTop : ' + element.scrollTop)
                            console.log('clientHeight : ' + element.clientHeight)
                            let rest = (element.scrollHeight - element.scrollTop) - element.clientHeight
                            if (rest < 20)
                            {
                                $('#checkbox_tos').prop('disabled', false)
                                $('#checkbox_tos').prop('checked', true)
                                $('#SubmitTosSeller').prop('disabled', false)
                                $('#tos-place').append(`
                                        <input type="checkbox" name="is_agree_tos_seller" value="1" class="d-none" checked>
                                    `)
                            }
                        });
                        var button = $('#SubmitTosSeller');
                        $('#checkbox_tos').change(function(event) {
                            button.prop('disabled', !$(event.target).is(':checked'));
                        });
                    });
                }else if(elementModal.getElementsByClassName('modal-body')[0].clientHeight === elementModal.getElementsByClassName('modal-body')[0].scrollHeight){
                    $('#checkbox_tos').prop('disabled', false)
                    $('#checkbox_tos').prop('checked', false)
                    var button = $('#SubmitTosSeller');
                    $('#checkbox_tos').change(function(event) {
                        button.prop('disabled', !$(event.target).is(':checked'));
                    });
                }
            })
        

    //jquery-mask-plugin
    $('#postal_code').mask('00000', {reverse: true});
    $('#element_6').mask('0000000000000000', {reverse: true});

    // $(document).ready(function(){
    //     // alert('helloman');
    //     $("#shop").on("submit", function(evt) {
    //         $(".register .loader-bar").show();
    //         $(".register button[type=submit]").attr('disabled', true);
    //     });
    // });

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
    })

    $(document).on('change', 'select[name=province]', function() {
        var item = $(this).val();
        getCity(item);
    });

    $(document).on('change', 'select[name=city]', function() {
        var item = $(this).val();
        getDistrict(item);
    });

    $(document).on('change', 'select[name=district]', function() {
        var item = $(this).val();
        getSubDistrict(item);
    });

    $(document).on('change', 'select[name=sub_district]', function() {
        var item = $(this).val();
        getPostalCode();
    });

    function getCity(code) {
        $('select[name="city"]').html($('<option/>').text('Select City'));
        $('select[name="city"]').attr('disabled', false);

        $('select[name="district"]').html($('<option/>').text('Select District'));
        $('select[name="district"]').attr('disabled', true);
        $('select[name="sub_district"]').html($('<option/>').text('Select Sub District'));
        $('select[name="sub_district"]').attr('disabled', true);
        $('text[name="postal_code"]').html($('<option/>').text('Select Postal Code'));
        $('text[name="postal_code"]').attr('disabled', true);

        // $('input[type="submit"]').attr('disabled', true);
        AIZ.plugins.bootstrapSelect('refresh');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{url('location/city')}}/"+code,
            type: 'GET',
            success: function(response) {
                $.each(response, function(i, data){
                    $('[name="city"]').append($('<option/>').text(data.name).val(data.code));
                });
                AIZ.plugins.bootstrapSelect('refresh');
            }
        })
    }

    function getDistrict(code) {
        $('select[name="district"]').html($('<option/>').text('Select District'));
        $('select[name="district"]').attr('disabled', false);
        $('select[name="sub_district"]').html($('<option/>').text('Select Sub District'));
        $('select[name="sub_district"]').attr('disabled', true);
        $('text[name="postal_code"]').html($('<option/>').text('Select Postal Code'));
        $('text[name="postal_code"]').attr('disabled', true);


        // $('input[type="submit"]').attr('disabled', true);

        AIZ.plugins.bootstrapSelect('refresh');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{url('location/district')}}/"+code,
            type: 'GET',
            success: function(response) {
                $.each(response, function(i, data){
                    $('[name="district"]').append($('<option/>').text(data.name).val(data.code));
                });
                AIZ.plugins.bootstrapSelect('refresh');
            }
        })
    }

    function getSubDistrict(code) {
        $('select[name="sub_district"]').html($('<option/>').text('Select Sub District'));
        $('select[name="sub_district"]').attr('disabled', false);
        $('text[name="postal_code"]').html($('<option/>').text('Select Postal Code'));
        $('text[name="postal_code"]').attr('disabled', false);

        // $('input[type="submit"]').attr('disabled', true);
        AIZ.plugins.bootstrapSelect('refresh');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{url('location/sub-district')}}/"+code,
            type: 'GET',
            success: function(response) {
                $.each(response, function(i, data){
                    $('[name="sub_district"]').append($('<option/>').text(data.name).val(data.code));
                });
                AIZ.plugins.bootstrapSelect('refresh');
            }
        })
    }

    function getPostalCode() {
        $('[name="postal_code"]').attr('disabled', false);
    }

</script>
@endsection
