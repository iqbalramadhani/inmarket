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
                <td><input type="radio" {{$service == json_encode($data) ? 'checked' : ''}} class="service_type" name="service_type[{{$seller_id}}]" value="{{json_encode($data)}}" required></td>
                <td>{{$data['service']}}</td>
                <td>{{$data['cost'][0]['value']}}</td>
                <td>{{$data['cost'][0]['etd']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
