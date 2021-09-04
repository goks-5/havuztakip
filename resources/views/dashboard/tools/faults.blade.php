<div class="tool_data row">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])

    <div id="tool_{{$tool->id}}"></div>
    @can('browse',app('App\Fault'))
      <div class="col-xs-6 tool_data_title" style="text-align: left;">
        <a class="btn btn-primary btn-sm" target="_self" href="https://enerjiyonetim.com/arizalar/"><span class="icon voyager-archive"></span> <span class="title">Tüm İş Emirleri</span></a>
      </div>
      @endcan
        @can('add',app('App\Fault'))
    <div class="col-xs-6 tool_data_title" style="text-align: right;">
      <a class="btn btn-primary btn-sm" target="_self" href="https://enerjiyonetim.com/arizalar/create"><span class="icon voyager-hammer"></span> <span class="title">İş Emri Bildir</span></a>
    </div>
    @endcan

</div>
