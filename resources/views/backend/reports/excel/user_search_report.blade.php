
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pencarian Pengguna</title>
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
                <th style="text-align: center; background: #ffd68a;"><strong>Cari Berdasarkan</strong></th>
                <th style="text-align: center; background: #ffd68a;"><strong>Jumlah Pencarian</strong></th>
            </tr>
            @foreach ($searches as $key => $search)
                <tr>
                    <td>{{ $search->query }}</td>
                    <td>{{ $search->count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>