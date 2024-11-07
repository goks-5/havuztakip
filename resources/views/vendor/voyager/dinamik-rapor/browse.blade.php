@extends('voyager::master')

@section('content')
    <div class="container">
        <!-- Inline-flex layout for selection controls -->
        <div style="display: inline-flex; align-items: center; gap: 15px; margin-top: 20px;">
            <div>
                <label for="device" class="sr-only">Cihaz</label>
                <select class="form-control" id="device" multiple="multiple" style="width: 200px;">
                </select>
            </div>

            <div>
                <label for="date_start" class="sr-only">Başlangıç Tarihi</label>
                <input type="text" class="form-control datetimepicker" id="date_start" placeholder="Başlangıç Tarihi" style="width: 200px;">
            </div>

            <div>
                <label for="date_end" class="sr-only">Bitiş Tarihi</label>
                <input type="text" class="form-control datetimepicker" id="date_end" placeholder="Bitiş Tarihi" style="width: 200px;">
            </div>

            <button class="btn btn-primary" id="searchButton" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 38px;">
                <i class="voyager-search"></i>
            </button>
            <button class="btn btn-secondary" id="compareButton" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 38px; margin-left: 10px;">
                <i class="fa fa-balance-scale"></i> <!-- Example balance scale icon -->
            </button>
        </div>

        
        <div id="gaugeContainer" style="display: flex; justify-content: space-around; margin-top: 20px;">
    <div id="gauge1" style="width: 30%; height: 200px;"></div>
    <div id="gauge2" style="width: 30%; height: 200px;"></div>
    <div id="gauge3" style="width: 30%; height: 200px;"></div>
</div>
        <!-- Chart Container -->
        <div style="display: flex; gap: 20px; margin-top: 20px;">
            
            <div id="chartContainer" style="height: 500px; width: 100%;"></div>
        </div>

        <!-- Table Container -->
        <div id="tableContainer" style="margin-top: 20px; width: 100%; overflow-x: auto;"></div>
    </div>
@endsection

@section('javascript')
<style>
    #dateNavigation {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #currentDate {
        display: inline-block;
        text-align: center;
    }
</style>

<!-- Include ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

