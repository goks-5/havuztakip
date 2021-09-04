<div class="form-group row">
    @include('ajax.dashboard.type.element.main',['slug'=>'device_chart' , 'ek' => 1])
</div>
<div class="form-group row">
  <div class="col-xs-6">
    <label class="control-label">Çizim Tipi</label>
    <select class="form-control select2" name="setting[type]">
      <option value="line" {{isset($settings['type']) && $settings['type'] == "line" ? 'selected':''}}>Cizgi Grafik (Line)</option>
      <option value="Column" {{isset($settings['type']) && $settings['type'] == "Column" ? 'selected':''}}>Blok Grafik (Column)</option>
    </select>
  </div>
  <div class="col-xs-6">
    <label class="control-label">Veri Aralığı</label>
    <select class="form-control select2" name="setting[hour]">
      <option value="6" {{isset($settings['hour']) && $settings['hour'] == "6" ? 'selected':''}}>Son 6 Saat</option>
      <option value="24" {{isset($settings['hour']) && $settings['hour'] == "24" ? 'selected':''}}>Son 1 Gün</option>
      <option value="72" {{isset($settings['hour']) && $settings['hour'] == "72" ? 'selected':''}}>Son 3 Gün</option>
      <option value="168" {{isset($settings['hour']) && $settings['hour'] == "168" ? 'selected':''}}>Son 7 Gün</option>
      <option value="720" {{isset($settings['hour']) && $settings['hour'] == "720" ? 'selected':''}}>Son 1 Ay</option>
      <option value="2160" {{isset($settings['hour']) && $settings['hour'] == "2160" ? 'selected':''}}>Son 3 Ay</option>
      <option value="8640" {{isset($settings['hour']) && $settings['hour'] == "8640" ? 'selected':''}}>Son 1 Yıl</option>
    </select>
  </div>
</div>
