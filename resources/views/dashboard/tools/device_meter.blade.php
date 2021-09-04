<div class="tool_data row" style=" overflow: hidden;">
@include('dashboard.tools.toolSettings',['tool'=>$tool])

@if (isset($settings['title']) && !empty($settings['title']))
<div class="col-xs-12 text-center">{{$settings['title']}} </div>
@endif
<span id="span_{{$tool->id}}" class="col-xs-12 text-center font-weight-bold"></span>
<meter id="meter_{{$tool->id}}"  min="{{$settings['min']}}" max="{{$settings['max']}}"
 low="{{$settings['low']}}" high="{{$settings['high']}}"
 optimum="{{round(($settings['low']+$settings['high'])/2)}}" value="{{round(($settings['low']+$settings['high'])/2)}}" >
</meter>

</div>

@php
  $diff = $settings['max'] -$settings['min'];
  $low = round((100 * $settings['low'])  / $diff);
  $high = round((100 * $settings['high'])  / $diff);

    $width = $settings['width'] ?? 300;
    $height = $settings['height'] ?? 20;

@endphp
<style>
meter::-webkit-meter-bar {
    height:  155%;
}
#meter_{{$tool->id}}{
  -webkit-transform: rotate({{$settings['rotate'] ?? 0}}deg);
  -moz-transform: rotate({{$settings['rotate'] ?? 0}}deg);
  -o-transform: rotate({{$settings['rotate'] ?? 0}}deg);
  transform: rotate({{$settings['rotate'] ?? 0}}deg);
  margin-left: 15px;
  @if($settings['rotate'] == 180)
  transform-origin: {{$width * 0.5}}px {{$height * 0.7 }}px;
  @endif
  @if($settings['rotate'] == 90)
      transform-origin: left top;
      margin-left:  {{15 + $width / 2 }}px;
  @endif
  @if( $settings['rotate'] == 270)
      transform-origin: 100% 0%;
      margin-left:  {{15 - $width }}px;
  @endif
  width: {{$width}}px;
  height: {{$height}}px;

  {{--
  background-image: linear-gradient(
    90deg,
    rgba(255,0,0,0) {{$low -2 }}%,

    #FFDB1A {{$low - 2}}%,
    #FFDB1A {{$low}}%,

    #86CC00 {{$low}}%,
    #86CC00 {{$low +2}}%,


    rgba(255,0,0,0) {{$low +2}}%,
    rgba(255,0,0,0) {{$high -2}}%,

    #86CC00 {{$high -2}}%,
    #86CC00 {{$high}}%,

    #CC4600 {{$high}}%,
    #CC4600 {{$high + 2}}%,

    rgba(255,0,0,0) {{$high + 2 }}%




  );
  --}}
}


</style>

@push('javascript')
@endpush
