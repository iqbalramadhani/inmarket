<div class="col-md-12">
    <table class="table">
        <thead>
        <th></th>
        <th>Nama Layanan</th>
        <th>Tarif</th>
        <th>Estimasi</th>
        </thead>
        <tbody>
        @foreach($results[0]['costs'] as $data)
            <tr>
                <td><input type="radio" name="service_type[{{$seller_id}}]" value="{{json_encode($data)}}"></td>
                <td>{{$data['service']}}</td>
                <td>{{$data['cost'][0]['value']}}</td>
                <td>{{$data['cost'][0]['etd']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
