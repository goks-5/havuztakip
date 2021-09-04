<div class="form-group row">
  @include('ajax.dashboard.type.element.main',['slug'=>'device_data_gauge', 'ek' => 1])

  <div class="col-xs-3">
    <label class="control-label">Minumum</label>
   <input type="number" name="setting[min]" class="form-control"  value="{{$settings['min'] ?? '0'}}"/>
  </div>
  <div class="col-xs-3">
    <label class="control-label">Maksimum</label>
 <input type="number" name="setting[max]" class="form-control"  value="{{$settings['max'] ?? '100'}}"/>
  </div>
</div>
<div class="form-group row">
  <div class="col-xs-3">
    <label class="control-label">Yeşil Başlangıç</label>
  </div>
  <div class="col-xs-3"> <input type="number" name="setting[greenFrom]" class="form-control"  value="{{$settings['greenFrom'] ?? '70'}}"/>
  </div>
  <div class="col-xs-3">
    <label class="control-label">Yeşil Bitiş</label>
  </div>
  <div class="col-xs-3"> <input type="number" name="setting[greenTo]" class="form-control"  value="{{$settings['greenTo'] ?? '80'}}"/>
  </div>
</div>
<div class="form-group row">
  <div class="col-xs-3">
    <label class="control-label">Sarı Başlangıç</label>
  </div>
  <div class="col-xs-3"> <input type="number" name="setting[yellowFrom]" class="form-control"  value="{{$settings['yellowFrom'] ?? '80'}}"/>
  </div>
  <div class="col-xs-3">
    <label class="control-label">Sarı Bitiş</label>
  </div>
  <div class="col-xs-3"> <input type="number" name="setting[yellowTo]" class="form-control"  value="{{$settings['yellowTo'] ?? '90'}}"/>
  </div>
</div>
<div class="form-group row">
  <div class="col-xs-3">
    <label class="control-label">Kırmızı Başlangıç</label>
  </div>
  <div class="col-xs-3"> <input type="number" name="setting[redFrom]" class="form-control"  value="{{$settings['redFrom'] ?? '90'}}"/>
  </div>
  <div class="col-xs-3">
    <label class="control-label">Kırmızı Bitiş</label>
  </div>
  <div class="col-xs-3"> <input type="number" name="setting[redTo]" class="form-control"  value="{{$settings['redTo'] ?? '100'}}"/>
  </div>
</div>
