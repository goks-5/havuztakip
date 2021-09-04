
<div class="tool_data row">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])
  @if (!empty($settings['title']))
  <div class="col-xs-5 tool_data_title" style="
  @foreach ($settings['css'] as $key => $value){{$key}}:{{$value}};@endforeach">{{$settings['title']}} </div>
  @endif
  <div class="@if (!empty($settings['title'])) col-xs-7 @else col-xs-12 @endif tool_data_data" style="
  @foreach ($settings['css'] as $key => $value){{$key}}:{{$value}};@endforeach"><span id="tool_{{$tool->id}}">...</span> {{ $settings['unit']  }}</div>
</div>
