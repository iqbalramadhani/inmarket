
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Penjualan Produk Penjual</title>
</head>
<body>
  <table>
        <thead>
            <!-- <tr>
                <th>Table Heading</th>
            </tr> -->
        </thead>
        <tbody>
            <tr>
                <th style="text-align: center; background: #ffd68a;"><strong>No.</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Kode Pemesanan</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Komisi Admin</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Penghasilan Penjual</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Biaya Pengiriman</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Dibuat di</strong></th>
            @foreach ($commission_history as $key => $history)
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>
                        @if(isset($history->order))
                            {{ $history->order->code }}
                        @else
                            <span class="badge badge-inline badge-danger">
                                translate('Order Deleted')
                            </span>
                        @endif
                    </td>
                    <td>{{ $history->order->orderDetails[0]->total_commission_inatrade }}</td>
                    <td>{{ $history->order->orderDetails[0]->total_commission_seller }}</td>
                    <td>{{ $history->order->orderDetails[0]->shipping_cost }}</td>
                    <td>{{ $history->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>