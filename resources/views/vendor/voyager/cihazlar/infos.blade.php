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
                    var data_{{ $key }} = google.visualization.arrayToDataTable([
                        ['Date', '{{ $numberInfo['name'] }}'],
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
                @endforeach
            }
        </script>
    @endif

@stop
