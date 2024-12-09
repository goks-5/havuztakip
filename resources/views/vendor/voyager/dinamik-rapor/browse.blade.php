@extends('voyager::master')

@section('content')
<div class="container-fluid">
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
        <div class="input-group date" id="date_start_picker" data-target-input="nearest" style="flex: 1; min-width: 200px;">
            <input type="text" id="date_start" class="form-control datetimepicker-input" data-target="#date_start_picker" placeholder="Başlangıç Tarihi" />
            <div class="input-group-append" data-target="#date_start_picker" data-toggle="datetimepicker">
                <div class="input-group-text"><i class=""></i></div>
            </div>
        </div>

        <!-- Bitiş Tarihi -->
        <div class="input-group date" id="date_end_picker" data-target-input="nearest" style="flex: 1; min-width: 200px;">
            <input type="text" id="date_end" class="form-control datetimepicker-input" data-target="#date_end_picker" placeholder="Bitiş Tarihi"/>
            <div class="input-group-append" data-target="#date_end_picker" data-toggle="datetimepicker">
                <div class="input-group-text"><i class=""></i></div>
            </div>
        </div>

        <!-- Butonlar -->
        <div style="display: flex; gap: 10px;">
            <!-- Pie Chart Butonu -->
            <button class="btn btn-primary" id="pieChartButton">
                <i class="fa fa-chart-pie"></i>
            </button>
            <!-- Grafik Butonu -->
            <button class="btn btn-primary" id="graphButton">
                <i class="fa fa-chart-line"></i>
            </button>
            <!-- Tablo Butonu -->
            <button class="btn btn-primary" id="tableButton">
                <i class="fa fa-table"></i>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" />
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
    #chartContainer {
        margin-top: -20px !important; /* Zorunlu olarak yukarı çek */
    display: flex;
    justify-content: space-between;
    flex-wrap: nowrap;
    gap: 20px;
    }

    .chart-box {
        width: 24%; /* Her pie chart için sabit genişlik */
        text-align: center; /* Ortala */
    }

    .chart-box h5 {
    margin-bottom: 5px; /* Alt kenar boşluğunu artırın */
    margin-top: 10px; /* Üst kenar boşluğu ekleyin (isteğe bağlı) */
    font-size: 16px;
    font-weight: bold;
    text-align: center; /* Başlıkları ortalayın */
    }

    .chart-canvas {
        width: 100%;
        height: 280px; /* Chart boyutunu düzenle */
        margin: 0 auto;
        margin-top: -10px; /* Chart'ı başlığa yaklaştır */
    }
</style>

<script type="text/javascript">
$(document).ready(function() {
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

    // fieldDropdown ayarları
    fieldDropdown.multiselect({
        nonSelectedText: "Bölüm Seçin",
        buttonWidth: '100%',
        enableFiltering: true,
        filterPlaceholder: 'Arama',
        maxHeight: 300,
        includeSelectAllOption: false,
        templates: {
            filterClearBtn: ''
        }
    });

    deviceDropdown.multiselect({
        nonSelectedText: "Cihaz Seçin",
        buttonWidth: '100%',
        enableFiltering: true,
        filterPlaceholder: 'Arama',
        maxHeight: 300,
        includeSelectAllOption: false,
        templates: {
            filterClearBtn: ''
        }
    });

    // Sayfa ilk yüklendiğinde tüm cihazları yükle
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
            console.error('Cihazlar başlangıçta yüklenirken hata:', error);
        }
    });

    $('.datetimepicker').datetimepicker({
    format: 'YYYY-MM-DD HH:mm',
});

