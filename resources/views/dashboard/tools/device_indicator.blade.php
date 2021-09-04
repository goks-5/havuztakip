<div class="tool_data @if(isset($settings['float'])) col-xs-{{$settings['float']}} @else row @endif" style="margin:0">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])
  <div class="row tool_indicator"  id="indicator_out_{{$tool->id}}" style="width:{{$settings['size']}};">
  <div class="indicator" id="indicator_in_{{$tool->id}}" style="width:{{$settings['size']}};height:{{$settings['size']}};">
  </div>
</div>
<div class="indicator_title" >&nbsp;{{$settings['title']}}&nbsp;</div>
</div>
