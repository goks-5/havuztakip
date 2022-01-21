@if($options->device == 1 || $options->device == 2 || $options->device == 3 )
<h5>Veri kaynağı</h5>
@endif
@if($options->device == 1)
  <div class="col-md-6">
    <label class="control-label">Cihaz</label>
    <select class="form-control select2" id="device">
      @foreach ($devices as $device)
       @if (count($filter) == 0 || in_array($device->mac,$filter))
      <option value='{{$device->id}}' @if(isset($settings['device']) && $device->id == $settings['device'] ) selected @endif>
        {{$device->name}}
        </option>
         @endif
      @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label class="control-label">Etiket</label>
    <select class="form-control select2" name="setting[device]" id="deviceTags">

    </select>
  </div>
  <script type="text/javascript">
    $(document).ready(function() {

      var devices = [];
      @foreach ($devices as $device)
      devices[{{$device->id}}] = {!! $device->tags !!} ;
      @endforeach

      $('#device').change(function() {
        device_id = {{$settings['device'] ?? 0 }};
        device_index = {{$settings['device_index'] ?? 0 }};
        selected = $(this).val();
        obj = devices[selected] ;
        $('#deviceTags').empty();
        Object.keys(obj).forEach(function(k){
          if(device_id == selected && device_index == k){
            $('#deviceTags').append("<option value='{\"device\":" + selected + ",\"device_index\":" + k + "}' selected>" + obj[k] + "</option>");
          }else{
            $('#deviceTags').append("<option value='{\"device\":" + selected + ",\"device_index\":" + k + "}'>" + obj[k] + "</option>");
          }
          });
      });
      $('#device').trigger("change");
    });

  </script>

  @endif

  @if($options->device == 2)

    <div class="col-md-6">
      <label class="control-label">Cihaz</label>
      <select class="form-control select2" id="device">
        @foreach ($devices as $device)

        <option value='{{$device->id}}' @if(isset($settings['device']) && $device->id == $settings['device'] ) selected @endif>
          {{$device->name}}
          </option>
          @endforeach
      </select>
    </div>
    <div class="col-md-6">
      <label class="control-label">Etiket</label>
      <select class="form-control select2"  id="deviceTags">

      </select>
    </div>
    <div class=col-md-12>
      <select name="setting[devices][]" multiple id="multiTag" >
      </select>
    </div>

    <script type="text/javascript">
      $(document).ready(function() {
        $('#multiTag').select2({width:"100%"});
        var devices = [];
        @foreach ($devices as $device)
        devices[{{$device->id}}] = {!! $device->tags !!} ;
        @endforeach

        $('#device').change(function() {
          selected = $(this).val();
          obj = devices[selected] ;
          $('#deviceTags').empty();
            $('#deviceTags').append("<option></option>");
          Object.keys(obj).forEach(function(k){
              $('#deviceTags').append("<option value='" + k + "'>" + obj[k] + "</option>");
          });
        });
        $('#device').trigger("change");
        $('#deviceTags').change(function() {
          selected = $('#device').val();
          obj = devices[selected] ;
          k = $(this).val();
          var newOption = new Option(obj[k] , "{\"device\":" + selected + ",\"device_index\":" + k + "}", false, true);
          $("#multiTag option[value='{\"device\":" + selected + ",\"device_index\":" + k + "}']").remove();
          $('#multiTag').append(newOption).trigger('change');

      });
      @if(isset($settings['devices']))
        @foreach ($settings['devices'] as $device)
          selected = {{$device['device']}};
          obj = devices[selected] ;
          k = {{$device['device_index']}};
          newOption = new Option(obj[k] , "{\"device\":" + selected + ",\"device_index\":" + k + "}", false, true);
          $('#multiTag').append(newOption).trigger('change');

      @endforeach
      @endif

      });
    </script>
    <style>
    span.select2-results{
      display: none!important;
    }
    </style>
    @endif
    @if($options->device == 1)
  <div class="col-md-12">
    <label class="control-label">Cihaz</label>
    <select class="form-control select2" id="device">
      @foreach ($devices as $device)
       @if (count($filter) == 0 || in_array($device->mac,$filter))
      <option value='{{$device->id}}' @if(isset($settings['device']) && $device->id == $settings['device'] ) selected @endif>
        {{$device->name}}
        </option>
         @endif
      @endforeach
    </select>
  </div>
  @endif