<script type="text/javascript">
   $(document).ready(function() {
        // Initialize the datetime picker and device dropdown
        $('.datetimepicker').datetimepicker({
            format: 'Y-MM-DD HH:mm',
        });

        $('#device').multiselect({
            nonSelectedText: "Cihaz Seçin",
            buttonWidth: '200px',
            includeSelectAllOption: true,
            selectAllText: "Tümünü Seç",
            enableFiltering: true,
            filterPlaceholder: 'Arama',
            templates: {
                filterClearBtn: '',
                filterIcon: ''
            }
        });

        var devices = @json($devices); // Load devices with tags from backend
        let deviceDropdown = $('#device');

        devices.forEach(device => {
            deviceDropdown.append(`<option value="${device.id}">${device.name}</option>`);
        });
        deviceDropdown.multiselect('rebuild');

        let chart = echarts.init(document.getElementById('chartContainer'));

        // Fetch data for both hourly (chart) and daily (table)
        async function fetchDataAndRender() {
    const deviceId = $('#device').val() ? $('#device').val()[0] : null;

    if (!deviceId) {
        alert('Please select a device.');
        return;
    }

    let selectedDevice = devices.find(device => device.id == deviceId);
    if (!selectedDevice || !selectedDevice.tags) {
        console.error("No tags found for the selected device.");
        alert('The selected device has no tags.');
        return;
    }

    const hourlyTagIds = Object.entries(selectedDevice.tags)
        .filter(([key, tagName]) => tagName.includes('Saatlik'))
        .map(([key]) => key);

    const dailyTagIds = Object.entries(selectedDevice.tags)
        .filter(([key, tagName]) => tagName.includes('Günlük'))
        .map(([key]) => key);

    const weeklyTagIds = dailyTagIds; // Use daily tags to calculate weekly data

    if (hourlyTagIds.length === 0) {
        alert('No hourly tags ("Saatlik") found for the selected device.');
    }

    if (dailyTagIds.length === 0) {
        alert('No daily tags ("Günlük") found for the selected device.');
    }
    const monthlyTagIds = Object.entries(selectedDevice.tags)
        .filter(([key, tagName]) => tagName.includes('Aylık'))
        .map(([key]) => key);

    if (monthlyTagIds.length === 0) {
        alert('No monthly tags ("Aylık") found for the selected device.');
    }

    
    const seriesData = [];
    let chartDates = [];
    let tableData = []; // Daily table
    let weeklyTableData = []; // Weekly table
    let monthlyTableData = []; // Aylık tablo verileri
    let gaugeData = []; // Gauge data

    const now = new Date();
    const offset = 3 * 60 * 60 * 1000; // UTC+3
    const endOfCurrentDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0).getTime() + offset;
    const startOfPreviousDay = endOfCurrentDay - 24 * 60 * 60 * 1000;

    try {
        const gaugeResponse = await $.ajax({
            url: '/get-gauge-data',
            type: 'GET',
            data: { device_id: deviceId },
        });

        gaugeData = gaugeResponse.data || [0, 0, 0]; // Default gauge values
        initializeGauges(gaugeData); // Call the function to initialize gauges
    } catch (error) {
        console.error('Error fetching gauge data:', error);
        alert('Failed to fetch gauge data.');
        gaugeData = [0, 0, 0]; // Default to zero gauges in case of error
        initializeGauges(gaugeData); // Ensure gauges are rendered even on error
    }

    // Fetch hourly data for chart
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
                    type: 'line',
                    data: values
                });
            }
        } catch (error) {
            console.error(`Error fetching hourly data for tag: ${tagId}`, error);
        }
    }

    // Fetch daily data for table
    for (const tagId of dailyTagIds) {
        try {
            const startOf7DaysAgo = endOfCurrentDay - 7 * 24 * 60 * 60 * 1000;

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
            console.error(`Error fetching daily data for tag: ${tagId}`, error);
        }
    }

    // Fetch weekly data for the last 4 weeks
   // Fetch weekly data for the last 4 weeks
   for (const tagId of weeklyTagIds) {
    try {
        const startOf4WeeksAgo = endOfCurrentDay - 28 * 24 * 60 * 60 * 1000; // 4 hafta önce

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

        const data = response.data || []; // Cevap yoksa boş dizi döndür
        if (data.length > 0) {
            const weeklyData = [];
            for (let i = 0; i < 4; i++) {
                const weekStart = new Date(endOfCurrentDay - (i + 1) * 7 * 24 * 60 * 60 * 1000);
                const weekEnd = new Date(endOfCurrentDay - i * 7 * 24 * 60 * 60 * 1000);
                const weekRange = `${weekStart.toISOString().split('T')[0]} - ${weekEnd.toISOString().split('T')[0]}`;

                const weeklySum = data
                    .filter(entry => new Date(entry.created_at) >= weekStart && new Date(entry.created_at) < weekEnd)
                    .reduce((sum, entry) => sum + (entry.value || 0), 0); // `undefined` ise 0 ekle

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
        console.error(`Error fetching weekly data for tag: ${tagId}`, error);
    }
}

for (const tagId of monthlyTagIds) {
    try {
        const startOf3MonthsAgo = new Date(now.getFullYear(), now.getMonth() - 2, 1); // Start of 3 months ago
        const endOfCurrentMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0); // End of current month

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
            for (let i = 2; i >= 0; i--) { // Last 3 months
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
    tag_name: selectedDevice.tags[tagId], // Keep the original tag name
    monthlyData
});

        }
    } catch (error) {
        console.error(`Error fetching monthly data for tag: ${tagId}`, error);
    }
}



