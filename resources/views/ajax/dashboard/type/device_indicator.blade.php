  <div class="form-group row">
  @include('ajax.dashboard.type.element.main',['slug'=>'device_indicator', 'ek' => 1])


  <div class="col-xs-3">
    <label class="control-label">Boyut</label>
    @php
      if(isset($settings['size'])){
        $size = $settings['size'];
      }else{
          $size = "64px";
      }
      if(isset($settings['float'])){
        $float = $settings['float'];
      }else{
          $float = "12";
      }
    @endphp
    <select class="form-control select2" name="setting[size]">
        <option value="16px" {{$size == "16px" ? 'selected' : ''}}>16</option>
        <option value="32px" {{$size == "32px" ? 'selected' : ''}}>32</option>
        <option value="64px" {{$size == "64px" ? 'selected' : ''}}>64</option>
        <option value="128px" {{$size == "128px" ? 'selected' : ''}}>128</option>
        <option value="256px" {{$size == "256px" ? 'selected' : ''}}>256</option>
    </select>
  </div>

</div>
<div class="form-group row">
  <div class="col-xs-3">
    <label class="control-label">Renk</label>
  </div>
  <div class="col-xs-3">
    <input type="color" name="setting[indicator][0][color]" class="form-control" value="{{$settings['indicator'][0]['color'] ?? '#000000'}}" />
  </div>
  <div class="col-xs-3">
    <label class="control-label">Başlangıç</label>
  </div>
  <div class="col-xs-3">
    <input type="number" name="setting[indicator][0][start]" class="form-control" value="{{$settings['indicator'][0]['start'] ?? '0'}}" />
  </div>
</div>
<div class="form-group row">
  <div class="col-xs-3">
    <label class="control-label">Renk</label>
  </div>
  <div class="col-xs-3">
    <input type="color" name="setting[indicator][1][color]" class="form-control" value="{{$settings['indicator'][1]['color'] ?? '#00ff00'}}" />
  </div>
  <div class="col-xs-3">
    <label class="control-label">Başlangıç</label>
  </div>
  <div class="col-xs-3">
    <input type="number" name="setting[indicator][1][start]" class="form-control" value="{{$settings['indicator'][1]['start'] ?? '1'}}" />
  </div>
</div>
<div class="form-group row">
  <div class="col-xs-3">
    <label class="control-label">Renk</label>
  </div>
  <div class="col-xs-3">
    <input type="color" name="setting[indicator][2][color]" class="form-control" value="{{$settings['indicator'][2]['color'] ?? '#ff0000'}}" />
  </div>
  <div class="col-xs-3">
    <label class="control-label">Başlangıç</label>
  </div>
  <div class="col-xs-3">
    <input type="number" name="setting[indicator][2][start]" class="form-control" value="{{$settings['indicator'][2]['start'] ?? '2'}}" />
  </div>
</div>
