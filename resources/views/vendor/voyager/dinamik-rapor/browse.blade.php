@extends('voyager::master')

@section('content')
<div class="container-fluid">
    <!-- Tüm input ve butonları aynı satırda tutuyoruz -->
    <div style="display: flex; align-items: center; gap: 15px; margin-top: 20px; flex-wrap: nowrap;">
        <!-- Bölüm Seç -->
        <div style="flex: 1; min-width: 200px;">
            <label for="fieldDropdown" class="sr-only">Bölüm</label>
            <select class="form-control" id="fieldDropdown" multiple="multiple">
                <option value="fabrika">Fabrika</option>
            </select>
        </div>

        <!-- Cihaz Seç -->
        <div style="flex: 1; min-width: 200px;">
            <label for="device" class="sr-only">Cihaz</label>
            <select class="form-control" id="device" multiple="multiple"></select>
        </div>

        <!-- Başlangıç Tarihi -->
        <div style="flex: 1; min-width: 200px;">
            <label for="date_start_picker" class="sr-only">Başlangıç Tarihi</label>
            <div class="input-group date" id="date_start_picker" data-target-input="nearest">
                <input type="text"
                       id="date_start"
                       class="form-control datetimepicker-input"
                       data-toggle="datetimepicker"
                       data-target="#date_start_picker"
                       placeholder="Başlangıç Tarihi" />
            </div>
        </div>

        <!-- Bitiş Tarihi -->
        <div style="flex: 1; min-width: 200px;">
            <label for="date_end_picker" class="sr-only">Bitiş Tarihi</label>
            <div class="input-group date" id="date_end_picker" data-target-input="nearest">
                <input type="text"
                       id="date_end"
                       class="form-control datetimepicker-input"
                       data-toggle="datetimepicker"
                       data-target="#date_end_picker"
                       placeholder="Bitiş Tarihi"/>
            </div>
        </div>

        <!-- Butonlar (Sıra: Grafik, Tablo, Pie Chart, Fark, Refresh) -->
        <div style="display: flex; gap: 10px;">
            <!-- 1. Grafik Butonu -->
            <button class="btn btn-primary" id="graphButton" title="Son 24 saat grafiği">
                <i class="fa fa-chart-line"></i>
            </button>
            <!-- 2. Tablo Butonu -->
            <button class="btn btn-primary" id="tableButton" title="Saatlik Günlük Aylık Tablolar">
                <i class="fa fa-table"></i>
            </button>
            <!-- 3. Pie Chart Butonu -->
            <button class="btn btn-primary" id="pieChartButton" title="Elektrik Su Doğalgaz Metraj chartları">
                <i class="fa fa-chart-pie"></i>
            </button>
            <!-- 4. Fark Butonu -->
            <button class="btn btn-warning" id="differenceButton" title="İki tarih arasındaki fark veri">
                <i class="fa fa-arrows-alt"></i>
            </button>
            <!-- 5. Refresh Butonu -->
            <button class="btn btn-secondary" id="refreshButton" title="Yenile">
                <i class="fa fa-refresh"></i>
            </button>
        </div>
    </div>
</div>

<!-- Pie Container -->
<div id="pieContainer" style="margin-top: 20px; width: 100%; overflow-x: auto;"></div>

<!-- Table Container -->
<div id="tableContainer" style="margin-top: 20px; width: 100%; overflow-x: auto;"></div>

<!-- Chart Container -->
<div id="chartContainer" style="width: 100%; height: 400px; margin-top: 20px;"></div>
@endsection

