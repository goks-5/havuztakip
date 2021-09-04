<div class="form-group row">
  @include('ajax.dashboard.type.element.main',['slug'=>'device_alarm' , 'ek' => 1 ])
</div>
<div class="form-group row">
  <div class="col-xs-3">
    <label class="control-label">Boyut</label>
    @php
      if(isset($settings['size'])){
        $size = $settings['size'];
      }else{
          $size = "64px";
      }
      if(isset($settings['sound'])){
        $sound = $settings['sound'];
      }else{
          $sound = "0";
      }
      if(isset($settings['opt'])){
            $opt = $settings['opt'];
          }else{
              $opt = ">=";
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



  <div class="col-xs-3">
    <label class="control-label" >Ses Çal</label>
  <input type='hidden' value='0' name='setting[sound]'>
    <input type="checkbox" name="setting[sound]" class="form-check-input" style="display:block" value="1" id='sound' {{$sound == "1" ? 'checked' : ''}}/>
    </div>
  <div class="col-xs-3">
      <label class="control-label">Koşul</label>
    <select class="form-control select2" name="setting[opt]">
        <option value=">=" {{$opt == ">=" ? 'selected' : ''}}>Büyük Eşittir</option>
        <option value="<=" {{$opt == "<=" ? 'selected' : ''}}>Küçük Eşittir</option>
    </select>
  </div>

  <div class="col-xs-3">
      <label class="control-label">Seviye</label>
    <input type="number" name="setting[start]" class="form-control" value="{{$settings['start'] ?? '0'}}" />
  </div>
</div>
