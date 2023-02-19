  <H4>Son Arızalar Listesi</H4>
  @include('ajax.dashboard.type.element.main', ['slug' => 'faults_table', 'ek' => 1])
  <br>

<h5>Durumlar</h5>
 <div class="col-xs-3">
      <label class="control-label">Bekliyor</label> 
      <input type="checkbox" name="setting[status][0]" class="form-check-input" style="display:block" value="Bekliyor |0|"
          id='sound' {{ $setting['status']['0'] ?? '0' == 'Bekliyor |0|' ? 'checked' : '' }} />
  </div>
  <div class="col-xs-3">
    <label class="control-label">Bakıma Başlandı</label>
    <input type="checkbox" name="setting[status][1]" class="form-check-input" style="display:block" value="Bakıma Başlandı |0|"
        id='sound' {{ $setting['status']['1'] ?? '0'  == 'Bakıma Başlandı |0|' ? 'checked' : '' }} />
</div>
<div class="col-xs-3">
  <label class="control-label">Firma Yönlendirildi</label>
  <input type="checkbox" name="setting[status][2]" class="form-check-input" style="display:block" value="Firma Yönlendirildi |2|"
      id='sound' {{ $setting['status']['2'] ?? '0'  == 'Firma Yönlendirildi |2|' ? 'checked' : '' }} />
</div>
<div class="col-xs-3">
  <label class="control-label">Malzeme Bekliyor</label>
  <input type="checkbox" name="setting[status][3]" class="form-check-input" style="display:block" value="Malzeme Bekliyor |2|"
      id='sound' {{ $setting['status']['3'] ?? '0'  == 'Malzeme Bekliyor |2|' ? 'checked' : '' }} />
</div>
<div class="col-xs-3">
  <label class="control-label">Tamamlandı</label>
  <input type="checkbox" name="setting[status][4]" class="form-check-input" style="display:block" value="Onay |1|"
      id='sound' {{ $setting['status']['4'] ?? '0'  == 'Onay |1|' ? 'checked' : '' }} />
</div>
<br>
<div class="col-xs-3">
  <label class="control-label">Veri Gösterme Limiti</label>
<input type="number" name="setting[limit]" class="form-control" value="{{$settings['limit'] ?? '10'}}" />
</div>

<br>
<br>
<br>
<br>
<br>
