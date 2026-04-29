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
<form id="searchForm" method="GET" action="{{ route('voyager.veriler.browse') }}">
  <div class="page-header container-fluid">
    <h1 class="page-title mb-0">
      <i class="voyager-data"></i> Veriler
    </h1>
    <input 
      type="text" 
      id="searchBox" 
      name="search" 
      class="form-control form-control-sm" 
      placeholder="Ara..."
      value="{{ old('search', $search) }}"
    >
  </div>
</form>
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
          <th>Veriler</th>
        </tr>
      </thead>
      <tbody>
        @foreach($devices as $device)
          <tr>
            <td>{{ $device->device_id }}</td>
            <td>{{ $device->name }}</td>
            <td>{{ $device->last_at }}</td>
            <td>
              @foreach(json_decode($device->tags, true) ?: [] as $tag)
                <div>{{ $tag }}</div>
              @endforeach
            </td>
            <td>
              <a href="{{ route('veriler.show', $device->id) }}"
                 class="btn btn-sm btn-primary">
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
  const searchBox  = document.getElementById('searchBox');
  const searchForm = document.getElementById('searchForm');
  let debounceTimer;

  searchBox.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      searchForm.submit();
    }, 300);
  });
</script>
@stop
