@extends('voyager::master')

@section('page_title', 'Yeni Bildirim Oluştur')

@section('content')
<div class="page-content container-fluid" style="background: #f4f7f6; padding-top: 20px;">
    @include('voyager::alerts')
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border:none;">
                <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 15px 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="panel-title" style="font-weight: 600; color: #444; margin:0; padding:0;">
                            <i class="voyager-bell" style="color: #3498db; margin-right: 8px;"></i> Bildirim Kuralları Tanımla
                        </h3>
                        <button type="button" class="btn btn-primary add-row-btn" style="margin:0; border-radius: 20px; font-size: 11px; padding: 5px 15px;">
                            <i class="voyager-plus"></i> KURAL EKLE
                        </button>
                    </div>
                </div>
                
                <div class="panel-body" style="padding: 20px;">
                    <form action="{{ route('events.store') }}" method="POST" id="main-form">
                        @csrf
                        <div id="event-rows">
                            <div class="event-item-card">
                                <div class="row custom-row-layout">
                                    <div class="col-item col-device">
                                        <label class="custom-label">Cihaz</label>
                                        <select name="items[0][device_id]" class="form-control device-select" required>
                                            <option value="">Seçiniz...</option>
                                            @foreach($devices as $device)
                                                <option value="{{ $device->id }}">{{ $device->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-item col-tag">
                                        <label class="custom-label">Etiket</label>
                                        <select name="items[0][tag_id]" class="form-control tag-select" required>
                                            <option value="">Bekleniyor...</option>
                                        </select>
                                    </div>
                                    <div class="col-item col-val">
                                        <label class="custom-label">Min</label>
                                        <input type="number" step="any" name="items[0][min]" class="form-control" value="0">
                                    </div>
                                    <div class="col-item col-val">
                                        <label class="custom-label">Max</label>
                                        <input type="number" step="any" name="items[0][max]" class="form-control" value="100">
                                    </div>
                                    <div class="col-item col-mail">
                                        <label class="custom-label">Bildirim E-postası</label>
                                        <div class="input-group" style="margin:0;">
                                            <span class="input-group-addon" style="padding: 0 10px;"><i class="voyager-mail"></i></span>
                                            <input type="email" name="items[0][email]" class="form-control" placeholder="mail@adres.com" required>
                                        </div>
                                    </div>
                                    <div class="col-item col-action">
                                        <div style="width: 30px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions text-right" style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                            <button type="submit" class="btn btn-success" style="border-radius: 4px; padding: 10px 40px; font-weight: 600;">
                                <i class="voyager-check"></i> KAYDET
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Yeni satır şablonu --}}
<template id="row-template">
    <div class="event-item-card animated fadeIn" style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #fcfcfc;">
        <div class="row custom-row-layout">
            <div class="col-item col-device">
                <select name="items[IDX][device_id]" class="form-control device-select" required>
                    <option value="">Seçiniz...</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-item col-tag">
                <select name="items[IDX][tag_id]" class="form-control tag-select" required>
                    <option value="">Bekleniyor...</option>
                </select>
            </div>
            <div class="col-item col-val">
                <input type="number" step="any" name="items[IDX][min]" class="form-control" value="0">
            </div>
            <div class="col-item col-val">
                <input type="number" step="any" name="items[IDX][max]" class="form-control" value="100">
            </div>
            <div class="col-item col-mail">
                <div class="input-group" style="margin:0;">
                    <span class="input-group-addon" style="padding: 0 10px;"><i class="voyager-mail"></i></span>
                    <input type="email" name="items[IDX][email]" class="form-control" placeholder="mail@adres.com" required>
                </div>
            </div>
            <div class="col-item col-action text-right">
                <button type="button" class="btn btn-link remove-row-btn" style="padding:0; margin-top: 8px;">
                    <i class="voyager-trash" style="color: #e74c3c; font-size: 16px;"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<style>
    /* Boşlukları daraltan kompakt düzen */
    .custom-row-layout { display: flex; align-items: flex-start; margin: 0 -3px; }
    .col-item { padding: 0 3px; }
    
    .col-device { width: 22%; }
    .col-tag { width: 18%; }
    .col-val { width: 7%; }
    .col-mail { width: 42%; flex-grow: 1; }
    .col-action { width: 40px; min-width: 40px; }

    .custom-label { font-size: 9px; text-transform: uppercase; color: #aaa; font-weight: 700; margin-bottom: 3px; display: block; }
    
    .form-control { height: 34px !important; border: 1px solid #ddd; border-radius: 4px !important; font-size: 13px; }
    .input-group-addon { border: 1px solid #ddd; border-right: none; background: #fafafa; height: 34px; }
    
    .animated { animation-duration: 0.3s; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .fadeIn { animation-name: fadeIn; }

    /* Voyager'ın select2 çakışmalarını önlemek için */
    .select2-container { width: 100% !important; }
</style>
@stop

@section('javascript')
<script>
$(document).ready(function () {
    let rowIdx = 1;

    // Satır Ekleme
    $('.add-row-btn').click(function() {
        let html = $('#row-template').html().replace(/IDX/g, rowIdx);
        $('#event-rows').append(html);
        rowIdx++;
    });

    // Satır Silme
    $(document).on('click', '.remove-row-btn', function() {
        $(this).closest('.event-item-card').remove();
    });

    // Cihaz Değişince Etiketleri Getir
    $(document).on('change', '.device-select', function() {
        let deviceId = $(this).val();
        // Sütun yapımız değiştiği için en yakın kapsayıcıyı doğru seçmeliyiz
        let row = $(this).closest('.custom-row-layout'); 
        let tagSelect = row.find('.tag-select');

        if (!deviceId) return;

        tagSelect.empty().append('<option>Yükleniyor...</option>');

        // Rota adını kullanarak URL oluşturuyoruz
        let url = "{{ route('events.get-tags', ['deviceId' => ':id']) }}".replace(':id', deviceId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                tagSelect.empty().append('<option value="">Etiket Seçin</option>');
                if(res.length > 0) {
                    $.each(res, function(i, item) {
                        tagSelect.append('<option value="'+ item.id +'">'+ item.name +'</option>');
                    });
                } else {
                    tagSelect.append('<option value="">Etiket bulunamadı</option>');
                }
            },
            error: function(xhr) {
                console.error("Hata detayı:", xhr.responseText);
                tagSelect.empty().append('<option value="">Hata oluştu!</option>');
            }
        });
    });
});
</script>
@stop