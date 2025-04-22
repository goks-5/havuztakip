@extends('voyager::master')

@section('css')
<style>
    /* Header içindeki arama kutusu */
    .page-header {
        position: relative;
        padding-bottom: 20px;
    }
    #searchBox {
        position: absolute;
        top: 45px;
        right: 15px;
        width: 200px;
    }
    /* Tablo-responsive eski ayarlar kaldırıldı */
    .table-responsive {
        position: static;
        padding-top: 0;
    }
    /* Alt pagination alanını sağa yaslamak için */
    .pagination-container {
        display: flex;
        justify-content: flex-end;
        padding-top: 1rem;
    }
</style>
@stop

@section('page_title', 'Veriler')

@section('page_header')
    <div class="page-header container-fluid">
        <h1 class="page-title mb-0">
            <i class="voyager-data"></i> Veriler
        </h1>
        <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Ara...">
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="devicesTable">
            <thead>
                <tr>
                    <th>Cihaz Id</th>
                    <th>Cihaz Adı</th>
                    <th>Son Veri Tarihi</th>
                    <th>Etiketler</th>
                    <th>Veriler</th> <!-- Yeni sütun -->
                </tr>
            </thead>
            <tbody>
                @php
                    // Sadece silinmemişleri alıyoruz ve 10'arlı sayfalama
                    $devices = DB::table('devices')
                                ->whereNull('deleted_at')
                                ->paginate(10);
                @endphp

                @foreach($devices as $device)
                    <tr>
                        <td>{{ $device->device_id }}</td>
                        <td>{{ $device->name }}</td>
                        <td>{{ $device->last_at }}</td>
                        <td>
                            @php
                                $tags = json_decode($device->tags, true) ?: [];
                            @endphp
                            @foreach($tags as $tag)
                                <div>{{ $tag }}</div>
                            @endforeach
                        </td>
                        <td>
                            <a href="/veriler/{{ $device->id }}" class="btn btn-sm btn-primary" title="Göster">
                                <i class="voyager-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-container">
            {{ $devices->links() }}
        </div>
    </div>
</div>
@stop

@section('javascript')
    <script>
        document.getElementById('searchBox').addEventListener('input', function() {
            var filter = this.value.toLowerCase();
            document.querySelectorAll('#devicesTable tbody tr').forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(filter)
                                  ? '' : 'none';
            });
        });
    </script>
@stop
