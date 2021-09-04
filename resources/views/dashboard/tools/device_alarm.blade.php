<div class="tool_data row" >
  @include('dashboard.tools.toolSettings',['tool'=>$tool])
  <div class="row tool_indicator"  id="indicator_out_{{$tool->id}}" style="width:{{$settings['size']}};">
  <div class="devicealarm" id="indicator_in_{{$tool->id}}" style="width:{{$settings['size']}};height:{{$settings['size']}};">
  </div>
</div>
<div class="indicator_title" >&nbsp;{{$settings['title']??''}}&nbsp;</div>

<audio id="audio_{{$tool->id}}" autoplay="autoplay" preload="auto" loop="loop">
    <source src="/storage/widget/siren1.mp3" type="audio/mpeg" />
    <embed id="audio_ie8_{{$tool->id}}" hidden="true" autostart="true" loop="true" src="/storage/widget/siren1.mp3" />
</audio>
</div>
