<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teklif</title>
</head>
<body>
    <h1>{{ $offer->title }}</h1>
    <p>Sayın {{ $offer->person_name }},</p>
    <p>Size yeni bir teklif sunulmuştur. Teklif detayları aşağıdadır:</p>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Açıklama</th>
                <th>Adet</th>
                <th>Birim Fiyat</th>
                <th>Toplam Fiyat</th>
            </tr>
        </thead>
        <tbody>
            @php
                $descriptions = explode(',', trim($offer->explanation, '"'));
                $quantities = explode(',', trim($offer->piece, '"'));
                $unit_prices = explode(',', trim($offer->unit_price, '"'));
                $total_prices = explode(',', trim($offer->total_price, '"'));
            @endphp
            @foreach ($descriptions as $index => $description)
                <tr>
                    <td>{{ $description }}</td>
                    <td>{{ $quantities[$index] ?? '' }}</td>
                    <td>{{ $unit_prices[$index] ?? '' }}</td>
                    <td>{{ $total_prices[$index] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Genel Toplam:</strong> {{ $offer->total }} {{ $offer->currency }}</p>

    <p>İyi günler dileriz.</p>
    <p><strong>Enerji Yönetim Ekibi</strong></p>
</body>
</html>
