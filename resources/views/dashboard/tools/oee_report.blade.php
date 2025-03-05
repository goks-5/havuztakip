@php
use App\Device;
use App\DeviceData;
use Carbon\Carbon;

// Başlangıç değişkenleri
$deviceId = isset($settings['device']) ? $settings['device'] : null;
$deviceName = "Cihaz Seçilmemiş";
$deviceTags = "Etiket Bulunamadı";
$tagId = null;
$tagValues = "Veri Yok";
$deviceData = [];

$timeRange = "Bilinmiyor";
$totalWorkingMinutes = 0;
$tagName = "Tag Seçilmemiş";

$filteredTags = []; // Cihazın etiketlerinden filtrelenen değerleri tutmak için

// Ayarlar içerisindeki saat değeri ve diğer parametreler
$hour = isset($settings['hour']) ? intval($settings['hour']) : 6;
$startTime = Carbon::now()->subHours($hour);
$timeRange = "Son {$hour} Saat";

$plannedProductionTime = isset($settings['planned_production_time']) ? intval($settings['planned_production_time']) : 60;
$actualOutput = isset($settings['actual_output']) ? intval($settings['actual_output']) : 0;
$expectedOutput = isset($settings['expected_output']) ? intval($settings['expected_output']) : 1;

// Eğer cihaz ID varsa, cihazı bul ve etiket bilgilerini kontrol et (metraj/elektrik)
if ($deviceId) {
    $device = Device::find($deviceId);
    if ($device) {
        $deviceName = $device->name;

        // Cihazın etiketlerini kontrol et ve uygun olanı seç
        if (!empty($device->tags)) {
            $tags = is_array($device->tags) ? $device->tags : json_decode($device->tags, true);
            if (!empty($tags)) {
                // Öncelikle "metraj" içeren ve ID'si 100'den küçük etiketleri filtrele
                $filteredTags = array_filter($tags, function ($tag, $key) {
                    return stripos($tag, 'metraj') !== false && intval($key) < 100;
                }, ARRAY_FILTER_USE_BOTH);

                // Eğer "metraj" içeren bulunamazsa, "elektrik" içerenleri al
                if (empty($filteredTags)) {
                    $filteredTags = array_filter($tags, function ($tag, $key) {
                        return stripos($tag, 'elektrik') !== false && intval($key) < 100;
                    }, ARRAY_FILTER_USE_BOTH);
                }

                // Uygun etiket bulunduysa
                if (!empty($filteredTags)) {
                    $deviceTags = implode(", ", $filteredTags);
                    $tagId = array_key_first($filteredTags); // İlk bulunan etiketin anahtarını kullan
                }
            }
        }
    }
}

// Device içerisindeki etiket değerlerinden seçilenin ismini al
if ($tagId !== null && isset($filteredTags[$tagId])) {
    $tagName = $filteredTags[$tagId];
}

// **Log: Cihaz ve Etiket Bilgilerini Kontrol Et**
Log::info("OEE Report - Seçilen Cihaz ve Etiket", [
    'device_id' => $deviceId,
    'device_name' => $deviceName,
    'selected_tag' => $deviceTags,
    'tag_id' => $tagId
]);

// **Grafik İçin Verileri Hazırla**
$chartLabels = [];
$chartData = array_fill(0, $hour, 0);

if (!empty($deviceId) && $tagId !== null) {
    $query = DeviceData::where('device_id', $deviceId)
        ->where('data_id', $tagId)
        ->where('created_at', '>=', $startTime)
        ->orderBy('created_at', 'asc')
        ->get(['value', 'created_at']);

    $previousValue = null;
    $previousTime = null;

    foreach ($query as $data) {
        $currentValue = floatval($data->value);
        $currentTime = Carbon::parse($data->created_at);

        if ($previousValue !== null && $currentValue > $previousValue) {
            $hourIndex = $hour - $currentTime->diffInHours(Carbon::now());
            if ($hourIndex >= 0 && $hourIndex < $hour) {
                $chartData[$hourIndex] += $previousTime->diffInMinutes($currentTime);
            }
            $totalWorkingMinutes += $previousTime->diffInMinutes($currentTime);
        }

        $previousValue = $currentValue;
        $previousTime = $currentTime;
    }
}

for ($i = 0; $i < $hour; $i++) {
    $chartLabels[] = Carbon::now()->subHours($hour - $i)->format('H:i');
}

// **Kullanılabilirlik Hesaplama**
$kullanilabilirlik = ($totalWorkingMinutes / $plannedProductionTime) * 100;
$kullanilabilirlik = min(100, max(0, round($kullanilabilirlik)));

