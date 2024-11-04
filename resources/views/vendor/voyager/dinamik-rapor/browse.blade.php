@extends('voyager::master')

@section('content')
    <div class="container">
        <h1 style="color: black; font-weight: bold; display: flex; align-items: center;">
            <i class="voyager-bar-chart" style="margin-right: 8px;"></i> Dinamik Rapor
        </h1>

        <!-- Inline-flex layout for selection controls -->
        <div style="display: inline-flex; align-items: center; gap: 15px; margin-top: 20px;">
            <!-- Device Dropdown with Bootstrap Multiselect -->
            <div>
                <label for="device" class="sr-only">Cihaz</label>
                <select class="form-control" id="device" multiple="multiple" style="width: 200px;">
                    <!-- Device options will be populated here -->
                </select>
            </div>

            <!-- Tag Dropdown (multi-select) with Bootstrap Multiselect -->
            <div>
                <label for="tag" class="sr-only">Etiket</label>
                <select class="form-control" id="tag" multiple="multiple" style="width: 200px;">
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
<!-- Include Bootstrap Multiselect for device and tag dropdowns with checkboxes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize datetime picker
        $('.datetimepicker').datetimepicker({
            format: 'Y-MM-DD HH:mm',
        });

        // Initialize Bootstrap Multiselect on device dropdown for multi-select with checkboxes
        $('#device').multiselect({
            nonSelectedText: "Cihaz Seçin",
            buttonWidth: '200px',
            includeSelectAllOption: true,
            selectAllText: "Tümünü Seç",
            enableFiltering: true,
            filterPlaceholder: 'Arama',
            templates: {
                filterClearBtn: '', // Çarpı ikonu kaldırılır
                filterIcon: '' // Büyüteç ikonu kaldırılır
            }
        });

        // Initialize Bootstrap Multiselect on tag dropdown for multi-select with checkboxes
        $('#tag').multiselect({
            nonSelectedText: "Etiket Seçin",
            buttonWidth: '200px',
            includeSelectAllOption: true,
            selectAllText: "Tümünü Seç",
            enableFiltering: true,
            filterPlaceholder: 'Arama',
            templates: {
                filterClearBtn: '', // Çarpı ikonu kaldırılır
                filterIcon: '' // Büyüteç ikonu kaldırılır
            }
        });

        var devices = @json($devices);

        // Populate devices dropdown
        let deviceDropdown = $('#device');
        devices.forEach(device => {
            deviceDropdown.append(`<option value="${device.id}">${device.name}</option>`);
        });
        deviceDropdown.multiselect('rebuild');

        // Handle device selection to populate tags dropdown
        deviceDropdown.on('change', function() {
            let selectedDeviceId = $(this).val();
            let selectedDevice = devices.find(device => device.id == selectedDeviceId);

            let tagDropdown = $('#tag');
            tagDropdown.empty();

            if (selectedDevice && typeof selectedDevice.tags === 'object') {
                Object.entries(selectedDevice.tags).forEach(([key, tag]) => {
                    tagDropdown.append(`<option value="${key}">${tag}</option>`);
                });
                // Refresh Bootstrap Multiselect options
                tagDropdown.multiselect('rebuild');
            } else {
                console.warn("No tags available for this device.");
            }
        });

        // Initialize ECharts instance
        let chart = echarts.init(document.getElementById('chartContainer'));

        // Fetch and render data based on selections
        async function fetchDataAndRenderChart() {
            const deviceId = $('#device').val() [0]; // Eğer çoklu seçim desteklemiyorsa .val()[0] şeklinde alın
            const tagIds = $('#tag').val();
            const startDate = $('#date_start').val();
            const endDate = $('#date_end').val();

            if (!deviceId || !tagIds || !startDate || !endDate) {
                alert('Lütfen tüm alanları doldurun.');
                return;
            }

            const seriesData = [];
            let dates = [];

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
                    }
                    const values = data.map(entry => entry.value);

                    seriesData.push({
                        name: $('#tag option[value="' + tagId + '"]').text(),
                        type: 'bar',
                        data: values
                    });

                } catch (error) {
                    console.error("Error fetching data for tag:", tagId, error);
                }
            }

            chart.clear();
            renderChart(seriesData, dates);
        }

        // Function to render the chart
        function renderChart(seriesData, dates) {
            const option = {
                legend: {
                    data: seriesData.map(series => series.name),
                    top: 'top',
                    selectedMode: 'multiple'
                },
                tooltip: {
                    trigger: 'axis'
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
                series: seriesData
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
