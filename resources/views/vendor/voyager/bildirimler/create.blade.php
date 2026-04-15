@extends('voyager::master')

@section('content')
<div class="page-content container-fluid">
    <div class="panel panel-bordered">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="voyager-plus"></i> Yeni Olay Ekle</h3>
        </div>
        <div class="panel-body">
            <form action="{{ route('events.store') }}" method="POST">
                @csrf
                <div id="event-rows">
                    <div class="row event-row" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <div class="col-md-3">
                            <label>Cihaz Seçin</label>
                            <select name="items[0][device_id]" class="form-control device-select" required>
                                <option value="">Seçiniz...</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}">{{ $device->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Etiketler</label>
                            <select name="items[0][tag_id]" class="form-control tag-select" required>
                                <option value="">Önce Cihaz Seçin</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Min</label>
                            <input type="number" step="any" name="items[0][min]" class="form-control" value="0">
                        </div>
                        <div class="col-md-2">
                            <label>Max</label>
                            <input type="number" step="any" name="items[0][max]" class="form-control" value="100">
                        </div>
                        <div class="col-md-2" style="margin-top: 25px;">
                            <button type="button" class="btn btn-success" id="add-row-btn"><i class="voyager-plus"></i></button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <label>Bildirim Maili</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@enerjiyonetim.com" required>
                    </div>
                    <div class="col-md-4" style="margin-top: 25px;">
                        <button type="submit" class="btn btn-primary btn-block">Kaydet</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Yeni satır şablonu --}}
<template id="row-template">
    <div class="row event-row" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
        <div class="col-md-3">
            <select name="items[IDX][device_id]" class="form-control device-select" required>
                <option value="">Seçiniz...</option>
                @foreach($devices as $device)
                    <option value="{{ $device->id }}">{{ $device->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="items[IDX][tag_id]" class="form-control tag-select" required>
                <option value="">Önce Cihaz Seçin</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" step="any" name="items[IDX][min]" class="form-control" value="0">
        </div>
        <div class="col-md-2">
            <input type="number" step="any" name="items[IDX][max]" class="form-control" value="100">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-row"><i class="voyager-trash"></i></button>
        </div>
    </div>
</template>
@stop

@section('javascript')
<script>
$(document).ready(function () {
    let rowIdx = 1;

    // Yeni satır ekle
    $('#add-row-btn').click(function() {
        let html = $('#row-template').html().replace(/IDX/g, rowIdx);
        $('#event-rows').append(html);
        rowIdx++;
    });

    // Satır sil
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.event-row').remove();
    });

    // Cihaz değişince JSON Tags çek
    $(document).on('change', '.device-select', function() {
        let deviceId = $(this).val();
        let row = $(this).closest('.event-row');
        let tagSelect = row.find('.tag-select');

        if (!deviceId) return;

        tagSelect.empty().append('<option>Yükleniyor...</option>');

        // window.location.origin kullanarak 404 riskini bitiriyoruz
        let url = window.location.origin + '/admin/get-tags/' + deviceId;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                tagSelect.empty().append('<option value="">Seçiniz...</option>');
                $.each(res, function(i, item) {
                    // item.id (anahtar) ve item.name (değer) basılıyor
                    tagSelect.append('<option value="'+ item.id +'">'+ item.name +'</option>');
                });
            },
            error: function() {
                tagSelect.empty().append('<option value="">Veri alınamadı!</option>');
            }
        });
    });
});
</script>
@stop