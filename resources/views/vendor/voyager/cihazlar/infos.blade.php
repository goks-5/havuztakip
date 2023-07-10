@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing') . ' Cihaz Verileri')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-bar-chart"></i> Cihaz Sinyal Detayları
        </h1>

        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($string ?? false)
                            @foreach ($string as $stringInfo)
                                <div class="command">
                                    <code>{{ $stringInfo['name'] }}</code>
                                    <small>{{ $stringInfo['value'] }}</small>
                                </div>
                            @endforeach
                        @endif
                        @if ($number ?? false)
                            @foreach ($number as $key => $numberInfo)
                                <div id="chart_{{ $key }}" style="width: 100%; height: 500px;"></div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')

@stop
@section('javascript')
    @if ($number ?? false)
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {
                'packages': ['corechart']
            });
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                @foreach ($number as $key => $numberInfo)
                    @if($key == 'rssi')
                        var data_{{ $key }} = google.visualization.arrayToDataTable([
                                ['Saat' ,'Poor' ,'Fair','Good', 'Excellent', '{{ $numberInfo['name'] }}'], 
                            @foreach ($numberInfo['values'] as $date => $value)['{{ $date }}', -100, -85, -75, -65, {{ $value }} ],@endforeach
                        ]);
                        var options_{{ $key }} = {
                            title: '{{ $numberInfo['name'] }}',
                            vAxis: {title: "Rssi",  ticks: [-35, -65,]},
                            hAxis: {title: "Saat"},
                            seriesType: "area",
                            series: {
                                0: {color: '#FF0000',visibleInLegend: false},
                                1: {color: '#FFA500',visibleInLegend: false},
                                2: {color: '#FFFF00',visibleInLegend: false},
                                3: {color: '#00FF00',visibleInLegend: false},
                                4: {color: '#01579B',type: "line"}
                            },
                            isStacked: true,
                           // curveType: 'function',
                            legend: {
                                position: 'bottom'
                            }
                        };
           
                        var chart_{{ $key }} = new google.visualization.ComboChart(document.getElementById('chart_{{ $key }}'));
                        chart_{{ $key }}.draw(data_{{ $key }}, options_{{ $key }});
                    @else
                        var data_{{ $key }} = google.visualization.arrayToDataTable([
                            ['Saat', '{{ $numberInfo['name'] }}'], 
                            @foreach ($numberInfo['values'] as $date => $value)
                            ['{{ $date }}', {{ $value }}], 
                            @endforeach
                        ]);
                        var options_{{ $key }} = {
                            title: '{{ $numberInfo['name'] }}',
                            curveType: 'function',
                            legend: {
                                position: 'bottom'
                            }
                        };
                        var chart_{{ $key }} = new google.visualization.LineChart(document.getElementById('chart_{{ $key }}'));
                        chart_{{ $key }}.draw(data_{{ $key }}, options_{{ $key }});
                    @endif
                @endforeach
            }
        </script>
    @endif
@stop
