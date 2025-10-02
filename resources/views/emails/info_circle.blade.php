<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Durum Özeti - {{ $companyName }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        h2 {
            margin-bottom: 10px;
            color: #444;
        }
        .summary {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .section-title {
            margin-top: 20px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        ul {
            margin: 8px 0 15px 20px;
        }
        li {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <h2>Durum Özeti - {{ $companyName }}</h2>

    <p class="summary">
        Cihaz: {{ $summary['deviceCount'] }} <br>
        Nokta: {{ $summary['pointCount'] }} <br>
        Çevrim Dışı: {{ $summary['oflineCount'] }} <br>
        Pasif: {{ $summary['passiveCount'] }}
    </p>

    {{-- Çevrimdışı cihazlar --}}
    @if(!empty($summary['oflineDevices']))
        <div class="section-title">Çevrimdışı Cihazlar</div>
        <ul>
            @foreach($summary['oflineDevices'] as $dev)
                <li>
                    {{ $dev['name'] }} cihazına {{ $dev['last_at'] }}'den beri ulaşılamıyor 
                    @if(isset($dev['status']) && $dev['status'] == 0)
                        <strong>(Pasif)</strong>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Pasif cihazlar --}}
    @if(!empty($summary['passiveDevices']))
        <div class="section-title">Pasif Cihazlar</div>
        <ul>
            @foreach($summary['passiveDevices'] as $dev)
                {{-- Eğer zaten çevrimdışı listesinde varsa tekrar yazma --}}
                @if(!collect($summary['oflineDevices'])->pluck('id')->contains($dev['id']))
                    <li>{{ $dev['name'] }} (Pasif)</li>
                @endif
            @endforeach
        </ul>
    @endif

    {{-- Etiketi uzun süre değişmeyenler --}}
    @if(!empty($summary['changeTags']))
        <div class="section-title">Etiket Değişmeyenler</div>
        <ul>
            @foreach($summary['changeTags'] as $tag)
                <li>
                    {{ $tag['name'] }} cihazındaki <strong>{{ $tag['tag'] }}</strong> etiketi 
                    {{ $tag['last_change'] }}'den beri değişmedi
                </li>
            @endforeach
        </ul>
    @endif

    <p style="margin-top: 30px; font-size: 12px; color: #888;">
        Bu e-posta otomatik olarak gönderilmiştir.
    </p>
</body>
</html>
