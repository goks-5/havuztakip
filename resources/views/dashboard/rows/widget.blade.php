@php
$tools = isset($row->tools[$index]) ? $row->tools[$index] : [];
$title = $row->{"title_".$index};
$color = $row->{"color_".$index};
@endphp

<div class="widget{{ empty($tools) ? " hide_tool":""}}" style="background-color:{{$color}}">
  @include('dashboard.tools.cellSettings',['row'=>$row->id,'index'=>$index])
    @if (!empty($title))
    <div class="widget-header"><span>{{$title}}</span></div>
    @endif
    <div class="widget-tools">
        @if (!empty($tools))
          @php
          $rowin = false;
          @endphp
            @foreach ($tools as $tool)
            @php
              $settings =  array();
              $settings = json_decode($tool->settings,true);
              $rowstart = false;
              $rowend = false;
              if(isset($settings['float']) && !$rowin){
                $rowin = true;
                $rowstart = true;
              }
              if(!isset($settings['float']) && $rowin){
                $rowin = false;
                $rowend = true;
              }
            @endphp
              @if($rowstart) <div class="row"> @endif
              @if($rowend) </div>  @endif
              @include('dashboard.tools.'.$tool->type,['tool'=>$tool,'settings'=>$settings])
            @endforeach
                @if($rowin) </div> @endif
        @endif
    </div>
</div>
