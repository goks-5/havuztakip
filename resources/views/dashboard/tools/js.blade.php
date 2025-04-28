<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<!-- ECharts Library -->
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

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
                            case 'card':
                                card(lastdata[k]);
                                break;   
                            case 'period':
                                period(lastdata[k]);
                                break;

                            case 'switch':
                                fswitch(lastdata[k]);
                                break;
                                
                            case 'sum_tag':
                                sumTag(lastdata[k]);
                                break;

                            case 'go_to_tab_button':
                                gototab(lastdata[k]);
                                break;
                                
                            case 'oee_report':
                            oeeReport(lastdata[k]);
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
                $('.boardlink').css('padding-right','60px');
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
                    $('.boardlink').css('padding-right','60px');
                    $(".toolSettings").show();
                    $(".edithide").show();
                } else {
                    $(".resizable").resizable('disable');
                    $(".resizable").draggable('disable');
                    $('.boardlink').attr('contenteditable', false);
                    $('.boardlink').css('padding-right','20px');
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
 
// Function to initialize the chart
function DeviceChart(data) {
    Object.keys(data).forEach(function (k) {
        if ($('#' + k).length) {
            var chartDiv = document.getElementById(k);
            var chartType = $(chartDiv).data('type');

            var categories = [];
            var seriesData = [];

            if (data[k] && data[k].rows && data[k].cols) {
                data[k].rows.forEach(function (row) {
                    if (row.c && row.c[0] && row.c[0].v) {
                        let dateValue = row.c[0].v.replace("Date", "").replace("(", "").replace(")", "");
                        let dateParts = dateValue.split(",");
                        let formattedDate = `${dateParts[0]}-${parseInt(dateParts[1]) + 1}-${dateParts[2]} ${dateParts[3]}:${dateParts[4]}`;
                        if (!categories.includes(formattedDate)) {
                            categories.push(formattedDate);
                        }

                        for (let i = 1; i < data[k].cols.length; i++) {
                            if (!seriesData[i - 1]) {
                                seriesData[i - 1] = {
                                    name: data[k].cols[i].label,
                                    type: chartType === 'line' ? 'line' : 'bar',
                                    data: new Array(categories.length).fill(0)
                                };
                            }

                            let categoryIndex = categories.indexOf(formattedDate);
                            if (row.c[i] && row.c[i].v) {
                                seriesData[i - 1].data[categoryIndex] = parseFloat(row.c[i].v);
                            }
                        }
                    }
                });

                window[`chartData_${k}`] = { categories, seriesData };
                
                var myChart = echarts.init(chartDiv);

                var option = {
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: seriesData.map(series => series.name)
                    },
                    xAxis: {
                        type: 'category',
                        data: categories
                    },
                    yAxis: {
                        type: 'value'
                    },
                    dataZoom: [
                        {
                            type: 'slider',
                            start: 0,
                            end: 100,
                            height: 20
                        },
                        {
                            type: 'inside',
                            start: 0,
                            end: 100
                        }
                    ],
                    series: seriesData
                };

                myChart.setOption(option);

                // Add ResizeObserver to handle dynamic resizing
                var resizeObserver = new ResizeObserver(() => {
                    myChart.resize();
                });
                resizeObserver.observe(chartDiv);

                // Store observer to disconnect later if needed
                window[`resizeObserver_${k}`] = resizeObserver;

                // Event listeners for zoom and legend
                myChart.on('dataZoom', function (params) {
                    updateYAxisRange(params.batch ? params.batch[0] : params);
                });

                myChart.on('legendselectchanged', function () {
                    const dataZoom = myChart.getOption().dataZoom[0];
                    updateYAxisRange(dataZoom);
                });

                function updateYAxisRange(params) {
                    const startPercent = params.start / 100;
                    const endPercent = params.end / 100;
                    const startIndex = Math.floor(startPercent * categories.length);
                    const endIndex = Math.floor(endPercent * categories.length) - 1;

                    const visibleSeries = seriesData.filter(series => {
                        return myChart.getOption().legend[0].selected[series.name] !== false;
                    });

                    const { min, max } = calculateDynamicRange(visibleSeries, startIndex, endIndex);
                    myChart.setOption({
                        yAxis: {
                            min,
                            max
                        }
                    });
                }

                function calculateDynamicRange(visibleSeries, startIndex, endIndex) {
                    const visibleData = visibleSeries.flatMap(series => 
                        series.data.slice(startIndex, endIndex + 1).filter(val => val !== 0)
                    );
                    return {
                        min: Math.min(...visibleData),
                        max: Math.max(...visibleData)
                    };
                }
            } else {
                console.error(`Invalid data format for chart: ${k}`);
            }
        }
    });

    window.downloadExcel = function (chartId) {
        var chartData = window[`chartData_${chartId}`];
        if (!chartData) {
            console.error(`Data not loaded yet. Chart ID: ${chartId}`);
            return;
        }

        const { categories, seriesData } = chartData;

        const csvContent = [
            ['Date', ...seriesData.map(series => series.name)].join(','),
            ...categories.map((date, index) => {
                const row = [date];
                seriesData.forEach(series => row.push(series.data[index] || 0));
                return row.join(',');
            })
        ].join('\n');

        const link = document.createElement('a');
        link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent);
        link.download = `Grafik Verileri Excel.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    window.saveImage = function (chartId) {
        var chartElement = document.getElementById(chartId);
        if (chartElement) {
            var chartInstance = echarts.getInstanceByDom(chartElement);
            if (chartInstance) {
                var base64 = chartInstance.getDataURL({
                    type: 'png',
                    backgroundColor: '#ffffff'
                });
                var link = document.createElement('a');
                link.href = base64;
                link.download = 'Grafik Resmi.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    };
}



        function DeviceData(data) {
            Object.keys(data).forEach(function(k) {
                if ($('#' + k).length) {
                    $('#' + k).html(data[k]);
                }
            });
        }
        
        function sumTag(data) {
            Object.keys(data).forEach(function(k) {
                if (data[k].total && $('#' + k).length) {
                    var totalContent = '<strong>' + data[k].total.label + ':</strong> ' + data[k].total.value;
                    $('#' + k).html(totalContent);
                }

                if (data[k].tags && $('#' + k + '_tips').length) {
                    var tagsContent = '<ul>';
                    data[k].tags.forEach(function(tag) {
                        tagsContent += '<li><strong>' + tag.label + ':</strong> ' + tag.value + '</li>';
                    });
                    tagsContent += '</ul>';
                    $('#' + k + '_tips').html(tagsContent);
                }
            });
        }
        
        function gototab(data) {
        Object.keys(data).forEach(function(k) {
            // Dashboard ID kontrolü yapıyoruz, ID varsa işleme devam ediyoruz
            if (data[k].dashboard_id && $('#' + k).length) {
                var dashboardId = data[k].dashboard_id;

                // Butonun içerisine dinamik olarak URL yönlendirmesini ekliyoruz
                var button = $('#' + k);

                // Butona tıklama olayını dinliyoruz
                button.off('click').on('click', function() {
                    var url = '/dashboard/' + dashboardId; // URL'yi oluşturuyoruz
                    window.location.href = url; // Kullanıcıyı yönlendiriyoruz
                    });
                }
            });
        }
        
        function oeeReport(data) {
    // data = { 'tool_12': {tool_id:12, chartLabels:[], chartData:[], kullanilabilirlik:..., ...}, 'tool_15': {...} }
    Object.keys(data).forEach(function(key) {
        var chartInfo = data[key];
        var toolId = chartInfo.tool_id; // 12, 15 vb.

        // HTML'de benzersiz ID'ler kullanacağız, mesela:
        // <div id="statusChart_tool_12"></div> gibi
        var statusChartId = "statusChart_tool_" + toolId;
        var donutChartId  = "oeeDonutChart_tool_" + toolId;

        // ECharts init
        var statusChart = echarts.init(document.getElementById(statusChartId));
        var donutChart  = echarts.init(document.getElementById(donutChartId));

        // Bar Chart options
        var optionStatus = {
            title: { text: 'Cihaz Çalışma Grafiği', left: 'center' },
            xAxis: { type: 'category', data: chartInfo.chartLabels },
            yAxis: { type: 'value' },
            series: [{
                data: chartInfo.chartData,
                type: 'bar'
            }]
        };
        statusChart.setOption(optionStatus);

        // Donut Chart options
        var oeeValue = chartInfo.oee;
        var donutOption = {
            title: {
                text: 'OEE',
                left: 'center',
                textStyle: { fontSize: 20, fontWeight: 'bold' }
            },
            series: [{
                type: 'pie',
                radius: ['40%', '60%'],
                label: {
                    show: true,
                    position: 'center',
                    formatter: function(params) {
                        return params.dataIndex === 0 ? params.data.value + '%' : '';
                    },
                    fontSize: 16,
                    fontWeight: 'bold',
                    color: '#333'
                },
                labelLine: { show: false },
                data: [
                    { value: oeeValue, name: 'OEE', itemStyle: { color: '#4CAF50' } },
                    { value: 100 - oeeValue, name: 'Kalan', itemStyle: { color: '#e0e0e0' } }
                ]
            }]
        };
        donutChart.setOption(donutOption);

        // Resize vs. eklemek isterseniz:
        window.addEventListener('resize', function () {
            statusChart.resize();
            donutChart.resize();
        });
    });
}
        function faults(data) {

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
       
    }


        function period(data) {
    Object.keys(data).forEach(function (k) {
        var datatable = new google.visualization.DataTable(data[k]);
        var table = new google.visualization.Table(document.getElementById(k));

        var alignment = data[k].alignment || 'left'; // Default alignment to left

        // Apply alignment to data cells
        var numRows = datatable.getNumberOfRows();
        var numCols = datatable.getNumberOfColumns();

        for (let col = 0; col < numCols; col++) {
            // Update header alignment dynamically
            let headerText = datatable.getColumnLabel(col);
            datatable.setColumnLabel(col, `<div style="text-align: ${alignment};">${headerText}</div>`);

            for (let row = 0; row < numRows; row++) {
                let cellValue = datatable.getValue(row, col);
                datatable.setFormattedValue(row, col, `<div style="text-align: ${alignment};">${cellValue}</div>`);
            }
        }

        table.draw(datatable, {
            width: '100%',
            height: '100%',
            allowHtml: true, // Enable HTML formatting
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

    function exportToTablo(divName) {
    console.log("Function triggered");

    // 1. Tabloyu seç
    var table = document.getElementById(divName);
    if (!table) {
        console.error("Table not found:", divName);
        return;
    }

    // 2. Tablo verilerini olduğu gibi al (formatı bozma)
    var tableData = [];
    
    // Başlıkları ekle (th)
    var headers = table.querySelectorAll('thead th');
    if (headers.length > 0) {
        var headerRow = Array.from(headers).map(th => th.innerText.trim());
        tableData.push(headerRow);
    }

    // Satırları işle (td)
    var rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        var rowData = [];
        var cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            // Hücre değerini formatlamadan olduğu gibi al
            rowData.push(cell.innerText.trim());
        });
        if (rowData.length > 0) tableData.push(rowData);
    });

    // 3. Excel dosyasını oluştur
    var worksheet = XLSX.utils.aoa_to_sheet(tableData);
    
    // 4. Türkçe formatı korumak için özel stil uygula
    Object.keys(worksheet).forEach(key => {
        if (!key.startsWith('!') && worksheet[key].v) {
            // Sayısal değerleri tespit et (nokta/virgül içerenler)
            if (typeof worksheet[key].v === 'string' && 
                worksheet[key].v.match(/[\d.,]+/)) {
                worksheet[key].t = 's'; // Türü string olarak zorla (formatı koru)
            }
        }
    });

    var workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Veriler");
    
    // 5. Dosyayı indir
    XLSX.writeFile(workbook, 'tablo_verileri.xlsx');
}


function getTableData(table) {
    var data = [];
    var rows = table.querySelectorAll('tr');
    rows.forEach(function(row) {
        var rowData = [];
        var cells = row.querySelectorAll('th, td');
        cells.forEach(function(cell) {
            var cellValue = cell.innerText.trim();

            // Convert Turkish comma decimals to dots and remove thousand separators
            cellValue = cellValue.replace(/\./g, '').replace(',', '.');

            // Ensure that numeric values are correctly parsed
            if (!isNaN(cellValue) && cellValue !== '') {
                cellValue = parseFloat(cellValue); // Convert to number
            }

            rowData.push(cellValue);
        });
        data.push(rowData);
    });
    return data;
}

function processData(data) {
    var dateMap = {};
    var processedData = [];
    var headerRow = data[0];
    processedData.push(headerRow);

    for (var i = 1; i < data.length; i++) {
        var row = data[i];
        var date = row[0];
        var otherData = row.slice(1);

        if (!dateMap.hasOwnProperty(date)) {
            var newRow = new Array(headerRow.length).fill("");
            newRow[0] = date;
            for (var j = 1; j <= otherData.length; j++) {
                newRow[j] = otherData[j - 1] !== "" ? otherData[j - 1] : 0;
            }
            processedData.push(newRow);
            dateMap[date] = processedData.length - 1;
        } else {
            var rowIndex = dateMap[date];
            for (var j = 0; j < otherData.length; j++) {
                if (otherData[j] !== "") {
                    processedData[rowIndex][j + 1] = otherData[j];
                }
            }
        }
    }

    var maxColumns = headerRow.length;
    processedData = processedData.map(row => {
        while (row.length < maxColumns) {
            row.push("");
        }
        return row;
    });

    return processedData;
}

// Excel dosyasını oluşturmak için kullanılan fonksiyon
function exportToGrafik(divName) {
    console.log("Function triggered");

    var table = document.getElementById(divName);
    if (!table) {
        console.error("Table not found with the given divName:", divName);
        return;
    }

    var data = getTableData(table);
    var processedData = processData(data);

    var worksheet = XLSX.utils.aoa_to_sheet(processedData);
    var workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

    var wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });

    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }

    var blob = new Blob([s2ab(wbout)], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'grafik_verileri.xlsx';

    setTimeout(function () {
        link.click();
        document.body.removeChild(link);
    }, 100);

    document.body.appendChild(link);
}

// Grafiği güncellemek için veri işleme sonuçlarını kullanın
function updateChart(chartInstance, tableId) {
    var table = document.getElementById(tableId);
    if (!table) {
        console.error("Table not found with the given tableId:", tableId);
        return;
    }

    var data = getTableData(table);
    var processedData = processData(data);

    // İşlenmiş verileri kullanarak grafiği güncelleyin
    var labels = processedData.slice(1).map(row => row[0]); // Tarihler
    var datasets = [];

    for (var i = 1; i < processedData[0].length; i++) {
        var datasetData = processedData.slice(1).map(row => parseFloat(row[i]) || 0);
        datasets.push({
            label: processedData[0][i],
            data: datasetData,
            // İsteğe bağlı olarak grafik rengi ekleyebilirsiniz
        });
    }

    chartInstance.data = {
        labels: labels,
        datasets: datasets
    };

    chartInstance.update();
}

</script>
