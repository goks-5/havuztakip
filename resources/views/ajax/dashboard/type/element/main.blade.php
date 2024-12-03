@php
$options = \App\Tool::where(['slug' => $slug])->first();
$gCount = $options->title + $options->size +  $options->color + $options->background + $options->layer ;
$wsize =  floor(24 /$gCount);
@endphp

  @include('ajax.dashboard.type.element.device',['filter' => $filter ?? []])

  @if($gCount>1)
  <h5>Yazı ve Görünüm</h5>
  @endif



@if($options->title == 1)
  <div class="col-xs-{{ $wsize }}">
    <label class="control-label">Başlık</label>
    <input type="text" name="setting[title]" class="form-control" value="{{$settings['title'] ?? ''}}" />
  </div>
@endif

@if($options->size == 1)
<div class="col-xs-{{ $wsize }}">

  <label class="control-label" for="name">Font Boyutu</label>
  <select class="form-control select2" name="setting[css][font-size]">
    <option value="8px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "8px" ? 'selected':''}}>8</option>
      <option value="9px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "9px" ? 'selected':''}}>9</option>
    <option value="10px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "10px" ? 'selected':''}}>10</option>
    <option value="11px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "11px" ? 'selected':''}}>11</option>
    <option value="12px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "12px" ? 'selected':''}}>12</option>
    <option value="14px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "14px" ? 'selected':'selected'}}>14</option>
    <option value="16px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "16px" ? 'selected':''}}>16</option>
    <option value="18px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "18px" ? 'selected':''}}>18</option>
    <option value="20px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "20px" ? 'selected':''}}>20</option>
    <option value="22px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "22px" ? 'selected':''}}>22</option>
    <option value="24px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "24px" ? 'selected':''}}>24</option>
    <option value="26px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "26px" ? 'selected':''}}>26</option>
    <option value="28px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "28px" ? 'selected':''}}>28</option>
    <option value="36px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "36px" ? 'selected':''}}>36</option>
    <option value="48px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "48px" ? 'selected':''}}>48</option>
    <option value="72px" {{isset($settings['css']['font-size']) && $settings['css']['font-size'] == "72px" ? 'selected':''}}>72</option>

  </select>
</div>
@endif
@if($options->color == 1)
  <div class="col-xs-{{ $wsize }}">
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
@endif
@if($options->background == 1)
  <div class="col-xs-{{ $wsize }}">
      <label class="control-label">Arka Plan Rengi</label>
      <select class="form-control select2" name="setting[css][background]">
        <option value="#FFFFFF" style="background-color: White;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#FFFFFF' ? 'selected':''}}>Beyaz</option>
        <option value="rgba(0, 0, 0, 0);border-color: rgba(0,0,0,0)" style="background-color:rgba(0, 0, 0, 0);"
        {{isset($settings['css']['background']) && $settings['css']['background'] == 'rgba(0, 0, 0, 0);border-color: rgba(0,0,0,0)' ? 'selected':''}}>Şeffaf</option>
        <option value="#000000" style="background-color: Black;color: #FFFFFF;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#000000' ? 'selected':''}}>Siyah</option>
        <option value="#808080" style="background-color: Gray;color: #FFFFFF;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#808080' ? 'selected':''}}>Gri</option>
        <option value="#A9A9A9" style="background-color: DarkGray;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#A9A9A9' ? 'selected':''}}>Koyu Gri</option>
        <option value="#D3D3D3" style="background-color: LightGrey;"
        {{isset($settings['css']['background']) && $settings['css']['background'] == '#D3D3D3' ? 'selected':''}}>Açık Gri</option>
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
  @endif
  @if($options->layer == 1)
    <div class="col-xs-{{ $wsize }}">
      <label class="control-label">Katman</label>
      <input type="number" name="order" class="form-control" value="{{$order ?? '999'}}" />
    </div>
  @endif

  <div class="col-xs-{{ $wsize }}">
    <label class="control-label">Hizalama</label>
    <select class="form-control" name="setting[text_align]" id="text_align">
        <option value="left" {{ (isset($settings['text_align']) && $settings['text_align'] == 'left') ? 'selected' : '' }}>Sol</option>
        <option value="center" {{ (isset($settings['text_align']) && $settings['text_align'] == 'center') ? 'selected' : '' }}>Merkez</option>
        <option value="right" {{ (isset($settings['text_align']) && $settings['text_align'] == 'right') ? 'selected' : '' }}>Sağ</option>
    </select>
</div>

<div class="col-xs-{{ $wsize }}">
        <label class="control-label">Yuvarlama</label>
        <select class="form-control select2" name="setting[numbers_round]">
            <option value="1" {{ isset($settings['numbers_round']) && $settings['numbers_round'] == "1" ? 'selected' : '' }}>Seçili Değil</option>
            <option value="2" {{ isset($settings['numbers_round']) && $settings['numbers_round'] == "2" ? 'selected' : '' }}>Tam Sayı Göster</option>
            <option value="3" {{ isset($settings['numbers_round']) && $settings['numbers_round'] == "3" ? 'selected' : '' }}>Tam Sayıya Yuvarla</option>
        </select>
    </div>

<div class="clearfix"></div> <!-- Boşluk bırakmak için clearfix ekledik -->

<h5>Boyut</h5>
    <div class="col-xs-6">
        <label class="control-label">Genişlik (px)</label>
        <input type="text" name="setting[style][width]" class="form-control" value="{{ $settings['style']['width'] ?? '' }}" placeholder="" />
    </div>
    <div class="col-xs-6">
        <label class="control-label">Yükseklik (px)</label>
        <input type="text" name="setting[style][height]" class="form-control" value="{{ $settings['style']['height'] ?? '' }}" placeholder="" />
    </div>
    <div class="clearfix"></div> <!-- Boşluk bırakmak için clearfix ekledik -->

    <h5>Yerleşim</h5>
    <div class="col-xs-6">
        <label class="control-label">Soldan Boşluk (px)</label>
        <input type="text" name="setting[style][left]" class="form-control" value="{{ $settings['style']['left'] ?? '' }}" placeholder="" />
    </div>
    <div class="col-xs-6">
        <label class="control-label">Üstten Boşluk (px)</label>
        <input type="text" name="setting[style][top]" class="form-control" value="{{ $settings['style']['top'] ?? '' }}" placeholder="" />
    </div>

  @if($options->unit == 1 || $ek  > 0)
  <h5 class="col-xs-12">Diğer özellikler</h5>
  @endif
@if($options->unit == 1)
<div class="col-xs-{{ $wsize }}">
<label class="control-label">Veri Birimi</label>
<input type="text" name="setting[unit]" class="form-control" value="{{$settings['unit'] ?? ''}}"/>
</div>
@endif