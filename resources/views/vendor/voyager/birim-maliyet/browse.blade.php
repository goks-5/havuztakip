@extends('voyager::master')

@section('content')
<div class="container mt-4">
    <h2 class="page-title">Birim Maliyet</h2>
    <button class="btn btn-primary" id="openPopup">Ekle</button>
</div>

<!-- Popup Modal -->
<div id="popupModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Birim Maliyet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mt-4">
                    <label for="device1">Cihaz 1</label>
                    <select class="form-control" id="device1">
                        <option value="">Cihaz Seçiniz</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}">{{ $device->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mt-4">
                    <label for="device2">Cihaz 2</label>
                    <select class="form-control" id="device2">
                        <option value="">Cihaz Seçiniz</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}">{{ $device->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary">Kaydet</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .page-title {
        margin-bottom: 20px;
    }
</style>
@endsection

@section('javascript')
<script>
    document.getElementById('openPopup').addEventListener('click', function () {
        $('#popupModal').modal('show');
    });
</script>
@endsection
