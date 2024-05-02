@extends('voyager::master')

@section('content')
<div class="container">
    <h1>Cihaz Verileri</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Tarih</th>
                <th>Veri</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deviceData as $data)
            <tr>
                <td>{{ $data->created_at }}</td>
                <td>{{ $data->data_value }}</td> <!-- Sizin veri modelinize göre düzenleyin -->
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection