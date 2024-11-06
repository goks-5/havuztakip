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

        function DeviceChart(data) {
        Object.keys(data).forEach(function(k) {
        var chartElement = document.getElementById(k);
        if (chartElement) {
            var chartType = chartElement.getAttribute('data-type') || 'line';
            var myChart = echarts.init(chartElement);

            var columns = data[k].cols;
            var rows = data[k].rows;

            var categories = [];
            
            var seriesData = {};

            columns.forEach(function(col, index) {
                if (index === 0) return; 
                seriesData[col.label] = [];
            });

            rows.sort(function(a, b) {
                return new Date(parseDateString(a.c[0].v)) - new Date(parseDateString(b.c[0].v));
            });

            rows.forEach(function(row) {
                var rowDate = parseDateString(row.c[0].v);
                console.log('Tarih:', rowDate, 'Aralıkta mı?', isDateWithinRange(rowDate, '72')); // Son 3 gün için kontrol
                var dateStr = row.c[0].v;
                categories.push(formatDate(parseDateString(dateStr)));
                for (var i = 1; i < row.c.length; i++) {
                    seriesData[columns[i].label].push(row.c[i].v || 0);
                }
            });

            window.seriesData = seriesData; // Global olarak kaydediyoruz
            window.categories = categories;

            // Prepare series array without decal patterns for ECharts
            var series = [];
            Object.keys(seriesData).forEach(function(label) {
                series.push({
                    name: label,
                    type: chartType === 'line' ? 'line' : 'bar',
                    data: seriesData[label],
                    itemStyle: {},
                    lineStyle: {
                        width: 2
                    },
                    symbol: 'circle',
                    symbolSize: 6
                });
            });

            // Configure ECharts options without toolbox
            var option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
            data: Object.keys(seriesData),
            top: 0,
            selectedMode: 'multiple', // 'multiple' seçeneği birden fazla seriyi yönetmenizi sağlar.
            // 'single' seçeneği sadece tek bir seri göstermek için kullanılır
            },

            dataZoom: [
                {
                    type: 'inside',
                    start: 0,
                    end: 100,
                    zoomLock: true
                },
                {
                    type: 'slider',
                    start: 0,
                    end: 100,
                    height: 10,
                    bottom: 20
                }
            ],
            xAxis: {
                type: 'category',
                data: categories
            },
            yAxis: {
                type: 'value'
            },
            series: series
        };


        myChart.setOption(option);

        // Excel ve Print işlevlerini global erişilebilir hale getir
        window.downloadExcel = function(seriesData, categories) {
        const rows = [["Tarih", ...categories]]; // İlk satır: "Tarih" ve kategoriler

        Object.keys(seriesData).forEach((label) => {
            const row = [label, ...seriesData[label]]; // İlk sütun etiket adı, ardından veriler
            rows.push(row);
        });

        // CSV içeriğini oluştur
        let csvContent = 'data:text/csv;charset=utf-8,';
        rows.forEach(row => {
            csvContent += row.map(value => `"${value}"`).join(",") + "\n"; // Değerleri tırnak içine al
        });

        // CSV dosyasını indirmek için bağlantı oluştur
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "Grafik Verileri.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    window.saveImage = function(chartId) {
        var chartElement = document.getElementById(chartId);
        if (chartElement) {
            var chart = echarts.getInstanceByDom(chartElement);
            if (chart) {
                var base64 = chart.getDataURL({
                    type: 'png',
                    backgroundColor: '#ffffff'
                });

                var link = document.createElement('a');
                link.href = base64;
                link.download = 'Grafik Resmi.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                console.error(`ECharts örneği bulunamadı! ID: ${chartId}`);
            }
        } else {
            console.error("Grafik bulunamadı! ID: " + chartId);
        }
    };
            }
        });
    }

    document.getElementById('timeRangeSelector').addEventListener('change', function() {
        var selectedRange = this.value;

        // Filtreleme işlemi yaparak ilgili veri aralığını alın
        var filteredRows = rows.filter(function(row) {
            var rowDate = parseDateString(row.c[0].v);
            return isDateWithinRange(rowDate, selectedRange);
        });

        // Filtrelenmiş verileri kullanarak grafiği yeniden çizin
        updateChart(filteredRows);
    });

    /**
     * Belirtilen tarihin seçilen aralıkta olup olmadığını kontrol eder.
     * @param {Date} date - Kontrol edilecek tarih
     * @param {string} range - Seçilen aralık
     * @returns {boolean}
     */
    function isDateWithinRange(date, range) {
        var now = new Date();
        switch (range) {
            case '6': // Son 6 saat
                return now - date <= 6 * 60 * 60 * 1000;

            case '24': // Son 1 gün
                return now - date <= 24 * 60 * 60 * 1000;

            case 'D': // Bugün
                return date.toDateString() === now.toDateString();

            case '72': // Son 3 gün
                var threeDaysAgo = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 3);
                return date >= threeDaysAgo && date <= now;

            case '168': // Son 7 gün
                var sevenDaysAgo = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 7);
                return date >= sevenDaysAgo && date <= now;

            case 'W': // Bu hafta
                var startOfWeek = new Date(now);
                startOfWeek.setDate(now.getDate() - now.getDay());
                return date >= startOfWeek;

            case '720': // Son 1 ay
                var oneMonthAgo = new Date(now);
                oneMonthAgo.setMonth(now.getMonth() - 1);
                return date >= oneMonthAgo;

            case 'M': // Bu ay
                return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();

            case '2160': // Son 3 ay
                var threeMonthsAgo = new Date(now);
                threeMonthsAgo.setMonth(now.getMonth() - 3);
                return date >= threeMonthsAgo;

            case '8640': // Son 1 yıl
                var oneYearAgo = new Date(now);
                oneYearAgo.setFullYear(now.getFullYear() - 1);
                return date >= oneYearAgo;

            case 'Y': // Bu yıl
                return date.getFullYear() === now.getFullYear();

            default:
                return true; // Varsayılan olarak tüm verileri göster
        }
    }

    /**
     * Grafik verilerini güncellemek için kullanılır.
     * @param {Array} filteredRows - Filtrelenmiş satırlar
     */
    function updateChart(filteredRows) {
        var uniqueCategories = new Set();
        var updatedCategories = [];
        var updatedSeriesData = {};

        // Initialize series data structure
        columns.forEach(function (col, index) {
            if (index === 0) return; // Skip first column (date)
            updatedSeriesData[col.label] = [];
        });

        // Process each filtered row
        filteredRows.forEach(function (row) {
            var dateStr = row.c[0].v;
            var formattedDate = formatDate(parseDateString(dateStr));

            // Check if the formatted date already exists in the Set
            if (!uniqueCategories.has(formattedDate)) {
                uniqueCategories.add(formattedDate); // Add to Set
                updatedCategories.push(formattedDate); // Update categories

                // Update each series data with corresponding row values
                for (var i = 1; i < row.c.length; i++) {
                    updatedSeriesData[columns[i].label].push(row.c[i].v || 0);
                }
            } else {
                console.log(`Duplicate date ignored: ${formattedDate}`);
            }
        });

        // Log the unique categories for debugging
        console.log("Unique Categories:", Array.from(uniqueCategories));

        // Update chart options
        myChart.setOption({
            xAxis: {
                type: 'category',
                data: updatedCategories, // Use filtered unique categories
            },
            series: Object.keys(updatedSeriesData).map(function (label) {
                return {
                    name: label,
                    type: chartType, // Line or bar depending on selected chartType
                    data: updatedSeriesData[label],
                    smooth: true,
                };
            }),
        });
    }



    /**
     * Verilen tarih stringini JavaScript Date objesine dönüştürür.
     * @param {string} dateStr - Tarih stringi (örn. Date(2024,9,27,8,0,0))
     * @returns {Date}
     */
    function parseDateString(dateStr) {
        var parts = dateStr.match(/Date\((\d+),(\d+),(\d+),(\d+),(\d+),(\d+)\)/);
        if (parts) {
            return new Date(Date.UTC(parts[1], parts[2], parts[3], parts[4], parts[5], parts[6]));
        }
        return new Date(); // Hata durumunda geçerli tarihi döndür
    }

    /**
     * Verilen tarih objesini YYYY-MM-DD formatında döndürür.
     * @param {Date} date - Formatlanacak tarih
     * @returns {string}
     */
    function formatDate(date) {
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        return `${year}-${pad(month)}-${pad(day)} ${pad(hours)}:${pad(minutes)}`;
    }


    /**
     * Tek haneli sayılara 0 ekler.
     * @param {number} n - Sayı
     * @returns {string}
     */
    function pad(n) {
        return n < 10 ? '0' + n : n.toString();
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
                            [2, 'desc']
                        ],
                        paging: false,
                        "info": false,
                        "createdRow": function(row, data, dataIndex) {
                            if (data[0].includes("Bekliyor |0|")) {
                                $(row).addClass('bekliyor');
                            } else if (data[0].includes("Bakıma Başlandı |0|")) {
                                $(row).addClass('basladi');
                            } else if (data[0].includes("Firma Yönlendirildi |2|")) {
                                $(row).addClass('yonlendirildi');
                            } else if (data[0].includes("Malzeme Bekliyor |2|")) {
                                $(row).addClass('m_bekliyor');
                            } else if (data[0].includes("Onay |1|")) {
                                $(row).addClass('onay');
                            } else if (data[0].includes("Yeni")) {
                                $(row).addClass('yeni');
                            }
                        }
                    });
                }


                t.clear();
                Object.keys(data[k]).forEach(function(s) {
                    actionbtn = '';
                    @can('accept', app('App\Fault'))
                        if (data[k][s]['status'] == 'Yeni') {
                            actionbtn = '<a href="#" title="Kabul Et" data-id ="' +
                                data[k][s]['id'] +
                                '" class="btn btn-sm btn-danger pull-right edit modalidset" data-toggle="modal"  data-target="#acceptModal" >' +
                                '<i class="voyager-paper-plane"></i> <span class="hidden-xs hidden-sm">Kabul Et</span></a>';
                        } else if (data[k][s]['status'] != 'Onay |1|' && data[k][s]['status'] !=
                            'Bitti |1|') {
                            actionbtn = '<a href="#" title="İşlem Gir" data-id ="' +
                                data[k][s]['id'] +
                                '" class="btn btn-sm btn-warning pull-right edit modalidset" data-toggle="modal"  data-target="#actionModal">' +
                                '<i class="voyager-fire"></i> <span class="hidden-xs hidden-sm">İşlem Gir</span></a>';
                        }
                    @endcan

                    @can('close', app('App\Fault'))
                        if (data[k][s]['status'] == 'Onay |1|') {

                            actionbtn = '<a href="#" title="Arızayı Kapat" data-id ="' +
                                data[k][s]['id'] +
                                '" class="btn btn-sm btn-primary pull-right edit modalidset" data-toggle="modal"  data-target="#closeModal">' +
                                '<i class="voyager-lightbulb"></i> <span class="hidden-xs hidden-sm">Arızayı Kapat</span></a>';
                        }
                    @endcan


                    t.row.add([
                    '<p title="Raporlayan : ' +
                    data[k][s]['reporting_user'] + // mevcut bildiren personel verisi
                    ' , Bakımcı : ' + data[k][s]['staff'] +
                    '">' + data[k][s]['status'] + '</p>',
                    data[k][s]['equipment'] +
                    ' - ' + data[k][s]['fault_code'] +
                    '<br>' +  data[k][s]['fault_comment'],
                    '<p title="Kabul Edilme : ' + data[k][s]['accepted_at'] +
                    '">' + data[k][s]['created_at'] + '</p>',
                    data[k][s]['reporting_user'], // Yeni eklenen sütun için bildiren personel verisi
                    actionbtn
                ]).draw(false);

                });
            });
        }

        $('.faults_table').on('click', '.modalidset', function() {
            console.log($(this).data('id'));
            $('.modalidinput').val($(this).data('id'));
        });


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

    function exportToTablo(divName) {
    console.log("Function triggered");

    // 1. Get the table element containing the data
    var table = document.getElementById(divName);
    if (!table) {
        console.error("Table not found with the given divName:", divName);
        return;
    }

    // 2. Create a new array to store formatted data
    var tableData = [];

    // 3. Include the headers (i.e., the dates row)
    var headers = table.getElementsByTagName('thead')[0];
    if (headers) {
        var headerRowData = [];
        var headerCols = headers.getElementsByTagName('th');
        for (var h = 0; h < headerCols.length; h++) {
            headerRowData.push(headerCols[h].innerText.trim());
        }
        tableData.push(headerRowData); // Add the header row to the table data
    }

    // 4. Add the rest of the table's data (ignore rows without valid data)
    var tableRows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (var i = 0; i < tableRows.length; i++) {
        var rowData = [];
        var tableCols = tableRows[i].getElementsByTagName('td');

        for (var j = 0; j < tableCols.length; j++) {
            var cellValue = tableCols[j].innerText.trim();

            // Convert Turkish comma decimals to dots for proper Excel/Numbers recognition
            cellValue = cellValue.replace(/\./g, '').replace(',', '.');

            // Ensure that cellValue is treated as a number if it's numeric
            if (!isNaN(cellValue) && cellValue !== '') {
                cellValue = parseFloat(cellValue); // Leave the original decimal format
            }

            rowData.push(cellValue);
        }

        // Only push the row if it contains actual data
        if (rowData.length > 0 && rowData.some(val => val !== '')) {
            tableData.push(rowData);
        }
    }

    // 5. Create a workbook from the formatted table data
    var worksheet = XLSX.utils.aoa_to_sheet(tableData);
    var workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

    // 6. Create the Excel file in binary format
    var wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });

    // 7. Helper function to convert the data to binary
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }

    // 8. Correct MIME type for Excel
    var blob = new Blob([s2ab(wbout)], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

    // 9. Create a link to download the file
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'tablo_verileri.xlsx'; // Name the file accordingly

    // 10. Trigger the download process with a slight delay
    setTimeout(function() {
        link.click(); // Simulate a click on the download link
        document.body.removeChild(link); // Remove the link from the DOM
    }, 100); // Delay for 100ms

    document.body.appendChild(link); // Append the link to the document
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
