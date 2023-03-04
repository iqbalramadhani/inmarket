<h4>Hi Admin</h5>
<h5>Terjadi Perubahan Data Penjual</h5>
Nama Usaha : {{$shop->name}}
<br>
Nama Pemilik : {{$shop->user()->first()->name}}
</br>
</br>

<table width="100%">
    <tr style="text-align:left;">
        <th style="border-bottom:2px solid #ccc;">Jenis Data</th>
        <th style="border-bottom:2px solid #ccc;">Lama</th>
        <th style="border-bottom:2px solid #ccc;">Sekarang</th>
    </tr>
    @foreach($data as $item)
    <tr style="text-align:left;">
        <td style="border-bottom:1px solid #ccc;">{{$item['label'] ?? ''}} :</td>
        <td style="border-bottom:1px solid #ccc;">{{$item['old_value'] ?? ''}}</td>
        <td style="border-bottom:1px solid #ccc; color:green; font-weight:bold">{{$item['value'] ?? ''}}</td>
    <tr>
    @endforeach
</table>

</br>
<h4>Terimakasih</h4>
<h5>Support</h5>