@extends('voyager::master')

@section('content')
    <div class="container">
        <h1 style="color: black; font-weight: bold; display: flex; align-items: center;">
            <i class="voyager-bar-chart" style="margin-right: 8px;"></i> Dinamik Rapor
        </h1>

        <!-- Inline-flex layout for selection controls -->
        <div style="display: inline-flex; align-items: center; gap: 15px; margin-top: 20px;">
            <!-- Device Dropdown -->
            <div>
                <label for="device" class="sr-only">Cihaz</label>
                <select class="form-control" id="device" style="width: 150px;">
                    <!-- Device options will be populated here -->
                </select>
            </div>

            <!-- Tag Dropdown -->
            <div>
                <label for="tag" class="sr-only">Etiket</label>
                <select class="form-control" id="tag" style="width: 150px;">
                    <option value="">Etiket Seçin</option>
                    <!-- Tag options will be populated here -->
                </select>
            </div>

            <!-- Start Date Picker -->
            <div>
                <label for="date_start" class="sr-only">Başlangıç Tarihi</label>
                <input type="text" class="form-control datetimepicker" id="date_start" placeholder="Başlangıç Tarihi" style="width: 200px;">
            </div>

            <!-- End Date Picker -->
            <div>
                <label for="date_end" class="sr-only">Bitiş Tarihi</label>
                <input type="text" class="form-control datetimepicker" id="date_end" placeholder="Bitiş Tarihi" style="width: 200px;">
            </div>

            <!-- Search Button -->
            <button class="btn btn-primary" id="searchButton" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 38px;">
                <i class="voyager-search"></i>
            </button>
        </div>

        <!-- Chart Container -->
        <div id="chartContainer" style="height: 400px; width: 100%; margin-top: 20px;"></div>
    </div>
    
@endsection

@section('javascript')
<!-- Include ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.datetimepicker').datetimepicker({
            format: 'Y-MM-DD HH:mm', // Date and time format
        });

        var devices = @json($devices);

        // Populate devices dropdown
        let deviceDropdown = $('#device');
        deviceDropdown.append('<option value="">Cihaz Seçin</option>');
        devices.forEach(device => {
            deviceDropdown.append(`<option value="${device.id}">${device.name}</option>`);
        });

        // Handle device selection to populate tags dropdown
        deviceDropdown.on('change', function() {
            let selectedDeviceId = $(this).val();
            let selectedDevice = devices.find(device => device.id == selectedDeviceId);

            let tagDropdown = $('#tag');
            tagDropdown.empty();
            tagDropdown.append('<option value="">Etiket Seçin</option>');

            if (selectedDevice && typeof selectedDevice.tags === 'object') {
                Object.entries(selectedDevice.tags).forEach(([key, tag]) => {
                    tagDropdown.append(`<option value="${key}">${tag}</option>`);
                });
            } else {
                console.warn("No tags available for this device.");
            }
        });

        // Initialize ECharts instance
        let chart = echarts.init(document.getElementById('chartContainer'));

        // Function to fetch and render data based on user selections
        async function fetchDataAndRenderChart() {
            const deviceId = $('#device').val();
            const tagId = $('#tag').val();
            const startDate = $('#date_start').val();
            const endDate = $('#date_end').val();

            if (!deviceId || !tagId || !startDate || !endDate) {
                alert('Please select all fields.');
                return;
            }

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
                console.log(data);

                // Extract dates and values from the response
                const dates = data.map(entry => entry.created_at);
                const lineValues = data.map(entry => entry.value); // Line series data
                const barValues = data.map(entry => entry.value); // Bar series data (example)

                // Render chart with both datasets
                renderChart(dates, lineValues, barValues);
            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }

        // Function to render the mixed chart with ECharts
        function renderChart(dates, lineValues, barValues) {
            const option = {
                title: {
                    text: 'Dinamik Rapor Grafiği'
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['Çizgi', 'Blok']
                },
                xAxis: {
                    type: 'category',
                    data: dates
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        name: 'Çizgi',
                        type: 'line',
                        data: lineValues,
                        itemStyle: {
                            color: '#5470C6'
                        }
                    },
                    {
                        name: 'Blok',
                        type: 'bar',
                        data: barValues,
                        itemStyle: {
                            color: '#91CC75'
                        }
                    }
                ]
            };

            chart.setOption(option);
        }

        // Event listener for the search button
        $('#searchButton').on('click', function (e) {
            e.preventDefault();
            fetchDataAndRenderChart();
        });
    });
</script>
@endsection
