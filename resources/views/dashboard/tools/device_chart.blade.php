
<div class="tool_data row ">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])
  @php
    $setting = json_decode($tool->settings,true);
  @endphp
  @if (isset($settings['title']) && !empty($settings['title']))
  <div class="col-xs-12 text-center">{{$settings['title']}} </div>
  @endif
  <div class="col-xs-12" id="tool_{{$tool->id}}" data-type="{{$setting['type'] ?? 'line'}}" style="height: calc(100% - 6px); overflow: hidden;"></div>
  <a href="#" class="btn btn-primary btn-sm edit"  onclick="printDiv('tool_{{$tool->id}}')">
    <i class="voyager-print"></i> Yazdir
    </a>
</div>