chart.clear();
renderChart(seriesData, chartDates); // Saatlik veri grafiği
renderTable(tableData, tableData.map(d => d.created_at).slice(-7)); // Günlük veri tablosu
renderWeeklyTable(weeklyTableData); // Haftalık veri tablosu
renderMonthlyTable(monthlyTableData);
}

function initializeGauges(gaugeData) {
        const gauge1 = echarts.init(document.getElementById('gauge1'));
        const gauge2 = echarts.init(document.getElementById('gauge2'));
        const gauge3 = echarts.init(document.getElementById('gauge3'));

        const gaugeOption = (title, value) => ({
            title: {
                text: title,
                left: 'center',
                top: '80%',
                textStyle: {
                    fontSize: 14,
                    fontWeight: 'bold',
                },
            },
            series: [
                {
                    type: 'gauge',
                    startAngle: 90,
                    endAngle: -270,
                    pointer: {
                        show: false,
                    },
                    progress: {
                        show: true,
                        width: 18,
                    },
                    axisLine: {
                        lineStyle: {
                            width: 18,
                        },
                    },
                    axisTick: {
                        show: false,
                    },
                    splitLine: {
                        show: false,
                    },
                    axisLabel: {
                        show: false,
                    },
                    data: [
                        {
                            value: value,
                            name: '',
                        },
                    ],
                    detail: {
                        valueAnimation: true,
                        formatter: '{value}%',
                        fontSize: 16,
                        offsetCenter: [0, '70%'],
                    },
                },
            ],
        });

        gauge1.setOption(gaugeOption('Elektrik', gaugeData[0]));
        gauge2.setOption(gaugeOption('Doğalgaz', gaugeData[1]));
        gauge3.setOption(gaugeOption('Su', gaugeData[2]));
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
                    return `${date.getHours()}:00`; // Show hourly format
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
        $('#tableContainer').append('<p>No data available for the selected device.</p>');
        return;
    }

    uniqueDates = uniqueDates.slice(-7); // Show only the last 7 days

    // Group data by tag name
    let groupedData = tagData.reduce((acc, entry) => {
        if (!acc[entry.tag_name]) {
            acc[entry.tag_name] = {};
        }
        acc[entry.tag_name][entry.created_at] = entry.value;
        return acc;
    }, {});

    // Create table headers
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

    // Populate table rows
    for (let tagName in groupedData) {
        table += `<tr><td>${tagName}</td>`;
        uniqueDates.forEach(date => {
            table += `<td>${groupedData[tagName][date] || '0.000'}</td>`; // Default to 0.000 if no data
        });
        table += `</tr>`;
    }

    table += `
            </tbody>
        </table>
    `;

    $('#tableContainer').append(table);
}

$('#searchButton').on('click', function (e) {
    e.preventDefault();
    fetchDataAndRender();
});
function renderWeeklyTable(weeklyTableData) {
    $('#tableContainer').append('<h4 class="section-header">Haftalık Veriler</h4>');

    if (!weeklyTableData || weeklyTableData.length === 0) {
        $('#tableContainer').append('<p>No weekly data available for the selected device.</p>');
        return;
    }

    // İlk etiketteki haftalık tarih aralıklarını almak
    let weekRanges = weeklyTableData[0].weeklyData.map(week => week.week.split(' - ')[0]); // Use only the start date


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
            // Değeri sayıya çevir ve sonra toFixed uygula
            const value = parseFloat(week.value) || 0; // Dizeyi sayıya çevir veya 0
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
        $('#tableContainer').append('<p>No monthly data available for the selected device.</p>');
        return;
    }

    // Extract month ranges from the first row
    let monthRanges = monthlyTableData[0].monthlyData.map(month => month.month.split(' - ')[0]); // Use only the start date

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


<style>
    .section-header {
        text-align: center;
        font-weight: normal;
        margin-top: 20px;
    }
</style>
