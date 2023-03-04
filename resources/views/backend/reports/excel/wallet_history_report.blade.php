
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
                <th style="text-align: center; background: #ffd68a;"><strong>Customer</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Waktu</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Amount</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Cara Pembayaran</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Persetujuan</strong></th>
                @foreach ($wallets as $key => $wallet)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        @if ($wallet->user != null)
                            <td>{{ $wallet->user->name }}</td>
                        @else
                            <td>{{ translate('User Not found') }}</td>
                        @endif
                        <td>{{ date('d-m-Y', strtotime($wallet->created_at)) }}</td>
                        <td>{{ single_price($wallet->amount) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $wallet->payment_method)) }}</td>
                        <td class="text-right">
                            @if ($wallet->offline_payment)
                                @if ($wallet->approval)
                                    <span class="badge badge-inline badge-success">{{translate('Approved')}}</span>
                                @else
                                    <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @endforeach
        </tbody>
    </table>
</body>
</html>