// **Performans Hesaplama**
$performans = ($expectedOutput > 0) ? ($actualOutput / $expectedOutput) * 100 : 0;
$performans = min(100, max(0, round($performans)));

// **Kalite %100 olarak sabitlendi**
$kalite = 100;

// OEE Hesaplaması: (Kullanılabilirlik * Performans * Kalite) / 10000
$oee = ($kullanilabilirlik * $performans * $kalite) / 10000;
$oee = round($oee, 1);

// Cihaz ve etiket verilerini son 10 kayıt için çek (metraj/elektrik kontrolü)
if (!empty($deviceId) && $tagId !== null) {
    $query2 = DeviceData::where('device_id', $deviceId)
        ->where('data_id', $tagId)
        ->orderBy('created_at', 'desc')
        ->limit(10);

    Log::info("SQL Query Debug:", [
        'sql' => $query2->toSql(),
        'bindings' => $query2->getBindings(),
    ]);

    $deviceData = $query2->pluck('value')->toArray();

    if (!empty($deviceData)) {
        $tagValues = implode(", ", $deviceData);
    }
}

Log::info("OEE Report - Sonuç", [
    'device_id' => $deviceId,
    'device_name' => $deviceName,
    'tag_id' => $tagId,
    'tag_name' => $deviceTags,
    'tag_values' => $tagValues,
    'query_result' => $deviceData,
    'OEE' => $oee
]);
@endphp

<!-- jQuery Yükleme -->
<script>
    if (typeof jQuery == 'undefined') {
        document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
    }
</script>