$('#pieChartButton').on('click', function (e) {
    e.preventDefault();

    // Seçilen cihazları al
    const selectedDevices = $('#device').val();
    if (!selectedDevices || selectedDevices.length === 0) {
        alert('Lütfen bir cihaz seçin.');
        return;
    }

    // Önce eski içeriği temizle
    $('#chartContainer').empty();

    // Yeni pie chart'lar için container oluştur
    $('#chartContainer').css('display', 'flex');

    // Backend'den cihaz verilerini al (örnek JSON)
    const deviceData = {
        Elektrik: selectedDevices.map(device => ({ value: Math.random() * 1000, name: devices.find(d => d.id === device)?.name || device })),
        Su: selectedDevices.map(device => ({ value: Math.random() * 1000, name: devices.find(d => d.id === device)?.name || device })),
        Doğalgaz: selectedDevices.map(device => ({ value: Math.random() * 1000, name: devices.find(d => d.id === device)?.name || device })),
        Metraj: selectedDevices.map(device => ({ value: Math.random() * 1000, name: devices.find(d => d.id === device)?.name || device })),
    };

    // Chart başlıkları
    const chartTitles = ['Elektrik', 'Su', 'Doğalgaz', 'Metraj'];

    for (let i = 0; i < 4; i++) {
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
                formatter: '{a} <br/>{b}: {c} ({d}%)',
            },
            legend: {
                orient: 'horizontal',
                bottom: '10%',
                left: 'center',
                data: deviceData[category].map(data => data.name),
                textStyle: {
                    fontSize: 12,
                },
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
                        borderWidth: 2,
                    },
                    label: {
                        show: false,
                        position: 'center',
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '16',
                            fontWeight: 'bold',
                        },
                    },
                    labelLine: {
                        show: false,
                    },
                    data: deviceData[category],
                },
            ],
        };

        pieChart.setOption(option);
    }
});

    // Backend'den devices bilgisinin geldiğini varsayıyoruz
    var devices = @json($devices ?? []);
    let chart = echarts.init(document.getElementById('chartContainer'));

    // Sadece grafiği çeken fonksiyon (saatlik veriler)
    async function fetchChartDataAndRender() {
        $('#tableContainer').empty(); // Tablo alanını temizle (sadece grafik gösterilecek)

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
        const offset = 3 * 60 * 60 * 1000; // UTC+3
        const endOfCurrentDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0).getTime() + offset;
        const startOfPreviousDay = endOfCurrentDay - 24 * 60 * 60 * 1000;

        let seriesData = [];
        let chartDates = [];

        // Saatlik veriler için AJAX istekleri
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

        // Grafiği çiz
        chart.clear();
        renderChart(seriesData, chartDates);
    }

    // Sadece tabloları çeken fonksiyon (günlük, haftalık, aylık veriler)
    async function fetchTableDataAndRender() {
        $('#tableContainer').empty(); // Tablo alanını temizle
        chart.clear(); // Grafik alanını temizle (sadece tablo gösterilecek)

        const deviceId = $('#device').val() ? $('#device').val()[0] : null;
        if (!deviceId) {
            alert('Lütfen bir cihaz seçin.');
            return;
        }

        let selectedDevice = devices.find(device => device.id == deviceId);
        if (!selectedDevice || !selectedDevice.tags) {
            console.error("Seçilen cihaza ait tag bulunamadı.");
            alert('Seçilen cihaza ait etike yok.');
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

        // Günlük veriler
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
                console.error(`Günlük veri alınırken hata: ${tagId}`, error);
            }
        }

        // Haftalık veriler (günlük taglardan hesaplanır)
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
                console.error(`Haftalık veri alınırken hata: ${tagId}`, error);
            }
        }

        // Aylık veriler
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
                console.error(`Aylık veri alınırken hata: ${tagId}`, error);
            }
        }

        // Tablo çizimleri
        renderTable(tableData, tableData.map(d => d.created_at).slice(-7));
        renderWeeklyTable(weeklyTableData);
        renderMonthlyTable(monthlyTableData);
    }

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

    $('#pieChartButton').on('click', function(e) {
        e.preventDefault();
        fetchChartDataAndRender();
    });

    // Grafik butonuna tıklandığında sadece grafik verilerini çekip göster
    $('#graphButton').on('click', function(e) {
        e.preventDefault();
        fetchChartDataAndRender();
    });

    // Tablo butonuna tıklandığında sadece tablo verilerini çekip göster
    $('#tableButton').on('click', function(e) {
        e.preventDefault();
        fetchTableDataAndRender();
    });
});
</script>
@endsection
