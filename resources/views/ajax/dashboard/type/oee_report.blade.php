@php
    use App\Device;
    $devices = Device::all(); // Veritabanından tüm cihazları çekiyoruz.
@endphp

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Cihaz Seçimi ve Ayarlar</title>
</head>
<body>

<!-- Cihaz Seçme Alanı (Settings İçinde Saklanıyor) -->
<!-- Cihaz Seçme Alanı -->
<div class="form-group text-center">
    <label for="deviceDropdown" class="control-label"><strong>Cihaz</strong></label>
    <select class="form-control" id="deviceDropdown" name="setting[device]">
        <option value="">Cihaz Seçiniz</option>
        @foreach($devices as $device)
            <option 
                value="{{ $device->id }}"
                data-tags="{{ $device->tags }}" 
                {{ isset($settings['device']) && $settings['device'] == $device->id ? 'selected' : '' }}
            >
                {{ $device->name }}
            </option>
        @endforeach
    </select>
</div>

<!-- Kullanılabilirlik Alanı -->
<div class="form-group row">
    <div class="col-12 text-center">
        <label class="control-label"><strong>Kullanılabilirlik</strong></label>
    </div>
    <div class="col-md-12">
        <label class="control-label d-block text-start">Planlanan Üretim Süresi (Dakika)</label>
        <input type="number" class="form-control" name="setting[planned_production_time]" placeholder="Planlanan Üretim Süresi" 
               value="{{ isset($settings['planned_production_time']) ? $settings['planned_production_time'] : '' }}">
    </div>
</div>

<!-- Performans Alanı -->
<div class="form-group row">
    <div class="col-12 text-center">
        <label class="control-label"><strong>Performans</strong></label>
    </div>
    <div class="col-md-6">
        <label class="control-label d-block text-start">Beklenen Üretim</label>
        <input type="number" class="form-control" name="setting[expected_output]" placeholder="Beklenen Üretim" 
               value="{{ isset($settings['expected_output']) ? $settings['expected_output'] : '' }}">
    </div>
    <div class="col-md-6" id="actualOutputContainer">
    <label class="control-label d-block text-start">Gerçek Üretim</label>
    <input type="number" class="form-control" name="setting[actual_output]" placeholder="Gerçek Üretim" 
           value="{{ isset($settings['actual_output']) ? $settings['actual_output'] : '' }}">
    </div>
</div>

<!-- Veri Aralığı Alanı -->
<div class="form-group row">
    <div class="col-12 text-center">
        <label class="control-label"><strong>Veri Aralığı</strong></label>
    </div>
    <div class="col-md-12">
        <select class="form-control select2" name="setting[hour]">
          <option value="6" {{ isset($settings['hour']) && $settings['hour'] == "6" ? 'selected' : '' }}>Son 6 Saat</option>
          <option value="24" {{ isset($settings['hour']) && $settings['hour'] == "24" ? 'selected' : '' }}>Son 1 Gün</option>
          <option value="D" {{ isset($settings['hour']) && $settings['hour'] == 'D' ? 'selected' : '' }}>Bugün</option>
          <option value="72" {{ isset($settings['hour']) && $settings['hour'] == "72" ? 'selected' : '' }}>Son 3 Gün</option>
          <option value="168" {{ isset($settings['hour']) && $settings['hour'] == "168" ? 'selected' : '' }}>Son 7 Gün</option>
          <option value="W" {{ isset($settings['hour']) && $settings['hour'] == 'W' ? 'selected' : '' }}>Bu Hafta</option>
          <option value="720" {{ isset($settings['hour']) && $settings['hour'] == "720" ? 'selected' : '' }}>Son 1 Ay</option>
          <option value="M" {{ isset($settings['hour']) && $settings['hour'] == 'M' ? 'selected' : '' }}>Bu Ay</option>
          <option value="2160" {{ isset($settings['hour']) && $settings['hour'] == "2160" ? 'selected' : '' }}>Son 3 Ay</option>
          <option value="8640" {{ isset($settings['hour']) && $settings['hour'] == "8640" ? 'selected' : '' }}>Son 1 Yıl</option>
          <option value="Y" {{ isset($settings['hour']) && $settings['hour'] == 'Y' ? 'selected' : '' }}>Bu Yıl</option>
        </select>
    </div>
</div>

<!-- JavaScript Kodları -->
<script>
  document.getElementById('deviceDropdown').addEventListener('change', function() {
      var selectedOption = this.options[this.selectedIndex];
      var tagsStr = selectedOption.getAttribute('data-tags') || ""; 

      console.log("Seçilen cihazın tagleri (ham string):", tagsStr);

      // Gizlemek istediğimiz container'ı seçelim
      var actualOutputContainer = document.getElementById('actualOutputContainer');

      try {
          // JSON parse edelim
          var tagsObj = JSON.parse(tagsStr);

          // JSON parse edilince tagsObj bir obje veya dizi olabilir.
          // Değerleri tek tek kontrol ediyoruz:
          var foundMetraj = false;
          Object.values(tagsObj).forEach(function(tagValue) {
              // Her bir tagValue içinde 'metraj' var mı diye case-insensitive kontrol
              if (tagValue.toLowerCase().includes('metraj')) {
                  foundMetraj = true;
              }
          });

          // Eğer 'metraj' varsa alanı gizle, yoksa göster
          if (foundMetraj) {
              actualOutputContainer.style.display = 'none';
              console.log("Bu cihazın resource type'ında 'metraj' var. Gerçek Üretim alanını gizledik.");
          } else {
              actualOutputContainer.style.display = 'block';
              console.log("Bu cihazın resource type'ında 'metraj' yok. Gerçek Üretim alanını gösterdik.");
          }

      } catch (e) {
          console.error("JSON parse hatası: ", e);
      }
  });
</script>

</body>
</html>
