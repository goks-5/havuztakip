  <H4>Son Arızalar Listesi</H4>
  @include('ajax.dashboard.type.element.main', ['slug' => 'faults_table', 'ek' => 1])
  <br>
/***

<option value='Bekliyor |0|'>Bekliyor</option>
<option value='Bakıma Başlandı |0|'>Bakıma Başlandı</option>
<option value='Firma Yönlendirildi |2|'>Firma Yönlendirildi</option>
<option value='Malzeme Bekliyor |2|'>Malzeme Bekliyor</option>
<option value='Onay |1|'>Tamamlandı</option>

*/
<h5>Durumlar</h5>
 <div class="col-xs-3">
      <label class="control-label">Bekliyor</label>
      <input type='hidden' value='0' name='setting[status][Bekliyor |0|]'>
      <input type="checkbox" name="setting[status][Bekliyor |0|]" class="form-check-input" style="display:block" value="1"
          id='sound' {{ $setting['status']['Bekliyor |0|'] ?? 0 == '1' ? 'checked' : '' }} />
  </div>
  <div class="col-xs-3">
    <label class="control-label">Bakıma Başlandı</label>
    <input type='hidden' value='0' name='setting[status][Bakıma Başlandı |0|]'>
    <input type="checkbox" name="setting[status][Bakıma Başlandı |0|]" class="form-check-input" style="display:block" value="1"
        id='sound' {{ $setting['status']['Bakıma Başlandı |0|'] ?? 0  == '1' ? 'checked' : '' }} />
</div>
<div class="col-xs-3">
  <label class="control-label">Firma Yönlendirildi</label>
  <input type='hidden' value='0' name='setting[status][Firma Yönlendirildi |2|]'>
  <input type="checkbox" name="setting[status][Firma Yönlendirildi |2|]" class="form-check-input" style="display:block" value="1"
      id='sound' {{ $setting['status']['Firma Yönlendirildi |2|'] ?? 0  == '1' ? 'checked' : '' }} />
</div>
<div class="col-xs-3">
  <label class="control-label">Malzeme Bekliyor</label>
  <input type='hidden' value='0' name='setting[status][Malzeme Bekliyor |2|]'>
  <input type="checkbox" name="setting[status][Malzeme Bekliyor |2|]" class="form-check-input" style="display:block" value="1"
      id='sound' {{ $setting['status']['Malzeme Bekliyor |2|'] ?? 0  == '1' ? 'checked' : '' }} />
</div>
<div class="col-xs-3">
  <label class="control-label">Tamamlandı</label>
  <input type='hidden' value='0' name='setting[status][Onay |1|]'>
  <input type="checkbox" name="setting[status][Onay |1|]" class="form-check-input" style="display:block" value="1"
      id='sound' {{ $setting['status']['Onay |1|'] ?? 0  == '1' ? 'checked' : '' }} />
</div>
<br>
<div class="col-xs-3">
  <label class="control-label">Veri Gösterme Limiti</label>
<input type="number" name="setting[limit]" class="form-control" value="{{$settings['limit'] ?? '10'}}" />
</div>
