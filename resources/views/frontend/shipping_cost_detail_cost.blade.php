<div class="col-md-8">
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td>Dari</td>
            <td>{{$from_text}}</td>
        </tr>
        <tr>
            <td>Tujuan</td>
            <td>{{$to_text}}</td>
        </tr>
        <tr>
            <td>Berat</td>
            <td>{{$weight}} (Gram)</td>
        </tr>
        </tbody>
    </table>
</div>
<div class="col-md-8">
    <table class="table">
        <thead>
        <th>Nama Layanan</th>
        <th>Tarif</th>
        <th>Estimasi</th>
        </thead>
        <tbody>
        @foreach($results[0]['costs'] as $data)
        <tr>
            <td>{{$data['service']}}</td>
            <td>{{$data['cost'][0]['value']}}</td>
            <td>{{$data['cost'][0]['etd']}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
