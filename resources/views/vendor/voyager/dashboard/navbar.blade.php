<nav class="navbar navbar-default navbar-fixed-top navbar-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button class="hamburger btn-link">
                <span class="hamburger-inner"></span>
            </button>
            @section('breadcrumbs')
            <ol class="breadcrumb hidden-xs">
                @php
                $segments = array_filter(explode('/', str_replace(route('voyager.dashboard'), '', Request::url())));
                $url = route('voyager.dashboard');
                @endphp
                @if(count($segments) == 0)
                    <li class="active"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</li>
                @else
                    <li class="active">
                        <a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}</a>
                    </li>
                    @foreach ($segments as $segment)
                        @php
                        $url .= '/'.$segment;
                        @endphp
                        @if ($loop->last)
                            <li>{{ ucfirst(urldecode($segment)) }}</li>
                        @else
                            <li>
                                <a href="{{ $url }}">{{ ucfirst(urldecode($segment)) }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ol>
            @show
          <ul class="nav navbar-nav navbar-left">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle text-left" data-toggle="dropdown" role="button" aria-expanded="false">
                    <i class="voyager-info-circled" id="device_info"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-animated">
                    <li><span class="ustcihaz">##</span> Cihaz <span id="ustnokta">##</span> Nokta</li>
                    <li><span class="ustcihaz">##</span> Cihazdan <span id="cevrimdisi">##</span> Çevrim Dışı</li>
                    <li id="cevrimdisilar"></li>
                    <li role="separator" class="divider"></li>
                    <li><span class="ustcihaz">##</span> Cihazdan <span id="pasif">##</span> Pasif</li>
                    <li id="pasifler"></li>
                </ul>
            </li>
        </ul>
        </div>

        <ul class="nav navbar-nav @if (__('voyager::generic.is_rtl') == 'true') navbar-left @else navbar-right @endif">

              <li class="dropdown profile">
                <a href="#" class="dropdown-toggle text-right" data-toggle="dropdown" role="button"
                   aria-expanded="false"><img src="{{ $user_avatar }}" class="profile-img"> <span
                            class="caret"></span></a>
                <ul class="dropdown-menu dropdown-menu-animated">
                    <li class="profile-img">
                        <img src="{{ $user_avatar }}" class="profile-img">
                        <div class="profile-body">
                            <h5>{{ Auth::user()->name }}</h5>
                            <h6>{{ Auth::user()->email }}</h6>
                        </div>
                    </li>
                    <li class="divider"></li>
                    <?php $nav_items = config('voyager.dashboard.navbar_items'); ?>
                    @if(is_array($nav_items) && !empty($nav_items))
                    @foreach($nav_items as $name => $item)
                    <li {!! isset($item['classes']) && !empty($item['classes']) ? 'class="'.$item['classes'].'"' : '' !!}>
                        @if(isset($item['route']) && $item['route'] == 'voyager.logout')
                        <form action="{{ route('voyager.logout') }}" method="POST">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-block">
                                @if(isset($item['icon_class']) && !empty($item['icon_class']))
                                <i class="{!! $item['icon_class'] !!}"></i>
                                @endif
                                {{__($name)}}
                            </button>
                        </form>
                        @else
                        <a href="{{ isset($item['route']) && Route::has($item['route']) ? route($item['route']) : (isset($item['route']) ? $item['route'] : '#') }}" {!! isset($item['target_blank']) && $item['target_blank'] ? 'target="_blank"' : '' !!}>
                            @if(isset($item['icon_class']) && !empty($item['icon_class']))
                            <i class="{!! $item['icon_class'] !!}"></i>
                            @endif
                            {{__($name)}}
                        </a>
                        @endif
                    </li>
                    @endforeach
                    @endif
                </ul>
            </li>


        </ul>
    </div>
</nav>

@push('javascript')
  <script  type="text/javascript">
    $(document).ready(function() {
  function deviceCheck() {
    $.ajax({
      url: '{{route('deviceonline')}}',
      success: function(data) {
        var lastdata = data;
        $('.ustcihaz').html(lastdata.deviceCount);
        $('#ustnokta').html(lastdata.pointCount);
        $('#cevrimdisi').html(lastdata.oflineCount);
        if(lastdata.ofline){
          $('#device_info').css('color','#ff0000');
        }else{
            $('#device_info').css('color','#00FF00');

            $('#cevrimdisilar').html('');
        }
var $yaz = " ";
if(lastdata.oflineDevices !== undefined && lastdata.oflineDevices.length !== 'undefined'){
  lastdata.oflineDevices.forEach(function(item){
    $yaz += item.name + ' Cihazına ' +  item.last_at + ' Den Beri Ulaşılamıyor <br>';
  });
}
if(lastdata.changeTags !== undefined && lastdata.changeTags.length !== 'undefined'){
    if(lastdata.changeTags.length > 0 ){
        $yaz += "<hr></hr></br>";
    }
  lastdata.changeTags.forEach(function(item){
    $yaz += item.name + ' Cihazınadaki ' +  item.tag + ' değeri  ' + item.last_change+ ' den beri değişmedi <br>';
  });
}
$('#cevrimdisilar').html($yaz);

      },
      complete: function() {
        setTimeout(deviceCheck, 180000);
      }
    });
  }
  setTimeout(deviceCheck,600);
});
</script>

<script>
  function loadDeviceInfo() {
      $.get("{{ route('deviceonline') }}", function (data) {

          // Üst bilgi
          $(".ustcihaz").first().text(data.deviceCount);
          $("#ustnokta").text(data.pointCount);

          // Çevrimdışı cihaz bilgileri
          $("#cevrimdisi").text(data.oflineCount);
          let offlineList = "";
          data.oflineDevices.forEach(d => {
              offlineList += `<li>${d.name} Cihazına ${d.last_at} Den Beri Ulaşılamıyor ${d.status == 0 ? '(Pasif)' : ''}</li>`;
          });
          $("#cevrimdisilar").html(offlineList);

          // Pasif cihaz bilgileri (çevrimdışılar da dahil)
          $("#pasif").text(data.passiveCount);
          let passiveList = "";
          data.passiveDevices.forEach(d => {
              passiveList += `<li>${d.name} (Pasif)</li>`;
          });
          $("#pasifler").html(passiveList);
      });
  }

  setInterval(loadDeviceInfo, 10000);
  loadDeviceInfo();

</script>

@endpush
