@if($options->device == 1 || $options->device == 2 || $options->device == 3 )
<h5>Veri kaynağı</h5>
@endif
@if($options->device == 1)
    <div class="col-md-6">
    <label for="devices" class="form-label">Cihaz</label>
    <input class="form-control" list="devices" id="device" placeholder="Cihaz ara">    
    <datalist  id="devices">
    @foreach ($devices as $device)
          @if (count($filter) == 0 || in_array($device->mac, $filter))
            <!-- ID yerine cihaz ismini value olarak kullanıyoruz -->
            <option value="{{$device->name}}" @if(isset($settings['device']) && $device->id == $sett>
              {{$device->name}}
            </option>
          @endif
        @endforeach
        </datalist>
      </div>
    <div class="col-md-6">
      <label for="deviceTags" class="form-label">Etiket</label>
      <input class="form-control" list="deviceTags" id="deviceTagInput" placeholder="Etiket ara">
      <datalist id="deviceTags">
        <!-- Etiketler JavaScript ile dinamik olarak eklenecek -->
      </datalist>
    </div>

    <script type="text/javascript">
    $(document).ready(function() {
      var devices = [];

      // Cihaz isimlerine göre etiketleri depoluyoruz
      @foreach ($devices as $device)
        devices["{{$device->name}}"] = {!! $device->tags !!};  // Cihaz adını kullanıyoruz
      @endforeach

      $('#device').change(function() {
        var selectedDeviceName = $(this).val();  // Seçilen cihaz adı
        var obj = devices[selectedDeviceName];    // Cihaz adına göre etiketleri çekiyoruz

        if (!obj) {
          $('#deviceTags').empty();
          return;
        }

        console.log("Seçilen cihaz: " + selectedDeviceName);  // Test için cihaz adını konsola yazıy>
        console.log("Etiketler: ", obj);  // Test için etiketleri konsola yazıyoruz

        $('#deviceTags').empty();  // Eski etiketleri temizliyoruz

        // Nesnenin anahtarları üzerinden döngü
        Object.keys(obj).forEach(function(key) {
          var tag = obj[key];  // Her bir etiketi alıyoruz
          $('#deviceTags').append("<option value='" + tag + "'>" + tag + "</option>");  // Etiketler>
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
    @if($options->device == 3)
  <div class="col-md-12">
    <label class="control-label">Cihaz</label>
    <select class="form-control select2" id="device" name="setting[device]">
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