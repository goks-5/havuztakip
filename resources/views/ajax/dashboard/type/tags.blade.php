
<h4>Etiketli Tüketimi Ekle</h4>
  @include('ajax.dashboard.type.element.main',['slug'=>'tags', 'ek' => 0])
  <br>
  <br>
  <br>
<div class="form-group row" style='display:none'>
  <div class="col-xs-2"></div>
  <div class="col-xs-4">
    <label class="control-label" for="name">Son Kaç Etiket Gösterilsin</label>
  </div>
  <div class="col-xs-4">

    <select class="form-control select2" name="setting[count]">
      <option value="1" {{isset($settings['count']) && $settings['count'] == "1" ? 'selected':''}}>1</option>
      <option value="2" {{isset($settings['count']) && $settings['count'] == "2" ? 'selected':''}}>2</option>
      <option value="3" {{isset($settings['count']) && $settings['count'] == "3" ? 'selected':''}}>3</option>
      <option value="4" {{isset($settings['count']) && $settings['count'] == "4" ? 'selected':''}}>4</option>
      <option value="5" {{isset($settings['count']) && $settings['count'] == "5" ? 'selected':''}}>5</option>
      <option value="6" {{isset($settings['count']) && $settings['count'] == "6" ? 'selected':''}}>6</option>
      <option value="7" {{isset($settings['count']) && $settings['count'] == "7" ? 'selected':''}}>7</option>
      <option value="8" {{isset($settings['count']) && $settings['count'] == "8" ? 'selected':''}}>8</option>
      <option value="9" {{isset($settings['count']) && $settings['count'] == "9" ? 'selected':''}}>9</option>
      <option value="10" {{isset($settings['count']) && $settings['count'] == "10" ? 'selected':''}} selected>10</option>

    </select>
  </div>

  <div class="col-xs-2"></div>
</div>
