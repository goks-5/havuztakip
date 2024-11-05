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
                <label for="tag" class="sr-only">Etiket</label>
                <select class="form-control" id="tag" multiple="multiple" style="width: 200px;">
                    <option value="">Etiket Seçin</option>
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
                <i class="fa fa-balance-scale"></i> <!-- Örnek olarak bir terazi ikonu kullandık -->
            </button>


        </div>

        <!-- Chart Containers -->
        <div style="display: flex; gap: 20px; margin-top: 20px;">
        <div id="chartContainer" style="height: 500px; width: 70%;"></div> <!-- Genişliği %70 yaptık ve yüksekliği artırdık -->

            <!-- Pie Chart Container with Date and Navigation Buttons -->
            <div style="position: relative; width: 50%; height: 400px;">
                <div id="pieChartContainer" style="height: 100%;"></div>
                <div id="dateNavigation" style="text-align: center; position: absolute; bottom: 10px; width: 100%; display: none;">
                    <button id="prevDate" style="margin-right: 10px;">&lt;</button>
                    <span id="currentDate">Tarih</span>
                    <button id="nextDate" style="margin-left: 10px;">&gt;</button>
                </div>
            </div>
        </div>
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

        $('#tag').multiselect({
            nonSelectedText: "Etiket Seçin",
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

        var devices = @json($devices);
        let deviceDropdown = $('#device');
        devices.forEach(device => {
            deviceDropdown.append(`<option value="${device.id}">${device.name}</option>`);
        });
        deviceDropdown.multiselect('rebuild');

        deviceDropdown.on('change', function() {
            let selectedDeviceId = $(this).val();
            let selectedDevice = devices.find(device => device.id == selectedDeviceId);

            let tagDropdown = $('#tag');
            tagDropdown.empty();

            if (selectedDevice && typeof selectedDevice.tags === 'object') {
                Object.entries(selectedDevice.tags).forEach(([key, tag]) => {
                    tagDropdown.append(`<option value="${key}">${tag}</option>`);
                });
                tagDropdown.multiselect('rebuild');
            } else {
                console.warn("No tags available for this device.");
            }
        });

        let chart = echarts.init(document.getElementById('chartContainer'));
        let pieChart = echarts.init(document.getElementById('pieChartContainer'));
        let currentDateIndex = 0;
        let dates = [];
        let allPieData = []; // Tüm tarihlere göre pie chart verisi

        async function fetchDataAndRenderChart() {
            const deviceId = $('#device').val()[0];
            const tagIds = $('#tag').val();
            const startDate = $('#date_start').val();
            const endDate = $('#date_end').val();

            if (!deviceId || !tagIds || !startDate || !endDate) {
                alert('Lütfen tüm alanları doldurun.');
                return;
            }

            const seriesData = [];
            allPieData = []; // Her sorguda sıfırlanır
            let pieDataByDate = {}; // Tarihe göre pie chart verisi

            for (const tagId of tagIds) {
                try {
                    const response = await $.ajax({
                        url: '/get-filtered-data',
                        type: 'GET',
                        data: {
                            device_id: deviceId,
                            data_id: tagId,
                            start_date: startDate,
                            end_date: endDate
                        }
                    });

                    const data = response.data;
                    if (data.length > 0) {
                        dates = data.map(entry => entry.created_at);
                        const values = data.map(entry => entry.value);

                        // Ana grafik için veriyi yapılandırma
                        seriesData.push({
                            name: $('#tag option[value="' + tagId + '"]').text(),
                            type: 'bar',
                            data: values
                        });

                        // Pie chart için tarihe göre veri oluşturma
                        data.forEach(entry => {
                            if (!pieDataByDate[entry.created_at]) {
                                pieDataByDate[entry.created_at] = [];
                            }
                            pieDataByDate[entry.created_at].push({
                                name: $('#tag option[value="' + tagId + '"]').text(),
                                value: entry.value
                            });
                        });
                    }
                } catch (error) {
                    console.error("Error fetching data for tag:", tagId, error);
                }
            }

            allPieData = dates.map(date => ({
                date: date,
                data: pieDataByDate[date] || []
            }));

            chart.clear();
            renderChart(seriesData, dates);

            // İlk tarihi göster ve ilk pie chart verisini çiz
            currentDateIndex = dates.length - 1;
            $('#currentDate').text(dates[currentDateIndex]);
            renderPieChart(allPieData[currentDateIndex].data);

            $('#dateNavigation').css('display', 'flex');
        }

        function renderChart(seriesData, dates) {
    const option = {
        legend: {
            data: seriesData.map(series => series.name),
            top: '2%',  // Legend'i biraz yukarı taşıyoruz
            selectedMode: 'multiple'
        },
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            top: '15%'  // Grafik alanını biraz aşağıya taşıyoruz
        },
        xAxis: {
            type: 'category',
            data: dates,
            axisLabel: { show: true }
        },
        yAxis: {
            type: 'value',
            axisLabel: { show: true }
        },
        series: seriesData.map(series => ({
            ...series,
            itemStyle: {
                decal: {
                    symbol: 'line',
                    dashArrayX: [1, 2],
                    dashArrayY: [2, 1],
                    rotation: Math.PI / 4,
                    color: 'auto'
                }
            }
        }))
    };

    chart.setOption(option);
}


                function renderPieChart(pieData) {
                const pieOption = {
                    tooltip: {
                        trigger: 'item',
                        formatter: function (params) {
                            const valueFormatted = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(params.value);
                            return `${params.name}: ${valueFormatted} (${params.percent}%)`;
                        }
                    },
                    legend: {
                        top: '2%',  // Legend'i daha yukarı taşıyoruz
                        left: 'center',
                        data: pieData.map(item => item.name)
                    },
                    series: [
                        {
                            name: 'Etiket Son Verisi',
                            type: 'pie',
                            radius: ['40%', '70%'], // İç ve dış yarıçap değerleriyle grafik boyutunu ayarlayabilirsiniz
                            top: '5%',  // Grafiği daha aşağıya taşıyoruz
                            data: pieData,
                            itemStyle: {
                                borderRadius: 10,
                                borderColor: '#ffffff',  
                                borderWidth: 4,
                                decal: {
                                    color: 'auto',  
                                    symbol: 'line',
                                    dashArrayX: [1, 2], 
                                    dashArrayY: [2, 1], 
                                    rotation: Math.PI / 4 
                                }
                            },
                            label: {
                                show: true, 
                                position: 'inside',
                                formatter: function (params) {
                                    return `${params.percent}%`; 
                                },
                                fontSize: 12,
                                color: '#fff',
                                fontWeight: 'bold'
                            },
                            labelLine: {
                                show: false 
                            }
                        }
                    ]
                };

            pieChart.setOption(pieOption);
        }


        // Tarihi bir gün azalt ve pie chart verisini güncelle
        $('#prevDate').on('click', function() {
            if (currentDateIndex > 0) {
                currentDateIndex--;
                $('#currentDate').text(dates[currentDateIndex]);
                renderPieChart(allPieData[currentDateIndex].data); // Güncel pie chart verisini kullan
            }
        });

        // Tarihi bir gün artır ve pie chart verisini güncelle
        $('#nextDate').on('click', function() {
            if (currentDateIndex < dates.length - 1) {
                currentDateIndex++;
                $('#currentDate').text(dates[currentDateIndex]);
                renderPieChart(allPieData[currentDateIndex].data); // Güncel pie chart verisini kullan
            }
        });

        $('#searchButton').on('click', function (e) {
            e.preventDefault();
            fetchDataAndRenderChart();
        });
    });
</script>
@endsection
