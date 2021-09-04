<div class="tool_data row">
@include('dashboard.tools.toolSettings',['tool'=>$tool])
<div class="col-xs-12" id="tool_{{$tool->id}}" style="width:100%;height:80%"></div>
@if (isset($settings['title']) && !empty($settings['title']))
<div class="col-xs-12 text-center">{{$settings['title']}} </div>
@endif
</div>
<span id="span_{{$tool->id}}"></span>


@push('javascript')
<script  type="text/javascript">

google.charts.setOnLoadCallback(drawChart_{{$tool->id}});
var chart = {tool_{{$tool->id}} : ""};
var datas = {tool_{{$tool->id}} : ""};
var options = {tool_{{$tool->id}} : ""};
      function drawChart_{{$tool->id}}() {
 chart.tool_{{$tool->id}} = new google.visualization.Gauge(document.getElementById('tool_{{$tool->id}}'));
  datas.tool_{{$tool->id}} = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['{{$settings['unit']}}', 0]
        ]);
options.tool_{{$tool->id}} = {
                max: {{$settings['max'] ?: "100"}}, min: {{$settings['min'] ?: "0"}},
                  redFrom: {{$settings['redFrom'] ?: "0"}}, redTo: {{$settings['redTo'] ?: "0"}},
                yellowFrom:{{$settings['yellowFrom'] ?: "0"}}, yellowTo: {{$settings['yellowTo'] ?: "0"}},
                greenFrom:{{$settings['greenFrom'] ?: "0"}}, greenTo: {{$settings['greenTo'] ?: "0"}},
                minorTicks: 5, animation:{duration:5000,easing:'inAndOut'}
              };
              chart.tool_{{$tool->id}}.draw(datas.tool_{{$tool->id}}, options.tool_{{$tool->id}});

}
</script>
@endpush
