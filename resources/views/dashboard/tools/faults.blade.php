<div class="tool_data row">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])

<!-- Grafik Konteyneri -->
<div id="chart-container" style="width: 100%; height: 400px; margin-top: 20px;"></div>

@php
use Carbon\Carbon;
use App\Fault;

// Son 24 saat filtresi kaldırıldı; artık tüm kayıtlar göz önünde bulunduruluyor.
$statuses = [
    'Yeni'                    => Fault::where('company_id', Auth::user()->company_id)
                                ->where('status', 'Yeni')
                                ->count(),
    'Bekliyor'                => Fault::where('company_id', Auth::user()->company_id)
                                ->where('status', 'Bekliyor |0|')
                                ->count(),
    'Bakıma başlandı'         => Fault::where('company_id', Auth::user()->company_id)
                                ->where('status', 'Bakıma Başlandı |0|')
                                ->count(),
    'Firmaya yönlendirildi'   => Fault::where('company_id', Auth::user()->company_id)
                                ->where('status', 'Firma Yönlendirildi |2|')
                                ->count(),
    'Malzeme bekleniyor'      => Fault::where('company_id', Auth::user()->company_id)
                                ->where('status', 'Malzeme Bekliyor |2|')
                                ->count(),
    'Onay'                    => Fault::where('company_id', Auth::user()->company_id)
                                ->where('status', 'Onay |1|')
                                ->count()
];

// Grafik için veriyi ECharts'ın beklediği formata dönüştürelim
$dataChart = [
    ['value' => $statuses['Yeni'],                  'name' => 'Yeni',                  'itemStyle' => ['color' => '#ffcccc']],
    ['value' => $statuses['Bekliyor'],              'name' => 'Bekliyor',              'itemStyle' => ['color' => '#ffe5cc']],
    ['value' => $statuses['Bakıma başlandı'],       'name' => 'Bakıma başlandı',       'itemStyle' => ['color' => '#ffffcc']],
    ['value' => $statuses['Firmaya yönlendirildi'], 'name' => 'Firmaya yönlendirildi', 'itemStyle' => ['color' => '#ccf2ff']],
    ['value' => $statuses['Malzeme bekleniyor'],    'name' => 'Malzeme bekleniyor',    'itemStyle' => ['color' => '#99e6ff']],
    ['value' => $statuses['Onay'],                  'name' => 'Onay',                  'itemStyle' => ['color' => '#ccffcc']]
];
@endphp

<!-- ECharts JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/echarts@latest/dist/echarts.min.js"></script>

<script>
  // ECharts örneğini başlat
  var chartDom = document.getElementById('chart-container');
  var myChart = echarts.init(chartDom);
  var option;

  option = {
      legend: {
          orient: 'horizontal',
          top: '0%',
          left: 'center'
      },
      tooltip: {
          trigger: 'item'
      },
      series: [
          {
              name: 'Arızalar',
              type: 'pie',
              center: ['50%', '52%'],  // Grafiği biraz aşağı kaydırıyoruz
              radius: ['40%', '70%'],   // Donut (halka) grafik
              label: {
                  show: true,
                  position: 'outside'
              },
              labelLine: {
                  show: true
              },
              data: {!! json_encode($dataChart) !!}
          }
      ]
  };

  myChart.setOption(option);
</script>
</div>