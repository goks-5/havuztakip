<div class="form-group row">
  <div class="col-xs-4">
    <label class="control-label">Cihaz</label>
    <select class="form-control select2" id="device">
      @foreach ($devices as $device)
      @php
      $tags = json_decode($device->tags,true);
      @endphp
      <option value='{{$device->id}}'> {{$device->name}} @if(count($companys) > 1 ) {{$companys[$device->company_id]}} @endif</option>
        @endforeach
    </select>
  </div>
  <div class="col-xs-4">
    <label class="control-label">Etiket</label>
    <select class="form-control select2" name="setting[device]">
      @foreach ($devices as $device)
      @php
      $tags = json_decode($device->tags,true);
      @endphp
      @foreach ($tags as $key => $tag)
      <option data-device='{{$device->id}}' value='{"device":{{$device->id}},"device_index":{{$key}}}' {{isset($settings['device']) && $device->id == $settings['device'] && $key == $settings['device_index'] ? 'selected':''}}>{{$tag}}</option>
      @endforeach
      @endforeach
    </select>
  </div>
</div>
