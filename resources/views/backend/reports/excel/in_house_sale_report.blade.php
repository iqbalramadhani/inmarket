
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Penjualan Produk Internal</title>
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
                <th style="text-align: center; background: #ffd68a;"><strong>Nama Produk</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Total Terjual</strong></th>
            @foreach ($products as $key => $product)
                <tr>
                    <td>{{ $product->getTranslation('name') }}</td>
                    <td>{{ $product->num_of_sale }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>