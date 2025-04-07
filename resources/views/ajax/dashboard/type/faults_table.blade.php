<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (Eğer sayfanızda yoksa) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<h4>Son Arızalar Listesi</h4>
<br>

<h5>Durumlar</h5>
<div class="form-group">
  <select name="setting[status][]" 
          class="form-control select2" 
          multiple="multiple" 
          style="width: 100%;">
    <option value="Yeni"
      {{ (is_array($settings['status'] ?? null) && in_array('Yeni', $settings['status'])) ? 'selected' : '' }}>
      Yeni
    </option>
    <option value="Bekliyor |0|"
      {{ (is_array($settings['status'] ?? null) && in_array('Bekliyor |0|', $settings['status'])) ? 'selected' : '' }}>
      Bekliyor
    </option>
    <option value="Bakıma Başlandı |0|"
      {{ (is_array($settings['status'] ?? null) && in_array('Bakıma Başlandı |0|', $settings['status'])) ? 'selected' : '' }}>
      Bakıma Başlandı
    </option>
    <option value="Firma Yönlendirildi |2|"
      {{ (is_array($settings['status'] ?? null) && in_array('Firma Yönlendirildi |2|', $settings['status'])) ? 'selected' : '' }}>
      Firma Yönlendirildi
    </option>
    <option value="Malzeme Bekliyor |2|"
      {{ (is_array($settings['status'] ?? null) && in_array('Malzeme Bekliyor |2|', $settings['status'])) ? 'selected' : '' }}>
      Malzeme Bekliyor
    </option>
    <option value="Onay |1|"
      {{ (is_array($settings['status'] ?? null) && in_array('Onay |1|', $settings['status'])) ? 'selected' : '' }}>
      Tamamlandı
    </option>
  </select>
</div>
<br>

<div class="form-group" style="width: 100%;">
  <label class="control-label"><strong>Veri Gösterme Limiti</strong></label>
  <input type="number" 
         name="setting[limit]" 
         class="form-control" 
         style="width: 100%;" 
         value="{{ $settings['limit'] ?? '10' }}" />
</div>

<!-- Select2 başlatma -->
<script>
  $(document).ready(function() {
    $('.select2').select2({
      placeholder: 'Durum(lar) seçiniz',
      allowClear: true
    });
  });
</script>
