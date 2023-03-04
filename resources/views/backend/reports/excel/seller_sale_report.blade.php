
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
                <th style="text-align: center; background: #ffd68a;"><strong>Nama Penjual</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Nama Toko</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Jumlah Penjualan Produk</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Total Penjualan</strong></th>
            @foreach ($datas as $data)
            <tr>
                <td style="text-align: center;">{{ $data->user->name }}</td>
                @if($data->user->shop != null)
                    <td style="text-align: center;">{{ $data->user->shop->name }}</td>
                @else
                    <td style="text-align: center;">--</td>
                @endif
                <td style="text-align: center;">
                    @php
                        $num_of_sale = 0;
                        foreach ($data->user->products as $key => $product) {
                            $num_of_sale += $product->num_of_sale;
                        }
                    @endphp
                    {{ $num_of_sale }}
                </td>
                <td style="text-align: center;">
                    {{ single_price(\App\Models\OrderDetail::where('seller_id', $data->user->id)->sum('price')) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>