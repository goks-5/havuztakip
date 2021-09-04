<div class="form-group row">
  <div class="col-md-6">
    <label class="control-label">Cihaz</label>
    <select class="form-control select2" id="device">
      @php
      $inarray = array();
      $inarray2 = array();
      if(isset($settings['devices'])){
      foreach ($settings['devices'] as $device) {
      $inarray[] = $device['device'] ."_". $device['device_index'];
      $inarray2[] = $device['device'] ;
      }
      }
      if(isset($settings['device'])){
      $inarray[] = $settings['device'] ."_". $settings['device_index'];
      $inarray2[] = $settings['device'] ;

      }
      @endphp

      @foreach ($devices as $device)
      @php
      $tags = json_decode($device->tags,true);
      @endphp
      <option value='{{$device->id}}' @if(in_array($device->id, $inarray)) selected @endif> {{$device->name}}
        @if(count($companys) > 1 ) {{$companys[$device->company_id]}}
        @endif</option>
        @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label class="control-label">Etiket</label>
    <select  {!! $multiple > 1 ?  'class="form-control selectpicker" name="setting[devices][]" multiple data-max-options="4"' : 'class="form-control select2" name="setting[device]"'!!}>

      @foreach ($devices as $device)
      @php
      $tags = json_decode($device->tags,true);
      @endphp
      @foreach ($tags as $key => $tag)
      <option data-device='{{$device->id}}' value='{"device":{{$device->id}},"device_index":{{$key}}}' @if(in_array($device->id ."_".$key, $inarray)) selected @endif>{{$tag}}</option>
      @endforeach
      @endforeach
    </select>
  </div>
</div>
<div class="form-group row">
  <div class="col-xs-3">
    <label class="control-label">Başlık</label>
    <input type="text" name="setting[title]" class="form-control" value="{{$settings['title'] ?? ''}}" />
  </div>
  <div class="col-xs-3">
    <label class="control-label">Yazı Rengi</label>
    <select class="form-control select2" name="setting[css][color]">
      <option value="#000000" style="background-color: Black;color: #FFFFFF;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#000000' ? 'selected':''}}>Siyah</option>
      <option value="#808080" style="background-color: Gray;color: #FFFFFF;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#808080' ? 'selected':''}}>Gri</option>
      <option value="#A9A9A9" style="background-color: DarkGray;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#A9A9A9' ? 'selected':''}}>Koyu Gri</option>
      <option value="#D3D3D3" style="background-color: LightGrey;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#D3D3D3' ? 'selected':''}}>Açık Gri</option>
      <option value="#FFFFFF" style="background-color: White;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#FFFFFF' ? 'selected':''}}>Beyaz</option>
      <option value="#0000FF" style="background-color: Blue;color: #FFFFFF;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#0000FF' ? 'selected':''}}>Mavi</option>
      <option value="#800080" style="background-color: Purple;color: #FFFFFF;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#800080' ? 'selected':''}}>Mor</option>
      <option value="#FF1493" style="background-color: DeepPink;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#FF1493' ? 'selected':''}}>Pembe</option>
      <option value="#006400" style="background-color: DarkGreen;color: #FFFFFF;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#006400' ? 'selected':''}}>Koyu Yeşil</option>
      <option value="#008000" style="background-color: Green;color: #FFFFFF;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#008000' ? 'selected':''}}>Yeşil</option>
      <option value="#9ACD32" style="background-color: YellowGreen;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#9ACD32' ? 'selected':''}}>Açık Yeşil</option>
      <option value="#FFFF00" style="background-color: Yellow;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#FFFF00' ? 'selected':''}}>Sarı</option>
      <option value="#FFA500" style="background-color: Orange;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#FFA500' ? 'selected':''}}>Turuncu</option>
      <option value="#FF0000" style="background-color: Red;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#FF0000' ? 'selected':''}}>Kırmızı</option>
      <option value="#A52A2A" style="background-color: Brown;color: #FFFFFF;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#A52A2A' ? 'selected':''}}>Kahverengi</option>
      <option value="#DEB887" style="background-color: BurlyWood;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#DEB887' ? 'selected':''}}>Açık Kahvve</option>
      <option value="#F5F5DC" style="background-color: Beige;"
      {{isset($settings['css']['color']) && $settings['css']['color'] == '#F5F5DC' ? 'selected':''}}>Krem</option>
    </select>
  </div>
  <div class="col-xs-3">
      <label class="control-label">Arka Plan Rengi</label>
      <select class="form-control select2" name="setting[css][background]">
        <option value="rgba(0, 0, 0, 0)" style="background-color:rgba(0, 0, 0, 0);"
        {{isset($settings['css']['background']) && $settings['css']['background'] == 'rgba(0, 0, 0, 0)' ? 'selected':''}}>Şeffaf</option>
        <option value="#000000" style="background-color: Black;color: #FFFFFF;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#000000' ? 'selected':''}}>Siyah</option>
        <option value="#808080" style="background-color: Gray;color: #FFFFFF;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#808080' ? 'selected':''}}>Gri</option>
        <option value="#A9A9A9" style="background-color: DarkGray;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#A9A9A9' ? 'selected':''}}>Koyu Gri</option>
        <option value="#D3D3D3" style="background-color: LightGrey;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#D3D3D3' ? 'selected':''}}>Açık Gri</option>
        <option value="#FFFFFF" style="background-color: White;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#FFFFFF' ? 'selected':''}}>Beyaz</option>
        <option value="#0000FF" style="background-color: Blue;color: #FFFFFF;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#0000FF' ? 'selected':''}}>Mavi</option>
        <option value="#800080" style="background-color: Purple;color: #FFFFFF;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#800080' ? 'selected':''}}>Mor</option>
        <option value="#FF1493" style="background-color: DeepPink;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#FF1493' ? 'selected':''}}>Pembe</option>
        <option value="#006400" style="background-color: DarkGreen;color: #FFFFFF;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#006400' ? 'selected':''}}>Koyu Yeşil</option>
        <option value="#008000" style="background-color: Green;color: #FFFFFF;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#008000' ? 'selected':''}}>Yeşil</option>
        <option value="#9ACD32" style="background-color: YellowGreen;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#9ACD32' ? 'selected':''}}>Açık Yeşil</option>
        <option value="#FFFF00" style="background-color: Yellow;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#FFFF00' ? 'selected':''}}>Sarı</option>
        <option value="#FFA500" style="background-color: Orange;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#FFA500' ? 'selected':''}}>Turuncu</option>
        <option value="#FF0000" style="background-color: Red;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#FF0000' ? 'selected':''}}>Kırmızı</option>
        <option value="#A52A2A" style="background-color: Brown;color: #FFFFFF;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#A52A2A' ? 'selected':''}}>Kahverengi</option>
        <option value="#DEB887" style="background-color: BurlyWood;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#DEB887' ? 'selected':''}}>Açık Kahvve</option>
        <option value="#F5F5DC" style="background-color: Beige;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#F5F5DC' ? 'selected':''}}>Krem</option>
      </select>
    </div>
    <div class="col-xs-3">
      <label class="control-label">Sıra</label>
      <input type="number" name="order" class="form-control" value="{{$order ?? '999'}}" />
    </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
  $('[data-device]').hide();
  $('[data-device="' +   $('#device').val() + '"]').show();
  $('#device').change(function() {
    $('[data-device]').hide();
    $('[data-device="' + $(this).val() + '"]').show();
  });

  });
</script>
