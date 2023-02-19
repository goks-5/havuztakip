<script type="text/javascript">
    $(document).ready(function() {
        function worker() {
            gorev();
            setTimeout(worker, 60000);
        }

        function gorev() {
            $.ajax({
                url: '{{ route('dashboarddata') }}',
                data: {
                    'dashboard': {{ $board->id ?? 0 }}
                },
                success: function(data) {
                    var lastdata = JSON.parse(data);
                    Object.keys(lastdata).forEach(function(k) {
                        switch (k) {
                            case 'device_data':
                                DeviceData(lastdata[k]);
                                break;
                            case 'last_date':
                                DeviceData(lastdata[k]);
                                break;
                            case 'device_daily_data':
                                DeviceData(lastdata[k]);
                                break;
                            case 'device_data_gauge':
                                DeviceDataGauge(lastdata[k]);
                                break;
                            case 'device_meter':
                                DeviceMeter(lastdata[k]);
                                break;
                            case 'device_indicator':
                                DeviceIndicator(lastdata[k]);
                                break;
                            case 'device_alarm':
                                DeviceAlarm(lastdata[k]);
                                break;
                            case 'device_chart':
                                DeviceChart(lastdata[k]);
                                break;
                            case 'faults':
                                faults(lastdata[k]);
                                break;
                            case 'faults_table':
                                faultsTable(lastdata[k]);
                                break;
                            case 'tags':
                                tags(lastdata[k]);
                                break;

                            case 'period':
                                period(lastdata[k]);
                                break;

                            case 'switch':
                                fswitch(lastdata[k]);
                                break;

                        }
                    });
                }
            });
        }

        setTimeout(worker, 5000);
        setTimeout(worker, 1000);



        $(function() {
            $(".resizable").resizable({
                grid: 10,
                stop: function(event, ui) {
                    postsize(ui);
                }
            });
            $(".resizable").draggable({
                snap: true,
                scroll: false,
                stop: function(event, ui) {
                    postsize(ui);
                }
            });
            @if (Session::has('message'))
                $('#editOnOff').bootstrapToggle('on');
                $('.boardlink').attr('contenteditable', true);
            @else
                $(".resizable").resizable('disable');
                $(".resizable").draggable('disable');
                $('.boardlink').attr('contenteditable', false);
                $(".toolSettings").hide();
                $(".edithide").hide();
            @endif
            $('#editOnOff').change(function() {
                if ($(this).prop('checked')) {
                    $(".resizable").resizable('enable');
                    $(".resizable").draggable('enable');
                    $('.boardlink').attr('contenteditable', true);
                    $(".toolSettings").show();
                    $(".edithide").show();
                } else {
                    $(".resizable").resizable('disable');
                    $(".resizable").draggable('disable');
                    $('.boardlink').attr('contenteditable', false);
                    $(".toolSettings").hide();
                    $(".edithide").hide();
                }
            });
            $('.boardlink').blur(function() {
                $.ajax({
                    url: '{{ route('boardAction') }}',
                    type: 'post',
                    data: {
                        id: $(this).data('id'),
                        title: $(this).text(),
                        action_type: 'changeBoardName'
                    },
                });
            });


        });


        function postsize(ui) {

            $.ajax({
                url: '{{ route('toolStyle') }}',
                type: 'post',
                data: {
                    id: ui.helper.data('tool'),
                    style: ui.helper.attr("style")
                },
            });
            gorev();
        }




        function DeviceIndicator(data) {
            Object.keys(data).forEach(function(k) {
                if ($('#indicator_out_' + data[k].tool).length) {
                    $('#indicator_out_' + data[k].tool).css("box-shadow", "0px 0px 8px 1px " + data[k]
                        .color);
                    $('#indicator_out_' + data[k].tool).css("background-color", data[k].color);
                }
            });
        }

        function DeviceAlarm(data) {
            Object.keys(data).forEach(function(k) {
                if ($('#indicator_out_' + data[k].tool).length) {
                    $('#indicator_out_' + data[k].tool).css("box-shadow", "0px 0px 0px 0px #FFFFFF");
                    $('#indicator_out_' + data[k].tool).css("background-color", "#FFFFFF");
                    var a = document.getElementById('audio_' + data[k].tool);
                    if (!(a.play instanceof Function)) {
                        a = document.getElementById('audio_ie8_' + data[k].tool);
                    }
                    a.pause();
                    if (data[k].alarm == true) {
                        $('#indicator_in_' + data[k].tool).css("background-image",
                            "url(/storage/widget/alarm.gif)");
                        if (data[k].sound == '1') {
                            a.play();
                        }
                    } else {
                        $('#indicator_in_' + data[k].tool).css("background-image",
                            "url(/storage/widget/alarmoff.png)");
                        if (data[k].sound == '1') {
                            a.pause();
                        }
                    }

                }
            });
        }

        function DeviceChart(data) {
            Object.keys(data).forEach(function(k) {
                if ($('#' + k).length) {
                    var chartDiv = document.getElementById(k);

                    if ($(chartDiv).data('type') == 'line') {
                        var materialChart = new google.visualization.LineChart(chartDiv);
                        var options = {
                            //'height': $('#' + k).height(),
                            explorer: {
                                actions: ['dragToZoom', 'rightClickToReset'],
                                axis: 'horizontal',
                                keepInBounds: true,
                                maxZoomIn: 16.0
                            },
                            chartArea: {
                                left: 50,
                                top: 20,
                                width: '93%'
                            },
                            legend: {
                                position: 'bottom'
                            }
                        };
                        var data2 = new google.visualization.DataTable(data[k]);
                        materialChart.draw(data2, options);
                    } else {
                        var materialChart = new google.visualization.ColumnChart(chartDiv);
                        var options = {
                            //  'height': $('#' + k).height(),
                            explorer: {
                                actions: ['dragToZoom', 'rightClickToReset'],
                                axis: 'horizontal',
                                keepInBounds: true,
                                maxZoomIn: 16.0
                            },
                            chartArea: {
                                left: 50,
                                top: 20,
                                width: '93%'
                            },
                            legend: {
                                position: 'bottom'
                            }
                        };
                        var data2 = new google.visualization.DataTable(data[k]);
                        materialChart.draw(data2, google.charts.Bar.convertOptions(options));
                    }

                }
            });
        }

        function DeviceData(data) {
            Object.keys(data).forEach(function(k) {
                if ($('#' + k).length) {
                    $('#' + k).html(data[k]);
                }
            });
        }

        function faults(data) {
            Object.keys(data).forEach(function(k) {

                if ($('#' + k).length) {
                    var options = {
                        title: 'Arızalar',
                        pieHole: 0.4,
                        pieSliceText: 'value',
                        // is3D : true,
                        slices: {
                            0: {
                                color: '#ff3333'
                            },
                            1: {
                                color: '#99ff99'
                            },
                            2: {
                                color: '#ffff55'
                            },
                            3: {
                                color: '#ffff88'
                            },
                            4: {
                                color: '#aaeeff'
                            },
                            5: {
                                color: '#99ddee'
                            }
                        },
                    };

                    var chart = new google.visualization.PieChart(document.getElementById(k));
                    var data2 = new google.visualization.arrayToDataTable(data[k]['fault']);

                    chart.draw(data2, options);
                }
            });


        }


        function tags(data) {
            var t = $('#tagstable').DataTable();
            t.clear();
            activeDevice = [];
            Object.keys(data).forEach(function(k) {
                Object.keys(data[k]).forEach(function(s) {
                    Object.keys(data[k][s]['tags']).forEach(function(ta) {
                        if (data[k][s]['end_time'] == null) {
                            activeDevice[data[k][s]['id']] = allDevice[data[k][s][
                                'tags_group'
                            ]];
                            data[k][s]['end_time'] =
                                ' <a href="#" class="btn btn-danger btn-sm"  style="float: right;" data-href="{{ route('end_tag') }}?id=' +
                                data[k][s]['id'] +
                                '"  data-toggle="modal" data-target="#confirm-end">Bitir</a>';
                        }
                        t.row.add([
                            data[k][s]['name'] + ' ' + data[k][s][
                                'start_time'
                            ] + ' - ' + data[k][s]['end_time'],
                            data[k][s]['tags'][ta]['name'],
                            data[k][s]['tags'][ta]['value']

                        ]).draw(false);
                    });
                });
            });
        }
/*
      case 'Bekliyor |0|':
                                      $fontcolor = "#526069";
                                      $cellcolor = "#FF5";
                                      break;
                                      case 'Bakıma Başlandı |0|':
                                      $fontcolor = "#526069";
                                      $cellcolor = "#FF8";
                                      break;
                                      case 'Firma Yönlendirildi |2|':
                                      $fontcolor = "#526069";
                                      $cellcolor = "#aef";
                                      break;
                                      case 'Malzeme Bekliyor |2|':
                                      $fontcolor = "#526069";
                                      $cellcolor = "#aef";
                                      break;
                                      case 'Onay |1|':
                                      $cellcolor = "#9F9";
                                      $fontcolor = "#526069";
                                      break;
                                      case 'Yeni':*/
        function faultsTable(data) {
            Object.keys(data).forEach(function(k) {

                if ($.fn.dataTable.isDataTable('#faults_table_' + k)) {
                    var t = $('#faults_table_' + k).DataTable();
                } else {
                    var t = $('#faults_table_' + k).DataTable({
                        "searching": false,
                        "order": [
                            [5, 'desc']
                        ],
                        paging: false,
                        "info": false,
                        "createdRow": function(row, data, dataIndex) {
                            if (data[0] == "Bekliyor |0|") {
                                $(row).addClass('bekliyor');
                            }
                            if (data[0] == "Bakıma Başlandı |0|") {
                                $(row).addClass('basladi');
                            }
                            if (data[0] == "Firma Yönlendirildi |2|") {
                                $(row).addClass('yonlendirildi');
                            }
                            if (data[0] == "Malzeme Bekliyor |2|") {
                                $(row).addClass('m_bekliyor');
                            }
                            if (data[0] == "Onay |1|") {
                                $(row).addClass('onay');
                            }
                            if (data[0] == "Yeni") {
                                $(row).addClass('yeni');
                            }
                        }
                    });
                }


                t.clear();
                Object.keys(data[k]).forEach(function(s) {
                    t.row.add([
                        data[k][s]['status'],
                        data[k][s]['equipment'],
                       '<p title="'+ data[k][s]['fault_comment'] +'">' + data[k][s]['fault_code'] + '</p>',
                        data[k][s]['reporting_user'],
                        data[k][s]['staff'],
                        data[k][s]['created_at'],
                        '<a href="/arizalar/' +
                        data[k][s]['id'] +
                        '/edit" title="Düzenle" class="btn btn-sm btn-primary pull-right edit">' +
                        '<i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Düzenle</span></a>'

                    ]).draw(false);
                });
            });
        }

        function period(data) {
            Object.keys(data).forEach(function(k) {
                var datatable = new google.visualization.DataTable(data[k]);
                var table = new google.visualization.Table(document.getElementById(k));
                table.draw(datatable, {
                    width: '100%',
                    height: '100%'
                });
            });




        }

        function DeviceDataGauge(data) {
            Object.keys(data).forEach(function(k) {
                console.log(data[k].tool);
                if ($('#span_' + data[k].tool).length) {
                    datas[k].setValue(0, 1, data[k].value);
                    chart[k].draw(datas[k], options[k]);
                }
            });
        }

        function DeviceMeter(data) {
            Object.keys(data).forEach(function(k) {
                console.log(data[k].tool);
                if ($('#meter_' + data[k].tool).length) {
                    $('#meter_' + data[k].tool).val(data[k].value);
                    $('#meter_' + data[k].tool).text(data[k].value);
                    $('#span_' + data[k].tool).text(data[k].value);
                }
            });
        }

        function fswitch(data) {
            Object.keys(data).forEach(function(k) {
                if ($('#switch_' + k).length) {
                    if ($('#switch_' + k).data('onvalue') == data[k] && !$('#switch_' + k)
                        .prop(
                            "checked")) {
                        $('#switch_' + k).bootstrapToggle('on');
                    } else if ($('#switch_' + k).data('offvalue') == data[k] && $(
                            '#switch_' + k).prop(
                            "checked")) {
                        $('#switch_' + k).bootstrapToggle('off');
                    }
                }
            });
        }


    });


    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
