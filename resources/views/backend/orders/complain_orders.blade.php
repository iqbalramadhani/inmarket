@extends('backend.layouts.app')
@section('content')
    <div class="card">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">Complained Orders</h5>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th data-breakpoints="md">Kode</th>
                    <th data-breakpoints="md">Penjual</th>
                    <th data-breakpoints="md">Pelanggan </th>
                    <th data-breakpoints="md">Jumlah</th>
                    <th data-breakpoints="md" class="text-right">Detail</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <td>
                            {{ $order->code }}
                        </td>
                        <td>{{$order->seller->name ?? ''}}</td>
                        <td>
                            @if ($order->user != null)
                                {{ $order->user->name }}
                            @else
                                Guest ({{ $order->guest_id }})
                            @endif
                        </td>
                        <td>
                            {{ single_price($order->grand_total) }}
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('admin.complained-orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                        </td>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
            </div>


        </div>
    </div>
    </div>
@endsection