<style>
    .progress_container {
        margin-top: 12px;
        margin-bottom: 8px;
    }
    
    .tool_container {
        /* Tool'un bounding box'ına %100 uyum sağlayacak şekilde */
        width: 100%;
        height: 100%;
        position: relative;
        box-sizing: border-box;
        overflow: hidden; /* ya da auto; eğer sığmazsa scroll isterseniz */
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .progress_bar {
        width: 100%;          /* Kapsayıcının tüm genişliği */
        height: 20px;         /* Bar yüksekliği */
        background: #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 4px;
        position: relative;
    }

    .progress_fill {
        height: 100%;
        border-radius: 10px;
        text-align: center;
        line-height: 20px;
        font-size: 14px;
        color: #fff;
        padding: 0 8px;
        transition: width 0.4s ease-in-out;
        display: block;
    }

    /* Renkli degrade */
    .progress_success {
        background: linear-gradient(to right, #4CAF50, #81C784);
    }
    .progress_warning {
        background: linear-gradient(to right, #FFC107, #FFD54F);
    }
    .progress_primary {
        background: linear-gradient(to right, #1E88E5, #64B5F6);
    }

    .progress_container strong {
        font-weight: bold;
        font-size: 14px;
        color: #333;
    }

    .info_button {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 20px;
    height: 20px;
    background: #000;
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    text-align: center;
    line-height: 20px;
    font-size: 12px;
    z-index: 3000 !important; /* 2000'den büyük bir değer verin */
    }

    .tooltip_box {
        display: none;
        position: absolute;
        top: 10px;
        right: 10px;
        background: #333;
        color: white;
        padding: 10px;
        border-radius: 5px;
        font-size: 12px;
        width: 250px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        white-space: nowrap;
        z-index: 3500;
    }

    #statusChart {
        margin-top: 20px;
    }
    /* Yan yana yerleşim için örnek stil */
    .chart-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .chart-col {
        flex: 0 0 48%;
    }
</style>

<!-- Burada 'tool_container' ID'sini ekledik ki ResizeObserver ile dinleyebilelim -->
<div class="tool_container" id="tool_container_{{ $tool->id }}">
    @include('dashboard.tools.toolSettings', ['tool' => $tool])

    <!-- Tooltip -->
    <button id="info_button_{{ $tool->id }}" class="info_button">ℹ</button>
   
     <!-- Sadece tek download butonu, ID eşsiz -->
     <button id="download_button_{{ $tool->id }}" 
            class="info_button" 
            style="right: 40px;" 
            title="Verileri CSV olarak indir">↓</button>

    <div id="tooltip_box_{{ $tool->id }}" class="tooltip_box">
        <strong>Detaylar:</strong><br>
        Cihaz: {{ $deviceName }}<br>
        Etiket: {{ $tagName }}<br>
        Veri Aralığı: {{ $timeRange }}<br>
        Çalışma Süresi: {{ $totalWorkingMinutes }} Dakika<br>
        Planlanan Üretim: {{ $plannedProductionTime }} Dakika<br>
        Gerçek Çıktı: {{ $actualOutput }} Adet<br>
        Beklenen Çıktı: {{ $expectedOutput }} Adet
    </div>

         <!-- Üst Kısım: 3 progress bar (solda) + OEE (sağda) -->
    <div style="
        display: flex; 
        align-items: center;   /* OEE yazısı barlarla dikey ortalansın */
        flex-wrap: nowrap;     /* Daraltınca scroll bar oluşur, satır atlamasın */
        gap: 20px;
        width: 100%;
        box-sizing: border-box;
        overflow: auto;        /* Eğer çok daralırsa scroll çıksın */
    ">
        <!-- Solda 3 bar dikey -->
        <div style="
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex: 1;       /* Boş alanı kaplasın */
            gap: 10px;     /* Barlar arası dikey boşluk */
        ">
            <!-- 1) Kullanılabilirlik -->
            <div>
                <div style="font-weight: bold;">Kullanılabilirlik</div>
                <div class="progress_bar">
                    <div class="progress_fill progress_success" style="width: {{ $kullanilabilirlik }}%;">
                        {{ $kullanilabilirlik }}%
                    </div>
                </div>
            </div>

            <!-- 2) Performans -->
            <div>
                <div style="font-weight: bold;">Performans</div>
                <div class="progress_bar">
                    <div class="progress_fill progress_warning" style="width: {{ $performans }}%;">
                        {{ $performans }}%
                    </div>
                </div>
            </div>

            <!-- 3) Kalite -->
            <div>
                <div style="font-weight: bold;">Kalite</div>
                <div class="progress_bar">
                    <div class="progress_fill progress_primary" style="width: {{ $kalite }}%;">
                        {{ $kalite }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağda OEE (dikey ortalamak için align-items: center; kullandık) -->
        <div style="
            font-size: 64px;
            font-weight: bold;
            white-space: nowrap;  /* Tek satırda kalsın */
        ">
            {{ $oee }}%
        </div>
    </div>

    <!-- Alt Kısım: ECharts Grafik -->
    <div style="margin-top: 20px; width: 100%; height: 50%; box-sizing: border-box;">
        <div id="statusChart_tool_{{ $tool->id }}" style="width: 100%; height: 100%;"></div>
    </div>
</div>

<!-- ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
<script>
/** 
 * CSV indirme fonksiyonu 
 */
function downloadChartDataAsCSV(labels, data) {
    let csvContent = "Saat,Deger\n";
    for (let i = 0; i < labels.length; i++) {
        csvContent += labels[i] + "," + data[i] + "\n";
    }
    let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    let url = URL.createObjectURL(blob);
    let link = document.createElement('a');
    link.href = url;
    link.download = "grafik_verileri.csv";
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

$(document).ready(function () {
    // PHP'den gelen chartLabels ve chartData
    var labels = {!! json_encode($chartLabels) !!};
    var data   = {!! json_encode($chartData) !!};

    // Tooltip aç/kapa
    $('#info_button_{{ $tool->id }}').on('click', function(e) {
        e.preventDefault();
        $('#tooltip_box_{{ $tool->id }}').fadeToggle(200);
    });
    $(document).click(function(event) {
        if (!$(event.target).closest('#info_button_{{ $tool->id }}, #tooltip_box_{{ $tool->id }}').length) {
            $('#tooltip_box_{{ $tool->id }}').fadeOut(200);
        }
    });

    // Download butonu tıklanınca CSV indir
    $('#download_button_{{ $tool->id }}').on('click', function() {
        downloadChartDataAsCSV(labels, data);
    });

    // ECharts init
    var chartDiv = document.getElementById('statusChart_tool_{{ $tool->id }}');
    var statusChart = echarts.init(chartDiv);

    var optionStatus = {
        title: {
            text: 'Cihaz Çalışma Grafiği',
            left: 'center'
        },
        grid: {
            left: 0,
            right: 20,
            top: 50,
            bottom: 30,
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: labels,
            axisLabel: { show: true },
            axisLine: { show: true },
            axisTick: { show: true }
        },
        yAxis: {
            type: 'value',
            min: 0,
            max: 1
        },
        series: [{
            data: data,
            type: 'line',
            areaStyle: {},    
            label: { show: false }
            // smooth: true
        }]
    };
    statusChart.setOption(optionStatus);

    // Responsive
    window.addEventListener('resize', function () {
        statusChart.resize();
    });
    if (typeof ResizeObserver !== 'undefined') {
        const ro = new ResizeObserver(() => {
            statusChart.resize();
        });
        ro.observe(document.getElementById('tool_container_{{ $tool->id }}'));
    }
});
</script>