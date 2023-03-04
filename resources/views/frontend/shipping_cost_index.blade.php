@extends('frontend.layouts.app')

@section('content')
    <section class="pt-4 mb-4">
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-left">
                    <h1 class="fw-600 h4">{{ translate('Tarif Kiriman') }}</h1>
                </div>
                <div class="col-lg-6">
                    <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                        <li class="breadcrumb-item opacity-50">
                            <a class="text-reset" href="{{ route('home') }}">{{ translate('Home') }}</a>
                        </li>
                        <li class="text-dark fw-600 breadcrumb-item">
                            <a class="text-reset" href="{{ route('orders.track') }}">"{{ translate('Tarif Kiriman') }}"</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="mb-5">
        <div class="container text-left">
            <div class="row">
                <div class="col-xxl-8 col-xl-8 col-lg-8 mx-auto">
                    <form class="" action="{{ route('orders.track') }}" method="GET" enctype="multipart/form-data">
                        <div class="bg-white rounded shadow-sm">
                            <div class="fs-15 fw-600 p-3 border-bottom text-center">
                                {{ translate('Tarif Kiriman')}}
                            </div>
                            <div class="form-box-content p-3">
                                <p style="margin-bottom: 5px; font-weight: bold;">Dari</p>
                                <div class="form-group test">
                                    <select class="form-control" name="Provinsi" id="select2_from_provinces" placeholder="Provinsi">
                                    </select>
                                </div>
                                <div class="form-group test">
                                    <select name="" class="form-control" id="select2-from-cities" placeholder="Kabupaten">
                                    </select>
                                </div>

                                <p style="margin-bottom: 5px; font-weight: bold;">Tujuan</p>
                                <div class="form-group test">
                                    <select class="form-control" name="Provinsi" id="select2-to-provinces" placeholder="Provinsi">
                                    </select>
                                </div>
                                <div class="form-group test">
                                    <select name="" class="form-control" id="select2-to-cities" placeholder="Kabupaten">
                                    </select>
                                </div>

                                <div class="form-group input-group mb-2">
                                    <input type="number" name="weight" class="form-control" placeholder="Berat (gram) ">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">Gram</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <select name="courier" id="select-courier" class="form-control">
                                        <option value="pos">POS</option>
                                        <option value="tiki">TIKI</option>
                                        <option value="jne">JNE</option>
                                        <option value="pcp">PCP</option>
                                        <option value="esl">ESL</option>
                                        <option value="rpx">RPX</option>
                                        <option value="pandu">Pandu</option>
                                        <option value="wahana">Wahana</option>
                                        <option value="jnt">JNT</option>
                                        <option value="pahala">Pahala</option>
                                        <option value="cahaya">Cahaya</option>
                                        <option value="sap">SAP</option>
                                        <option value="indah">Indah</option>
                                        <option value="dse">DSE</option>
                                        <option value="slis">SLIS</option>
                                        <option value="first">First</option>
                                        <option value="ncs">NCS</option>
                                        <option value="star">Star</option>
                                    </select>
                                </div>
                                <div class="text-center">
                                    <button type="button" id="button_check_cost" class="btn btn-primary">{{ translate('Cek Tarif')}}</button>
                                </div>
                            </div>

                            <div class="spinner-border" id="spinner" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>

                            <div class="div" id="detail_cost">

                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script
        src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>

        $('#spinner').hide();

        $('#select2_from_provinces').select2({
            placeholder: "Pilih Provinsi",
            ajax: {
                url: '/ajax-get-provinces',
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: $.map(data.rajaongkir.results, function (item) {
                            return {
                                text: item.province,
                                id: item.province_id
                            }
                        })
                    };
                }
            }
        });

        $('#select2-from-cities').hide()

        $('#select2_from_provinces').on('change', function(){
            let province_id = $(this).val()
            $('#select2-from-cities').val(null).trigger('change');
            $('#select2-from-cities').show()
            $('#select2-from-cities').select2({
                placeholder: "Pilih Kabupaten",
                ajax: {
                    url: '/ajax-get-cities',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            province_id: province_id,
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.rajaongkir.results, function (item) {
                                return {
                                    text: item.city_name,
                                    id: item.city_id
                                }
                            })
                        };
                    }
                }
            });
        });


        $('#select2-to-provinces').select2({
            placeholder: "Pilih Provinsi",
            ajax: {
                url: '/ajax-get-provinces',
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: $.map(data.rajaongkir.results, function (item) {
                            return {
                                text: item.province,
                                id: item.province_id
                            }
                        })
                    };
                }
            }
        });
        $('#select2-to-cities').hide()

        $('#select2-to-provinces').on('change', function(){
            let province_id = $(this).val()
            $('#select2-to-cities').val(null).trigger('change');
            $('#select2-to-cities').show()
            $('#select2-to-cities').select2({
                placeholder: "Pilih Kabupaten",
                ajax: {
                    url: '/ajax-get-cities',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            province_id: province_id,
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.rajaongkir.results, function (item) {
                                return {
                                    text: item.city_name,
                                    id: item.city_id
                                }
                            })
                        };
                    }
                }
            });
        });

        $('#button_check_cost').on('click', function(){
            let from_text = $('#select2-from-cities option:selected').text()
            let to_text = $('#select2-to-cities option:selected').text()
            let from_id = $('#select2-from-cities').val()
            let to_id = $('#select2-to-cities').val()
            let weight = $('input[name="weight"]').val();
            let courier = $('select[name="courier"]').val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'POST',
                url: '/ajax-get-cost',
                data: {
                    from_text: from_text,
                    to_text: to_text,
                    from_id: from_id,
                    to_id: to_id,
                    weight: weight,
                    courier: courier
                },
                beforeSend: function() {
                    $("#detail_cost").hide()
                    $("#spinner").show();
                },
                success: function(data){
                    $("#spinner").hide();
                    $("#detail_cost").show()
                    $("#detail_cost").html(data)
                }
            })
        });
    </script>
@endsection
