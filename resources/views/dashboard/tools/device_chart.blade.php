<!-- ECharts Library -->
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

<div class="tool_data row ">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])
  @php
    $setting = json_decode($tool->settings,true);
  @endphp
  @if (isset($settings['title']) && !empty($settings['title']))
  <div class="col-xs-12 text-center">{{$settings['title']}} </div>
  @endif
  <div class="col-xs-12" id="tool_{{$tool->id}}" data-type="{{$setting['type'] ?? 'line'}}" style="height: calc(100% - 6px); overflow: hidden;"></div>
   <div id="chart-container" style="position: relative; margin-top: 20px;">
    </div>   
    <div id="chart" style="width: 100%; height: 400px; margin-bottom: 45px;"></div> <!-- 👈 scroll biraz daha aşağı -->
</div>

<div style="position: absolute; top: 10px; right: 10px; z-index: 10;">
    <!-- Üç Çizgi Menü Butonu -->
    <button id="menuButton_{{$tool->id}}" onclick="toggleMenu('menu_{{$tool->id}}')" style="background: none; border: none; cursor: pointer; font-size: 20px;">
        &#9776;
    </button>
    <div id="menu_{{$tool->id}}" style="display: none; position: absolute; background: #ffffff; border: 1px solid #ccc; border-radius: 5px; padding: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); z-index: 1000;">
        <button onclick="saveImage('tool_{{$tool->id}}')" style="display: flex; align-items: center; justify-content: center; width: 100%; padding: 10px; cursor: pointer; border: none; background: none;">
            <img src="https://img.icons8.com/material-outlined/24/000000/save.png" style="margin-right: 8px;" />
        </button>
        <button onclick="downloadExcel('tool_{{$tool->id}}')" style="display: flex; align-items: center; justify-content: center; width: 100%; padding: 10px; cursor: pointer; border: none; background: none;">
            <img src="https://img.icons8.com/material-outlined/24/000000/ms-excel.png" style="margin-right: 8px;" />
        </button>
    </div>
</div>


<script type="text/javascript">
  function toggleMenu(menuId) {
    var menu = document.getElementById(menuId);
    if (menu) {
        if (menu.style.display === "none" || menu.style.display === "") {
            menu.style.display = "block";
        } else {
            menu.style.display = "none";
        }
    }
}

document.addEventListener('click', function(event) {
    var menus = document.querySelectorAll('[id^="menu_"]');
    menus.forEach(menu => {
        var button = document.querySelector(`#menuButton_${menu.id.split('_')[1]}`);
        if (!menu.contains(event.target) && event.target !== button) {
            menu.style.display = 'none';
        }
    });
});

// ✅ Grafik: scroll daha aşağıda, xAxis gizli, sağ-sol minimum
document.addEventListener("DOMContentLoaded", function() {
    var chartDom = document.getElementById('tool_{{$tool->id}}');
    if (!chartDom) return;
    var myChart = echarts.init(chartDom);

    var option = {
        tooltip: { trigger: 'axis' },
        legend: { top: '5%', left: 'center' },
        grid: {
            top: '12%',
            left: '1%',
            right: '1%',
            bottom: '8%',   // 👈 grafiğin alt boşluğu biraz daha genişletildi
            containLabel: true
        },
        xAxis: {
            type: 'category',
            show: false,    // tarih etiketleri kaldırıldı
            data: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran']
        },
        yAxis: {
            type: 'value',
            axisLabel: { fontSize: 11 }
        },
        series: [
            {
                name: 'Veri',
                type: '{{$setting['type'] ?? 'bar'}}',
                data: [120, 132, 101, 134, 90, 230],
                barMaxWidth: 25
            }
        ]
    };

    myChart.setOption(option);
    window.addEventListener('resize', () => myChart.resize());
});
</script>

<style>
.tool_data {
    height: 100%;
    max-height: 100%;
    overflow: hidden;
}

#chart-container {
    position: relative;
    margin-top: 20px;
}

#chart {
    width: 100%;
    height: 400px;
    margin-bottom: 45px; /* 👈 scroll 10-12px daha aşağıda */
}

#menu {
    display: none;
    position: absolute;
    top: 30px;
    right: 0;
    background: #ffffff;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}
</style>
