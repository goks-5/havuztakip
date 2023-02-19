<div class="tool_data row">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])

@dump($settings)


    <div id="tool_{{$tool->id}}"></div>

    @can('browse',app('App\Fault'))

      @endcan
        @can('add',app('App\Fault'))

    @endcan

</div>