@section('javascript')
{{-- Multiselect, FontAwesome, TempusDominus (Bootstrap 4) CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" />

{{-- Eğer projenizde jQuery, Bootstrap 4 ve Moment.js zaten yüklüyse bu satırları iptal edebilirsiniz.
     Aksi halde en azından şu sıralamayla yüklendiğinden emin olun:
     <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>

<style>
    .row {
        display: flex;
        gap: 10px;
    }
    .section-header {
        margin-top: 20px;
        font-weight: bold;
    }
    /* Donut Chart Stil Ayarları */
    #chartContainer {
        margin-top: -20px !important; /* Yukarı çek */
        display: flex;
        justify-content: space-between;
        flex-wrap: nowrap;
        gap: 20px;
    }
    .chart-box {
        width: 24%; /* 4 donut yan yana */
        text-align: center;
    }
    .chart-box h5 {
        margin-top: 0px;      /* Üstten boşluk */
        margin-bottom: 10px;  /* Etiketle chart arasında 10px boşluk */
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }
    .chart-canvas {
        width: 100%;
        height: 280px;
        margin: 0 auto;
        margin-top: 0px; /* Donut'u 10px daha yukarı al (önceden 10px idi, şimdi 0) */
    }
    /* Datetimepicker pop-up boyut ve font ayarları */
    .bootstrap-datetimepicker-widget {
        min-width: 250px;
        font-size: 13px;
    }
    .bootstrap-datetimepicker-widget table td.day,
    .bootstrap-datetimepicker-widget table td.hour,
    .bootstrap-datetimepicker-widget table td.minute,
    .bootstrap-datetimepicker-widget table td.second {
        width: 30px;
        height: 30px;
        font-size: 13px;
    }
</style>

<script type="text/javascript">
$(document).ready(function() {
    // 1) DateTimePicker Ayarları
    $('#date_start_picker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        widgetPositioning: { horizontal: 'auto', vertical: 'bottom' },
        icons: {
            time: 'fa fa-clock',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-calendar-check',
            clear: 'fa fa-trash',
            close: 'fa fa-times'
        }
    });
    $('#date_end_picker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        widgetPositioning: { horizontal: 'auto', vertical: 'bottom' },
        icons: {
            time: 'fa fa-clock',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-calendar-check',
            clear: 'fa fa-trash',
            close: 'fa fa-times'
        }
    });

    // 2) Dropdown Ayarları (Bölüm, Cihaz)
    let fieldDropdown = $('#fieldDropdown');
    let deviceDropdown = $('#device');

    // Bölümleri listelemek için AJAX
    $.ajax({
        url: '/get-fields',
        type: 'GET',
        success: function (fields) {
            fields.forEach(field => {
                fieldDropdown.append(`<option value="${field.field_name}">${field.name}</option>`);
            });
            fieldDropdown.multiselect('rebuild');
        },
        error: function (error) {
            console.error('Bölümler yüklenirken bir hata oluştu:', error);
        }
    });

    fieldDropdown.multiselect({
        nonSelectedText: "Bölüm Seçin",
        buttonWidth: '100%',
        enableFiltering: true,
        filterPlaceholder: 'Arama',
        maxHeight: 300,
        includeSelectAllOption: false,
        templates: { filterClearBtn: '' }
    });

    deviceDropdown.multiselect({
        nonSelectedText: "Cihaz Seçin",
        buttonWidth: '100%',
        enableFiltering: true,
        filterPlaceholder: 'Arama',
        maxHeight: 300,
        includeSelectAllOption: false,
        templates: { filterClearBtn: '' }
    });

    $.ajax({
        url: '/get-devices',
        type: 'GET',
        success: function (devices) {
            deviceDropdown.empty();
            devices.forEach(device => {
                deviceDropdown.append(`<option value="${device.id}">${device.name}</option>`);
            });
            deviceDropdown.multiselect('rebuild');
        },
        error: function (error) {
            console.error('Cihazlar yüklenirken hata:', error);
        }
    });

    // 3) ECharts Nesnesi (grafikler için)
    var devices = @json($devices ?? []);
    let chart = echarts.init(document.getElementById('chartContainer'));

    // 4) PIE CHART Butonu (Tarih Aralığına Göre Toplam)
    $('#pieChartButton').on('click', async function (e) {
        e.preventDefault();
        const selectedDevices = $('#device').val();
        if (!selectedDevices || selectedDevices.length === 0) {
            alert('Lütfen bir cihaz seçin.');
            return;
        }
        const startDate = $('#date_start').val();
        const endDate = $('#date_end').val();
        if (!startDate || !endDate) {
            alert('Lütfen tarih aralığı seçin.');
            return;
        }

        // Donut chart'ları #chartContainer'da göster
        $('#chartContainer').empty();
        $('#chartContainer').css('display', 'flex');

        const chartTitles = ['Elektrik', 'Su', 'Doğalgaz', 'Metraj'];
        const deviceData = { Elektrik: [], Su: [], Doğalgaz: [], Metraj: [] };

        for (let category of chartTitles) {
            for (let deviceId of selectedDevices) {
                const devObj = devices.find(d => d.id == deviceId);
                if(!devObj || !devObj.tags) {
                    deviceData[category].push({ value: 0, name: category });
                    continue;
                }
                const tagEntry = Object.entries(devObj.tags).find(([k,v]) => v.includes(category));
                if(!tagEntry) {
                    deviceData[category].push({ value: 0, name: category });
                    continue;
                }
                const tagId = tagEntry[0];
                const tagName = tagEntry[1];
                let sumValue = 0;
                try {
                    const response = await $.ajax({
                        url: '/get-sum-for-tag',
                        type: 'GET',
                        data: {
                            device_id: deviceId,
                            tag_id: tagId,
                            start_date: startDate,
                            end_date: endDate
                        }
                    });
                    sumValue = response.sum || 0;
                } catch (err) {
                    console.error(`Toplam değer alınırken hata: ${category}`, err);
                }
                deviceData[category].push({ value: sumValue, name: tagName });
            }
        }

        for (let i = 0; i < chartTitles.length; i++) {
            const category = chartTitles[i];
            $('#chartContainer').append(`
                <div class="chart-box">
                    <h5>${category}</h5>
                    <div id="pieChart${i}" class="chart-canvas"></div>
                </div>
            `);

            const pieChart = echarts.init(document.getElementById(`pieChart${i}`));
            const option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b}: {c} ({d}%)'
                },
                legend: {
                    orient: 'horizontal',
                    bottom: '10%',
                    left: 'center',
                    data: deviceData[category].map(data => data.name),
                    textStyle: { fontSize: 12 }
                },
                series: [
                    {
                        name: 'Cihazlar',
                        type: 'pie',
                        radius: ['40%', '70%'],
                        avoidLabelOverlap: false,
                        itemStyle: {
                            borderRadius: 10,
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        label: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            label: {
                                show: true,
                                fontSize: '16',
                                fontWeight: 'bold'
                            }
                        },
                        labelLine: { show: false },
                        data: deviceData[category]
                    }
                ]
            };
            pieChart.setOption(option);
        }
    });

    // 5) Grafik Butonu (Saatlik Veriler)
    $('#graphButton').on('click', async function(e) {
        e.preventDefault();
        $('#tableContainer').empty();
        chart = echarts.init(document.getElementById('chartContainer'));
        const deviceId = $('#device').val() ? $('#device').val()[0] : null;
        if (!deviceId) {
            alert('Lütfen bir cihaz seçin.');
            return;
        }
        let selectedDevice = devices.find(device => device.id == deviceId);
        if (!selectedDevice || !selectedDevice.tags) {
            console.error("Seçilen cihaza ait tag bulunamadı.");
            alert('Seçilen cihaza ait etiket yok.');
            return;
        }
        const hourlyTagIds = Object.entries(selectedDevice.tags)
            .filter(([key, tagName]) => tagName.includes('Saatlik'))
            .map(([key]) => key);
        if (hourlyTagIds.length === 0) {
            alert('Bu cihaza ait saatlik etiket bulunamadı.');
            return;
        }
        const now = new Date();
        const offset = 3 * 60 * 60 * 1000;
        const endOfCurrentDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0).getTime() + offset;
        const startOfPreviousDay = endOfCurrentDay - 24 * 60 * 60 * 1000;
        let seriesData = [];
        let chartDates = [];

        for (const tagId of hourlyTagIds) {
            try {
                const response = await $.ajax({
                    url: '/get-filtered-data',
                    type: 'GET',
                    data: {
                        device_id: deviceId,
                        data_id: tagId,
                        start_date: new Date(startOfPreviousDay).toISOString(),
                        end_date: new Date(endOfCurrentDay).toISOString()
                    }
                });
                const data = response.data;
                if (data.length > 0) {
                    chartDates = data.map(entry => entry.created_at);
                    const values = data.map(entry => entry.value);
                    seriesData.push({
                        name: selectedDevice.tags[tagId],
                        type: 'bar',
                        data: values
                    });
                }
            } catch (error) {
                console.error(`Saatlik veri alınırken hata: ${tagId}`, error);
            }
        }

        chart.clear();
        renderChart(seriesData, chartDates);
    });

    // 6) Tablo Butonu (Günlük, Haftalık, Aylık Veriler)
    $('#tableButton').on('click', async function(e) {
        e.preventDefault();
        await fetchTableDataAndRender();
    });

    // 7) Fark Butonu - İki tarih arasındaki farkı hesapla ve göster
    $('#differenceButton').on('click', function(e) {
        e.preventDefault();
        const startDateStr = $('#date_start').val();
        const endDateStr = $('#date_end').val();
        if (!startDateStr || !endDateStr) {
            alert('Lütfen iki tarih de seçin.');
            return;
        }
        const startDate = new Date(startDateStr);
        const endDate = new Date(endDateStr);
        const diffMs = endDate - startDate;
        if(diffMs < 0){
            if(!$('#farkContainer').length){
                $('body').append('<div id="farkContainer"></div>');
            }
            $('#farkContainer').html('<p>Başlangıç tarihi, bitiş tarihinden sonra olamaz.</p>');
            return;
        }
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        const diffHrs = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
        const diffSecs = Math.floor((diffMs % (1000 * 60)) / 1000);
        const diffText = `<p>İki tarih arasındaki fark: ${diffDays} gün, ${diffHrs} saat, ${diffMins} dakika, ${diffSecs} saniye.</p>`;
        if(!$('#farkContainer').length){
            $('body').append('<div id="farkContainer"></div>');
        }
        $('#farkContainer').html(diffText);
    });

    // 8) Refresh Butonu - Alanları temizle
    $('#refreshButton').on('click', function(e) {
        e.preventDefault();
        $('#pieContainer').empty();
        $('#tableContainer').empty();
        $('#chartContainer').empty();
        $('#farkContainer').empty();
    });

    // 9) Saatlik Verilerle Grafik (fetchTableDataAndRender)
    async function fetchTableDataAndRender() {
        $('#tableContainer').empty();
        chart.clear();
        const deviceId = $('#device').val() ? $('#device').val()[0] : null;
        if (!deviceId) {
            alert('Lütfen bir cihaz seçin.');
            return;
        }
        let selectedDevice = devices.find(device => device.id == deviceId);
        if (!selectedDevice || !selectedDevice.tags) {
            console.error("Seçilen cihaza ait tag bulunamadı.");
            alert('Seçilen cihaza ait etiket yok.');
            return;
        }
        const dailyTagIds = Object.entries(selectedDevice.tags)
            .filter(([key, tagName]) => tagName.includes('Günlük'))
            .map(([key]) => key);
        const monthlyTagIds = Object.entries(selectedDevice.tags)
            .filter(([key, tagName]) => tagName.includes('Aylık'))
            .map(([key]) => key);

        const now = new Date();
        const offset = 3 * 60 * 60 * 1000;
        const endOfCurrentDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0).getTime() + offset;

        let tableData = [];
        let weeklyTableData = [];
        let monthlyTableData = [];

        // Günlük veriler (son 7 gün)
        const startOf7DaysAgo = endOfCurrentDay - 7 * 24 * 60 * 60 * 1000;
        for (const tagId of dailyTagIds) {
            try {
                const response = await $.ajax({
                    url: '/get-filtered-data',
                    type: 'GET',
                    data: {
                        device_id: deviceId,
                        data_id: tagId,
                        start_date: new Date(startOf7DaysAgo).toISOString(),
                        end_date: new Date(endOfCurrentDay).toISOString()
                    }
                });
                const data = response.data;
                if (data.length > 0) {
                    const uniqueDates = [...new Set(data.map(entry => entry.created_at.split(' ')[0]))];
                    uniqueDates.forEach(date => {
                        const dailyData = data.find(entry => entry.created_at.startsWith(date));
                        tableData.push({
                            tag_name: selectedDevice.tags[tagId],
                            created_at: date,
                            value: dailyData ? dailyData.value : 0
                        });
                    });
                }
            } catch (error) {
                console.error(`Günlük veri hatası: ${tagId}`, error);
            }
        }

        // Haftalık veriler (günlük taglardan hesaplanır - son 4 hafta)
        const weeklyTagIds = dailyTagIds;
        const startOf4WeeksAgo = endOfCurrentDay - 28 * 24 * 60 * 60 * 1000;
        for (const tagId of weeklyTagIds) {
            try {
                const response = await $.ajax({
                    url: '/get-filtered-data',
                    type: 'GET',
                    data: {
                        device_id: deviceId,
                        data_id: tagId,
                        start_date: new Date(startOf4WeeksAgo).toISOString(),
                        end_date: new Date(endOfCurrentDay).toISOString()
                    }
                });
                const data = response.data || [];
                if (data.length > 0) {
                    const weeklyData = [];
                    for (let i = 0; i < 4; i++) {
                        const weekStart = new Date(endOfCurrentDay - (i + 1) * 7 * 24 * 60 * 60 * 1000);
                        const weekEnd = new Date(endOfCurrentDay - i * 7 * 24 * 60 * 60 * 1000);
                        const weekRange = `${weekStart.toISOString().split('T')[0]} - ${weekEnd.toISOString().split('T')[0]}`;
                        const weeklySum = data
                            .filter(entry => new Date(entry.created_at) >= weekStart && new Date(entry.created_at) < weekEnd)
                            .reduce((sum, entry) => sum + (entry.value || 0), 0);
                        weeklyData.push({
                            week: weekRange,
                            value: weeklySum
                        });
                    }
                    weeklyTableData.push({
                        tag_name: selectedDevice.tags[tagId].replace('Günlük', 'Haftalık'),
                        weeklyData
                    });
                }
            } catch (error) {
                console.error(`Haftalık veri hatası: ${tagId}`, error);
            }
        }

        // Aylık veriler (son 3 ay)
        for (const tagId of monthlyTagIds) {
            try {
                const startOf3MonthsAgo = new Date(now.getFullYear(), now.getMonth() - 2, 1);
                const endOfCurrentMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);

                const response = await $.ajax({
                    url: '/get-filtered-data',
                    type: 'GET',
                    data: {
                        device_id: deviceId,
                        data_id: tagId,
                        start_date: startOf3MonthsAgo.toISOString(),
                        end_date: endOfCurrentMonth.toISOString()
                    }
                });
                const data = response.data || [];
                if (data.length > 0) {
                    const monthlyData = [];
                    for (let i = 2; i >= 0; i--) {
                        const monthStart = new Date(now.getFullYear(), now.getMonth() - i, 1);
                        const monthEnd = new Date(now.getFullYear(), now.getMonth() - i + 1, 0);
                        const monthRange = `${monthStart.toISOString().split('T')[0]} - ${monthEnd.toISOString().split('T')[0]}`;
                        const monthlySum = data
                            .filter(entry => new Date(entry.created_at) >= monthStart && new Date(entry.created_at) <= monthEnd)
                            .reduce((sum, entry) => sum + parseFloat(entry.value || 0), 0);
                        monthlyData.push({
                            month: monthRange,
                            value: monthlySum
                        });
                    }
                    monthlyTableData.push({
                        tag_name: selectedDevice.tags[tagId],
                        monthlyData
                    });
                }
            } catch (error) {
                console.error(`Aylık veri hatası: ${tagId}`, error);
            }
        }

        renderTable(tableData, tableData.map(d => d.created_at).slice(-7));
        renderWeeklyTable(weeklyTableData);
        renderMonthlyTable(monthlyTableData);
    }

    // 10) ECharts ile Bar Grafiği Çizen Fonksiyon
    function renderChart(seriesData, dates) {
        const option = {
            legend: {
                data: seriesData.map(series => series.name),
                top: '2%'
            },
            tooltip: {
                trigger: 'axis',
                formatter: function(params) {
                    let tooltipText = `${params[0].axisValue}<br/>`;
                    params.forEach(param => {
                        tooltipText += `${param.marker} ${param.seriesName}: ${param.value}<br/>`;
                    });
                    return tooltipText;
                }
            },
            grid: {
                top: '20%',
                left: '10%',
                right: '10%',
                bottom: '10%'
            },
            xAxis: {
                type: 'category',
                data: dates,
                axisLabel: {
                    formatter: function(value) {
                        let date = new Date(value);
                        return `${date.getHours()}:00`;
                    }
                }
            },
            yAxis: {
                type: 'value'
            },
            series: seriesData
        };
        chart.setOption(option);
    }

    // 11) Günlük Tabloyu Çizen Fonksiyon
    function renderTable(tagData, uniqueDates) {
        $('#tableContainer').append('<h4 class="section-header">Günlük Veriler</h4>');

        if (!tagData || tagData.length === 0) {
            $('#tableContainer').append('<p>Seçilen cihaza ait günlük veri bulunamadı.</p>');
            return;
        }

        uniqueDates = uniqueDates.slice(-7);

        let groupedData = tagData.reduce((acc, entry) => {
            if (!acc[entry.tag_name]) {
                acc[entry.tag_name] = {};
            }
            acc[entry.tag_name][entry.created_at] = entry.value;
            return acc;
        }, {});

        let table = `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Etiket İsmi</th>
                        ${uniqueDates.map(date => `<th>${date}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
        `;

        for (let tagName in groupedData) {
            table += `<tr><td>${tagName}</td>`;
            uniqueDates.forEach(date => {
                table += `<td>${groupedData[tagName][date] || '0.000'}</td>`;
            });
            table += `</tr>`;
        }

        table += `
                </tbody>
            </table>
        `;

        $('#tableContainer').append(table);
    }

    // 12) Haftalık Tabloyu Çizen Fonksiyon
    function renderWeeklyTable(weeklyTableData) {
        $('#tableContainer').append('<h4 class="section-header">Haftalık Veriler</h4>');

        if (!weeklyTableData || weeklyTableData.length === 0) {
            $('#tableContainer').append('<p>Haftalık veri bulunamadı.</p>');
            return;
        }

        let weekRanges = weeklyTableData[0].weeklyData.map(week => week.week.split(' - ')[0]);

        let table = `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Etiket İsmi</th>
                        ${weekRanges.map(range => `<th>${range}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
        `;

        weeklyTableData.forEach(row => {
            table += `<tr><td>${row.tag_name}</td>`;
            row.weeklyData.forEach(week => {
                const value = parseFloat(week.value) || 0;
                table += `<td>${value.toFixed(3)}</td>`;
            });
            table += `</tr>`;
        });

        table += `
                </tbody>
            </table>
        `;

        $('#tableContainer').append(table);
    }

    // 13) Aylık Tabloyu Çizen Fonksiyon
    function renderMonthlyTable(monthlyTableData) {
        $('#tableContainer').append('<h4 class="section-header">Aylık Veriler</h4>');

        if (!monthlyTableData || monthlyTableData.length === 0) {
            $('#tableContainer').append('<p>Aylık veri bulunamadı.</p>');
            return;
        }

        let monthRanges = monthlyTableData[0].monthlyData.map(month => month.month.split(' - ')[0]);

        let table = `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Etiket İsmi</th>
                        ${monthRanges.map(range => `<th>${range}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
        `;

        monthlyTableData.forEach(row => {
            table += `<tr><td>${row.tag_name}</td>`;
            row.monthlyData.forEach(month => {
                table += `<td>${month.value.toFixed(3)}</td>`;
            });
            table += `</tr>`;
        });

        table += `
                </tbody>
            </table>
        `;

        $('#tableContainer').append(table);
    }
});
</script>
@endsection
