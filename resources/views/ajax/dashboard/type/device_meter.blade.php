<div class="form-group row">
  @include('ajax.dashboard.type.element.main',['slug'=>'device_meter', 'ek' => 1])
</div>
<div class="form-group row">
  <div class="col-xs-4">
    <label class="control-label">Minumum</label>
   <input type="number" name="setting[min]" class="form-control"  value="{{$settings['min'] ?? '0'}}"/>
  </div>
  <div class="col-xs-4">
    <label class="control-label">Maksimum</label>
 <input type="number" name="setting[max]" class="form-control"  value="{{$settings['max'] ?? '100'}}"/>
  </div>

  <div class="col-xs-4">
    <label class="control-label">Uzunluk (px)</label>
 <input type="number" name="setting[width]" class="form-control"  value="{{$settings['width'] ?? '300'}}"/>
  </div>
  <div class="col-xs-4">
    <label class="control-label">Genişlik (px)</label>
  <input type="number" name="setting[height]" class="form-control"  value="{{$settings['height'] ?? '20'}}"/>
  </div>


  <div class="col-xs-4">
    <label class="control-label">Yön</label>
    <select name="setting[rotate]" class="form-control">
    <option value="0" @if(($settings['rotate'] ?? 0) == 0)  selected @endif >Sol</option>
    <option value="180" @if(($settings['rotate'] ?? 0) == 180)  selected @endif>Sağ</option>
    <option value="270" @if(($settings['rotate'] ?? 0) == 270)  selected @endif>Yukarı</option>
    <option value="90" @if(($settings['rotate'] ?? 0) == 90)  selected @endif>Aşağı</option>
    </select>
  </div>
</div>
<div class="form-group row">
  <div class="col-xs-6">
    <label class="control-label">Düşük Seviye Bitişi</label>
  </div>
  <div class="col-xs-6"> <input type="number" name="setting[low]" class="form-control"  value="{{$settings['low'] ?? '25'}}"/>
  </div>
  <div class="col-xs-6">
    <label class="control-label">Yüksek Seviye Başlangıcı</label>
  </div>
  <div class="col-xs-6"> <input type="number" name="setting[high]" class="form-control"  value="{{$settings['high'] ?? '75'}}"/>
  </div>

</div>
