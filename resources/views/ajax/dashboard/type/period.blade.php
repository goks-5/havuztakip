  <div class="form-group row">
      @include('ajax.dashboard.type.element.main', ['slug' => 'period', 'ek' => 1])


      <div class="col-xs-4">
          <label class="control-label">Veri Aralığı</label>
          <select class="form-control select2" name="setting[hour]">
              <option value="6" {{ isset($settings['hour']) && $settings['hour'] == '6' ? 'selected' : '' }}>Son 6
                  Saat</option>
              <option value="24" {{ isset($settings['hour']) && $settings['hour'] == '24' ? 'selected' : '' }}>Son 1
                  Gün</option>
              <option value="72" {{ isset($settings['hour']) && $settings['hour'] == '72' ? 'selected' : '' }}>Son 3
                  Gün</option>
              <option value="168" {{ isset($settings['hour']) && $settings['hour'] == '168' ? 'selected' : '' }}>Son 7
                  Gün</option>
              <option value="720" {{ isset($settings['hour']) && $settings['hour'] == '720' ? 'selected' : '' }}>Son 1
                  Ay</option>
              <option value="2160" {{ isset($settings['hour']) && $settings['hour'] == '2160' ? 'selected' : '' }}>Son 3
                  Ay</option>
              <option value="8640" {{ isset($settings['hour']) && $settings['hour'] == '8640' ? 'selected' : '' }}>Son 1
                  Yıl</option>
          </select>
      </div>

      <div class="col-xs-4">
          <label class="control-label">Tarih Satırlarda</label>
          <input type='hidden' value='0' name='setting[data_type]'>
          <input type="checkbox" name="setting[data_type]" class="form-check-input" style="display:block" value="1"
              id='data_type' {{ isset($settings['data_type']) && $settings['data_type'] == '1' ? 'checked' : '' }} />
      </div>
      <div class="col-xs-4">
        <label class="control-label">Eskiden Yeniye</label>
        <input type='hidden' value='0' name='setting[order_asc]'>
        <input type="checkbox" name="setting[order_asc]" class="form-check-input" style="display:block" value="1"
            id='order_asc' {{ isset($settings['order_asc']) && $settings['order_asc'] == '1' ? 'checked' : '' }} />
    </div>
  </div>
