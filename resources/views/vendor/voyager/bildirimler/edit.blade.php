@extends('voyager::master')

@section('page_title', 'Kural Düzenle')

@section('content')
<div class="page-content container-fluid" style="background: #f4f7f6; padding-top: 20px;">
    <div class="panel panel-bordered" style="border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border:none;">
        <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 20px;">
            <h3 class="panel-title" style="font-weight: 700; color: #333; margin: 0;">
                <i class="voyager-edit" style="color: #3498db; margin-right: 8px;"></i> Kural Düzenle
            </h3>
        </div>
        
        <div class="panel-body" style="padding: 25px;">
            <form action="{{ route('events.update', $event->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row custom-row-layout">
                    <div class="col-item col-device">
                        <label class="custom-label">Cihaz</label>
                        <select name="device_id" class="form-control device-select" required>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}" {{ $event->device_id == $device->id ? 'selected' : '' }}>
                                    {{ $device->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-item col-tag">
                        <label class="custom-label">Etiket</label>
                        <select name="tag_id" class="form-control tag-select" required data-selected="{{ $event->tag_id }}">
                            @php
                                // Mevcut cihazın taglerini PHP ile decode edip seçili olanı isimlendiriyoruz
                                $currentDevice = $devices->where('id', $event->device_id)->first();
                                $tags = json_decode($currentDevice->tags ?? '[]', true);
                            @endphp
                            @if($tags)
                                @foreach($tags as $key => $val)
                                    <option value="{{ $key }}" {{ $event->tag_id == $key ? 'selected' : '' }}>
                                        {{ $val }}
                                    </option>
                                @endforeach
                            @else
                                <option value="{{ $event->tag_id }}" selected>{{ $event->tag_id }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="col-item col-val">
                        <label class="custom-label">Min</label>
                        <input type="number" step="any" name="min_value" class="form-control" value="{{ $event->min_value }}">
                    </div>

                    <div class="col-item col-val">
                        <label class="custom-label">Max</label>
                        <input type="number" step="any" name="max_value" class="form-control" value="{{ $event->max_value }}">
                    </div>

                    <div class="col-item col-mail">
                        <label class="custom-label">Bildirim E-postası</label>
                        <div class="input-group" style="margin:0;">
                            <span class="input-group-addon"><i class="voyager-mail"></i></span>
                            <input type="email" name="email" class="form-control" value="{{ $event->email }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-actions text-right" style="margin-top: 30px; border-top: 1px solid #f5f5f5; padding-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="border-radius: 4px; padding: 10px 40px; font-weight: 600;">
                        <i class="voyager-check"></i> DEĞİŞİKLİKLERİ KAYDET
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-row-layout { display: flex; align-items: center; margin: 0 -8px; }
    .col-item { padding: 0 8px; }
    .col-device { width: 20%; }
    .col-tag { width: 20%; }
    .col-val { width: 8%; }
    .col-mail { width: 44%; flex-grow: 1; }
    .custom-label { font-size: 10px; text-transform: uppercase; color: #bbb; font-weight: 700; margin-bottom: 6px; display: block; }
    .form-control { height: 34px !important; border: 1px solid #e2e2e2; border-radius: 4px !important; }
</style>
@stop

@section('javascript')
<script>
$(document).ready(function () {
    // Cihaz değiştiğinde tetiklenir
    $(document).on('change', '.device-select', function() {
        let deviceId = $(this).val();
        let row = $(this).closest('.custom-row-layout'); // Satırı bul
        let tagSelect = row.find('.tag-select'); // O satırdaki tag select'i bul
        
        if (!deviceId) return;

        tagSelect.empty().append('<option>Yükleniyor...</option>');
        
        // Dinamik URL oluşturma (Voyager'ın base URL'ini dikkate alır)
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
                    tagSelect.append('<option value="">Etiket Bulunamadı</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Ajax Hatası: ", error);
                console.log("Sunucu Yanıtı: ", xhr.responseText);
                tagSelect.empty().append('<option value="">Yükleme Hatası!</option>');
            }
        });
    });
});
</script>
@stop