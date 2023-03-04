@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
      <div class="col-md-6">
          <h1 class="h3">{{ translate('My Wallet') }}</h1>
      </div>
    </div>
    </div>
    <div class="row gutters-10">
      <div class="col-md-3 mx-auto mb-3" >
          <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
            <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                <!-- <i class="las la--sign la-2x text-white"></i> -->
                <h4>Rp.</h4>
            </span>
            <div class="px-3 pt-3 pb-3">
                <div class="h4 fw-700 text-center">{{ single_price(Auth::user()->balance) }}</div>
                <div class="opacity-50 text-center">{{ translate('Wallet Balance') }}</div>
            </div>
          </div>
      </div>
      <div class="col-md-3 mx-auto mb-3" >
        <div class="p-3 rounded mb-3 c-pointer text-center bg-grad-2 shadow-sm hov-shadow-lg has-transition" onclick="show_withdraw_modal()">
            <span class="size-60px rounded-circle mx-auto bg-default d-flex align-items-center justify-content-center mb-3">
                <i class="las la-wallet la-3x text-white"></i>
            </span>
            <div class="fs-18 text-white">{{ translate('Withdraw') }}</div>
        </div>
      </div>
      <div class="col-md-3 mx-auto mb-3" >
        <div class="p-3 rounded mb-3 c-pointer text-center bg-grad-3 shadow-sm hov-shadow-lg has-transition" onclick="show_wallet_modal()">
            <span class="size-60px rounded-circle mx-auto bg-default d-flex align-items-center justify-content-center mb-3">
                <i class="las la-plus la-3x text-white"></i>
            </span>
            <div class="fs-18 text-white">{{ translate('Top Up') }}</div>
        </div>
      </div>
      @if (\App\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
          <div class="col-md-4 mx-auto mb-3" >
              <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition" onclick="show_make_wallet_recharge_modal()">
                  <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                      <i class="las la-plus la-3x text-white"></i>
                  </span>
                  <div class="fs-18 text-primary">{{ translate('Offline Recharge Wallet') }}</div>
              </div>
          </div>
      @endif
    </div>
    <div class="card">
      <div class="card-header">
          <h5 class="mb-0 h6">{{ translate('Wallet History')}}</h5>
      </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                  <tr>
                      <th>#</th>
                      <th data-breakpoints="lg">{{  translate('Date') }}</th>
                      <th data-breakpoints="lg">{{ translate('Tipe')}}</th>
                      <th data-breakpoints="lg">{{ translate('Payment Method')}}</th>
                      <th>{{ translate('Amount')}}</th>
                      <th data-breakpoints="lg">{{ translate('Status')}}</th>
                      <th data-breakpoints="lg" >{{ translate('Payment Confirmation')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($wallets as $key => $wallet)
                      <tr>
                          <td>{{ $key+1 }}</td>
                          <td>{{ date('d-m-Y', strtotime($wallet->created_at)) }}</td>
                          <td >
                            @if($wallet->type=='TOPUP')
                                <span class="badge badge-inline badge-success">{{ ucfirst(str_replace('_', ' ', 'Top Up')) }}</span>
                            @elseif ($wallet->type=='DISBURSEMENT')  
                                <span class="badge badge-inline badge-danger">{{ ucfirst(str_replace('_', ' ', 'Withdrawal')) }}</span>
                            @endif
                            </td>
                          <td >{{ ucfirst(str_replace('_', ' ', $wallet ->payment_method)) }}</td>
                          <td>{{ single_price($wallet->amount) }}</td>
                          <td >
                            @if($wallet->type=='TOPUP')
                                @if ($wallet->oy_id!=null && ($wallet->oy_id->status=='COMPLETE' || $wallet->oy_id->status=='Success'))
                                    <span class="badge badge-inline badge-success">{{translate($wallet->oy_id->status)}}</span>
                                @elseif ($wallet->oy_id!=null)
                                    <span class="badge badge-inline badge-warning">{{translate($wallet->oy_id->status)}}</span>
                                @else
                                    <span class="badge badge-inline badge-default">{{translate('No Data')}}</span>
                                @endif
                            @elseif ($wallet->type=='DISBURSEMENT')
                                @if ($wallet->oy_withdraw!=null && $wallet->oy_withdraw->status=='Success')
                                    <span class="badge badge-inline badge-success">{{translate($wallet->oy_withdraw->status)}}</span>
                                @elseif ($wallet->oy_withdraw!=null)
                                    <span class="badge badge-inline badge-warning">{{translate($wallet->oy_withdraw->status)}}</span>
                                @else
                                    <span class="badge badge-inline badge-default">{{translate('No Data')}}</span>
                                @endif
                            @endif
                        </td>
                            <td >
                                @if (($wallet->oy_id!=null && $wallet->oy_id->status=='COMPLETE') || ($wallet->oy_withdraw!=null && $wallet->oy_withdraw->status=='Success'))
                                @elseif ($wallet->oy_id!=null || $wallet->oy_withdraw!=null)
                                    <a href="{{route('wallet.check.payment', ['wallet_id' => $wallet->id])}}" class="btn btn-warning btn-xs btn-disable">{{translate('Konfirmasi')}}</button>
                                @else
                                    <!-- <a class="btn btn-warning btn-sm">{{translate('Pay')}}</a> -->
                                @endif
                            </td>
                          <!-- <td class="text-right">
                              @if ($wallet->offline_payment)
                                  @if ($wallet->approval)
                                      <span class="badge badge-inline badge-success">{{translate('Approved')}}</span>
                                  @else
                                      <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                                  @endif
                              @else
                                  N/A
                              @endif
                          </td> -->
                      </tr>
                  @endforeach

                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $wallets->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')

  <div class="modal fade" id="wallet_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Recharge Wallet') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
              </div>
              <form class="" action="{{ route('wallet.recharge') }}" method="post">
                  @csrf
                  <div class="modal-body gry-bg px-3 pt-3">
                      <div class="row">
                          <div class="col-md-4">
                              <label>{{ translate('Amount')}} <span class="text-danger">*</span></label>
                          </div>
                          <div class="col-md-8">
                              <input type="text" lang="en" class="decimal_separator_topup form-control mb-3" name="amount" placeholder="{{ translate('Amount')}}" required>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-md-4">
                              <label>{{ translate('Payment Method')}} <span class="text-danger">*</span></label>
                          </div>
                          <div class="col-md-8">
                              <div class="mb-3">
                                  <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="payment_option" data-live-search="true">
                                      @if (get_setting('oyid_payment') == 1)
                                          <option value="oyid_va">{{ translate('Bank Transfer (Auto)')}}</option>
                                      @endif
                                      @if (get_setting('oyid_payment') == 1)
                                          <option value="oyid_card">{{ translate('Credit/Debit Card')}}</option>
                                      @endif
                                      @if (get_setting('oyid_payment') == 1)
                                          <option value="oyid_qris">{{ translate('QRIS')}}</option>
                                      @endif
                                      @if (get_setting('oyid_payment') == 1)
                                          <option value="oyid_wallet">{{ translate('E-Wallet')}}</option>
                                      @endif
                                      @if (get_setting('paypal_payment') == 1)
                                          <option value="paypal">{{ translate('Paypal')}}</option>
                                      @endif
                                      @if (get_setting('stripe_payment') == 1)
                                          <option value="stripe">{{ translate('Stripe')}}</option>
                                      @endif
                                      @if (get_setting('sslcommerz_payment') == 1)
                                          <option value="sslcommerz">{{ translate('SSLCommerz')}}</option>
                                      @endif
                                      @if (get_setting('instamojo_payment') == 1)
                                          <option value="instamojo">{{ translate('Instamojo')}}</option>
                                      @endif
                                      @if (get_setting('paystack') == 1)
                                          <option value="paystack">{{ translate('Paystack')}}</option>
                                      @endif
                                      @if (get_setting('voguepay') == 1)
                                          <option value="voguepay">{{ translate('VoguePay')}}</option>
                                      @endif
                                      @if (get_setting('payhere') == 1)
                                          <option value="payhere">{{ translate('Payhere')}}</option>
                                      @endif
                                      @if (get_setting('ngenius') == 1)
                                          <option value="ngenius">{{ translate('Ngenius')}}</option>
                                      @endif
                                      @if (get_setting('razorpay') == 1)
                                          <option value="razorpay">{{ translate('Razorpay')}}</option>
                                      @endif
                                      @if (get_setting('iyzico') == 1)
                                          <option value="iyzico">{{ translate('Iyzico')}}</option>
                                      @endif
                                      @if (get_setting('proxypay') == 1)
                                          <option value="proxypay">{{ translate('Proxypay')}}</option>
                                      @endif
                                      @if (get_setting('bkash') == 1)
                                          <option value="bkash">{{ translate('Bkash')}}</option>
                                      @endif
                                      @if (get_setting('nagad') == 1)
                                          <option value="nagad">{{ translate('Nagad')}}</option>
                                      @endif
                                      @if(\App\Addon::where('unique_identifier', 'african_pg')->first() != null && \App\Addon::where('unique_identifier', 'african_pg')->first()->activated)
                                          @if (get_setting('mpesa') == 1)
                                              <option value="mpesa">{{ translate('Mpesa')}}</option>
                                          @endif
                                          @if (get_setting('flutterwave') == 1)
                                              <option value="flutterwave">{{ translate('Flutterwave')}}</option>
                                          @endif
                                          @if (get_setting('payfast') == 1)
                                              <option value="payfast">{{ translate('PayFast')}}</option>
                                          @endif
                                      @endif
                                      @if (\App\Addon::where('unique_identifier', 'paytm')->first() != null && \App\Addon::where('unique_identifier', 'paytm')->first()->activated)
                                          <option value="paytm">{{ translate('Paytm')}}</option>
                                      @endif
                                  </select>
                              </div>
                          </div>
                      </div>
                      <div class="form-group text-right">
                          <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{translate('Confirm')}}</button>
                      </div>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <div class="modal fade" id="withdraw_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Withdraw Wallet') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
              </div>
              <form class="" id="withdraw_form" action="{{ route('wallet.withdraw') }}" method="post">
                  @csrf
                  <div class="modal-body gry-bg px-3 pt-3">
                      <div class="row">
                          <div class="col-md-4">
                              <label>{{ translate('Amount')}} <span class="text-danger">*</span></label>
                          </div>
                          <div class="col-md-8">
                              <input type="text" lang="en" class="decimal_separator_withdraw form-control mb-3" name="amount" placeholder="{{ translate('Amount')}}" required>
                          </div>
                      </div>
                      <div class="details">
                          <div class="row">
                              <div class="col-md-4">
                                  <label>{{ translate('Bank Account')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control mb-3 selectpicker" data-minimum-results-for-search="Infinity" name="bank_id" data-live-search="true">
                                        @foreach($accounts as $account)
                                            <option value="{{$account->id}}">{{$account->bank_name.'|'.$account->account_number.' - '.$account->account_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-md-4">
                                    <label>{{ translate('No Rekening ')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" lang="en" class="form-control" name="account_number" placeholder="{{ translate('Account Number')}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="offset-md-4 col-md-8">
                                    <div id="receiver_info" class="text-left d-none p-2"> 
                                        <img width="50px" class="spinner" src="public/assets/img/spinner.svg" alt="" srcset="">
                                        <p class="text-success d-inline" id="receiver_name"><span></span></p>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{translate('Continue')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
      </div>
  </div>


  <!-- offline payment Modal -->
  <div class="modal fade" id="offline_wallet_recharge_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">{{ translate('Offline Recharge Wallet') }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
              </div>
              <div id="offline_wallet_recharge_modal_body"></div>
          </div>
      </div>
  </div>

@endsection

@section('script')
    <script type="text/javascript">

        let configurationAutoNumeric = {
            allowDecimalPadding: false,
            createLocalList: false,
            currencySymbol: "Rp. ",
            decimalCharacter: ",",
            digitGroupSeparator: ".",
            maximumValue: "1000000000",
            minimumValue: "0"
        }
        let withdraw_numeric = new AutoNumeric('.decimal_separator_withdraw', configurationAutoNumeric);
        let wallet_wnumeric = new AutoNumeric('.decimal_separator_topup', configurationAutoNumeric);
        
        $( "#wallet_modal form" ).submit(function( event ) {
            wallet_wnumeric.unformat();
            return 1;
        });
        $( "#withdraw_modal form" ).submit(function( event ) {
            withdraw_numeric.unformat();
            return 1;
        });

        var delay = (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })()

        // let $filter = $('.details input[name="account_number"]');
        $('.details input[name="account_number"]').on('keyup', function () {

            if($('.details input[name="account_number"]').val() == '' || $('.details select[name="bank_code"]').val()=='') {
                return 0;
            }

            $('.spinner').removeClass(['d-none']);
            $('#receiver_info').removeClass(['d-none']);
        
            delay(function () {
                check_account();
            }, 2000);
        });
        $('.details select[name="bank_code"]').on('change', function () {
            if($('.details input[name="account_number"]').val() == '' || $('.details select[name="bank_code"]').val()=='') {
                return 0;
            }
            
            $('.spinner').removeClass(['d-none']);
            $('#receiver_info').removeClass(['d-none']);
        
            delay(function () {
                check_account();
            }, 2000);
        });

        function check_account() {
            // $.ajax({
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     },
            //     url: "{{route('oy.account_inquiry')}}",
            //     type: 'POST',
            //     data: {
            //         bank_code : $('.details select[name="bank_code"]').val(),
            //         account_number : $('.details input[name="account_number"]').val(),
            //     },
            //     success: function (response) {
            //         if(response.status.message.toLowerCase()=='success') {
            //             $('.spinner').addClass(['d-none']);
            //             $('#receiver_name span').html('A/N ' + response.account_name)
            //             $('#withdraw_form button[type="submit"]').attr({'disabled': false});
            //         } else {
            //             $('#withdraw_form button[type="submit"]').attr({'disabled': true});
            //         }
            //     },
            //     error : function(error) {
            //         console.log(error);
            //         $('#withdraw_form button[type="submit"]').attr({'disabled': true});
            //     }
            // });
        }

        

        function show_wallet_modal(){
            $('#wallet_modal').modal('show');
        }
        function show_withdraw_modal(){
            $('#withdraw_modal').modal('show');
        }

        function show_make_wallet_recharge_modal(){
            $.post('{{ route('offline_wallet_recharge_modal') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#offline_wallet_recharge_modal_body').html(data);
                $('#offline_wallet_recharge_modal').modal('show');
            });
        }
    </script>
@endsection
