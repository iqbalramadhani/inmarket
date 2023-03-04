@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="card">
    <div class="card-header">
        <div class="aiz-titlebar mt-2 mbs-4">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <h1 class="h3">Rekening Bank Anda</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form action="{{route('bank.update', ['id' => $detail->id])}}" method="POST">
            @csrf
            <div class="row mb-1 ">
                <div class="col-md-4">
                    <label>{{ translate('Kode Bank')}} <span class="text-danger">*</span></label>
                </div>
                <div class="col-md-8">
                    <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="bank_code" data-live-search="true" required>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->code }}" {{ ( $bank->code == $detail->bank_code) ? 'selected' : '' }}> {{ $bank->name }} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-md-4">
                    <label>{{ translate('No Rekening ')}} <span class="text-danger">*</span></label>
                </div>
                <div class="col-md-8">
                    <input type="text" lang="en" class="form-control" value="{{$detail->account_number}}" name="account_number" id="account_number" placeholder="{{ translate('Account Number')}}" required>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-md-4">
                    <label>{{ translate('Nama Pemilik Rekening ')}} <span class="text-danger">*</span></label>
                </div>
                <div class="col-md-8">
                    <input type="text" lang="en" class="form-control" value="{{$detail->account_name}}" name="account_name" placeholder="{{ translate('Name')}}" required>
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-between">
                <a href="{{route('shops.index')}}" class="btn btn-sm btn-secondary">Kembali</a>
                <button type="submit" id="buttonSimpan" class="btn btn-sm btn-primary d-none">Simpan</button>
                <button type="submit" id="checkButton" class="btn btn-sm btn-primary">Check</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var spinner = `
            <div class="spinner-border spinner-border-sm" id="spinner" role="status"></div>
        `;
        $('input[name="account_number"]').on({
            mouseleave: function(){
                $('#buttonSimpan').addClass('d-none')
                $('#checkButton').removeClass('d-none')
                $('#checkButton').html('Check');
            },
        });

        $('#checkButton').on('click', function(event) {
            let bank_code = $('select[name="bank_code"]').val()
            let account_number = $('input[name="account_number"]').val()
            let account_name = $('input[name="account_name"]').val()

            if(! account_number){
                AIZ.plugins.notify('danger', 'Masukan nomor rekening');
                return;
            }
            event.preventDefault()
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'POST',
                url: "{{route('bank.check-account')}}",
                data: {
                    bank_code: bank_code,
                    account_number: account_number
                },
                beforeSend: function() {
                    $('#checkButton').html(spinner)
                },
                success: function(data){
                    if(data.valid){
                        $('input[name="account_name"]').val(data.account_name)
                        $('#buttonSimpan').removeClass('d-none')
                        $('#checkButton').addClass('d-none')
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Nomor rekening tidak valid',
                        })
                        $('#buttonSimpan').addClass('d-none')
                        $('#checkButton').removeClass('d-none')
                        $('#checkButton').html('Check');
                    }
                },
                error: function(error){
                    console.log(error)
                    AIZ.plugins.notify('danger', error.responseJSON.data);
                }
            })
        })

        //jquery-mask-plugin
        $('#account_number').mask('0000000000000000000000', {reverse: true});
    </script>
@endsection
