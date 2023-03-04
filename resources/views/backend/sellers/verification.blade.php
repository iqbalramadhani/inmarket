@extends('backend.layouts.app')

@section('content')

<div class="card">
  <div class="card-header">
      <h5 class="mb-0 h6">{{ translate('Seller Verification') }}</h5>
      <div class="pull-right clearfix">
        <a href="{{ route('sellers.reject', $seller->id) }}" class="btn btn-default d-innline-block">{{translate('Reject')}}</a></li>
        <a href="{{ route('sellers.approve', $seller->id) }}" class="btn btn-circle btn-dark d-innline-block">{{translate('Accept')}}</a>
      </div>
  </div>
  <div class="card-body row">
      <div class="col-md-5">
          <h6 class="mb-4">{{ translate('User Info') }}</h6>
          <p class="text-muted">
              <strong>{{ translate('Name') }} :</strong>
              <span class="ml-2">{{ $seller->user->name }}</span>
          </p>
          <p class="text-muted">
              <strong>{{translate('Email')}}</strong>
              <span class="ml-2">{{ $seller->user->email }}</span>
          </p>
          <!-- <p class="text-muted">
              <strong>{{translate('Address')}}</strong>
              <span class="ml-2">{{ $seller->user->address }}</span>
          </p>
          <p class="text-muted">
              <strong>{{translate('Phone')}}</strong>
              <span class="ml-2">{{ $seller->user->phone }}</span>
          </p> -->
          <br>

          <h6 class="mb-4">{{ translate('Shop Info') }}</h6>
          <p class="text-muted">
              <strong>{{translate('Shop Name')}}</strong>
              <span class="ml-2">{{ $seller->user->shop->name }}</span>
          </p>
          <p class="text-muted">
              <strong>{{translate('Address')}}</strong>
              <span class="ml-2">{{ $seller->user->shop->address }}</span>
          </p>
          <p class="text-muted">
              <strong>{{translate('Province')}}</strong>
              @if ($seller->user->shop->indonesia_province_id)
                <span class="ml-2">{{ $seller->user->shop->indonesia_province->name }}</span>
              @else
                <span class="ml-2">-</span>
              @endif
          </p>
          <p class="text-muted">
              <strong>{{translate('City')}}</strong>
              @if ($seller->user->shop->indonesia_city_id)
                <span class="ml-2">{{ $seller->user->shop->indonesia_city->name }}</span>
              @else
                <span class="ml-2">-</span>
              @endif
          </p>
          <p class="text-muted">
              <strong>{{translate('District')}}</strong>
              @if ($seller->user->shop->indonesia_district_id)
                <span class="ml-2">{{ $seller->user->shop->indonesia_district->name }}</span>
              @else
                <span class="ml-2">-</span>
              @endif
          </p>
          <p class="text-muted">
              <strong>{{translate('Sub District')}}</strong>
              @if ($seller->user->shop->indonesia_subdistrict_id)
                <span class="ml-2">{{ $seller->user->shop->indonesia_village->name }}</span>
              @else
                <span class="ml-2">-</span>
              @endif
          </p>
          <br>

          <h6 class="mb-4">Alamat Pickup</h6>
          <p class="text-muted">
              <strong>{{translate('Address')}}</strong>
              <span class="ml-2">{{ $seller->user->shop->address }}</span>
          </p>
          <p class="text-muted">
              <strong>{{translate('Province')}}</strong>
              @if ($seller->user->shop->province_id)
                <span class="ml-2">{{ $seller->user->shop->rajaongkir_province->province_name  }}</span>
              @else
                <span class="ml-2">-</span>
              @endif
          </p>
          <p class="text-muted">
              <strong>{{translate('City')}}</strong>
              @if ($seller->user->shop->city_id)
                <span class="ml-2">{{ $seller->user->shop->rajaongkir_city->city_name  }}</span>
              @else
                <span class="ml-2">-</span>
              @endif
          </p>
          <p class="text-muted">
              <strong>{{translate('Sub District')}}</strong>
              @if ($seller->user->shop->subdistrict_id)
                <span class="ml-2">{{ $seller->user->shop->rajaongkir_subdistrict->subdistrict_name  }}</span>
              @else
                <span class="ml-2">-</span>
              @endif
          </p>
          <p class="text-muted">
              <strong>{{translate('Postal Code')}}</strong>
              @if ($seller->user->shop->postal_code)
                <span class="ml-2">{{ $seller->user->shop->postal_code }}</span>
              @else
                <span class="ml-2">-</span>
              @endif
          </p>
      </div>
      <div class="col-md-5">
        <h6 class="mb-4">{{ translate('Verification Info') }}</h6>
          <table class="table table-striped table-bordered" cellspacing="0" width="100%">
              <tbody>
                  @foreach (json_decode($seller->verification_info) as $key => $info)
                      <tr>
                          @if ($info->type != 'location')
                          <th class="text-muted">{{ $info->label }}</th>
                          @endif
                          @if ($info->type == 'text' || $info->type == 'select' || $info->type == 'radio')
                              <td>{{ $info->value }}</td>
                          @elseif ($info->type == 'multi_select')
                              <td>
                                {{(($info->value == 'null') ? 'Tidak Ada' : (is_array(json_decode($info->value)) ? implode(', ', json_decode($info->value)) : $info->value))}}
                              </td>
                          @elseif ($info->type == 'file')
                              <td>
                                  <a href="{{ my_asset($info->value) }}" target="_blank" class="btn-info">{{translate('Click here')}}</a>
                              </td>
                          @endif
                      </tr>
                  @endforeach
              </tbody>
          </table>
          <div class="text-center">
              <a href="{{ route('sellers.reject', $seller->id) }}" class="btn btn-sm btn-default d-innline-block">{{translate('Reject')}}</a></li>
              <a href="{{ route('sellers.approve', $seller->id) }}" class="btn btn-sm btn-dark d-innline-block">{{translate('Accept')}}</a>
          </div>
      </div>
  </div>
</div>

@endsection
