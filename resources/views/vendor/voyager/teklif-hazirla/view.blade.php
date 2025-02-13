@extends('voyager::master')

@section('content')
<!-- İndir Butonu -->
<div style="position: absolute; top: 17mm; right: 20mm;">
    <button id="downloadPdfBtn" class="btn btn-primary">📥 İndir</button>
</div>

<div class="container" style="background: url('{{ asset('images/background.png') }}') no-repeat center center; background-size: cover; width: 210mm; height: 297mm; padding: 20mm; margin: auto; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.1); position: relative;">
    <!-- Logo -->
<div style="position: absolute; top: 10mm; left: 20mm;">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 120px; height: auto;">
</div>

<!-- Sağ Üst Köşe Bilgileri -->
<div class="right-info" style="position: absolute; top: 40mm; right: 10mm; text-align: left;">
    <table style="border-collapse: collapse;">
        <tr>
            <td style="font-weight: bold; text-align: left; padding-right: 10px;">Tarih:</td>
            <td style="text-align: left;">{{ $offer->created_at->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; text-align: left; padding-right: 10px;">Teklif No:</td>
            <td style="text-align: left;">{{ $offer->offer_no }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; text-align: left; padding-right: 10px;">Teslim Süresi:</td>
            <td style="text-align: left;">{{ $offer->delivery_date }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; text-align: left; padding-right: 10px;">Opsiyon:</td>
            <td style="text-align: left;">{{ $offer->created_at->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; text-align: left; padding-right: 10px;">Talep No:</td>
            <td style="text-align: left;">{{ $offer->demand_no }}</td>
        </tr>
    </table>
</div>

    <!-- Başlık (Title) -->
    <div style="text-align: center; margin-top: 60mm; font-size: 24pt; font-weight: bold; color: #333;">
        {{ $offer->title }}
    </div>

    <!-- İlgili Şirket -->
    <div style="text-align: left; margin-top: 5mm; font-size: 16pt; font-weight: normal; color: #333;">
    {{ $offer->company }}
    </div>

    <!-- İlgili Kişi -->
    <div style="text-align: left; margin-top: 5mm; font-size: 16pt; font-weight: normal; color: #333;">
        Sayın {{ $offer->person_name }},
    </div>

    <!-- Ürün veya Hizmet Tablosu -->
    <div style="margin-top: 20mm;">
        <table style="width: 100%; border-collapse: collapse; font-size: 12pt; text-align: left;">
            <thead>
                <tr style="background-color: #0056b3; color: white;">
                    <th style="padding: 10px; border: 1px solid #ddd;">Açıklama</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Adet</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Birim Fiyat</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Toplam</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($offer->unit_price) && !empty($offer->total_price))
                    @foreach (explode(',', $offer->unit_price) as $index => $unit_price)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            {{ explode(',', $offer->explanation)[$index] ?? 'Açıklama yok' }}
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ explode(',', $offer->piece)[$index] ?? '0' }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ number_format($unit_price, 2) }} {{ $offer->currency }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ number_format(explode(',', $offer->total_price)[$index] ?? 0, 2) }} {{ $offer->currency }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="padding: 10px; text-align: center; border: 1px solid #ddd;">Veri bulunamadı.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Toplam -->
<div style="margin-top: 10mm; text-align: right; font-size: 14pt; font-weight: bold;">
    <span class="total-box" style="background-color: #0056b3; color: white; padding: 5px 15px; border-radius: 5px;">
        Toplam: {{ number_format($offer->total ?? 0, 2) }} {{ $offer->currency }}
    </span>
</div>

    <!-- Not -->
    <div style="margin-top: 10mm; text-align: left; font-size: 12pt; color: #333;">
        <em>*Fiyatlara KDV dahil değildir ve peşin ödeme geçerlidir.</em>
    </div>

<!-- Onay Kutusu -->
<div class="onay-kutusu" style="position: absolute; top: 230mm; left: 130mm; text-align: center; font-size: 12pt;">
    <label style="font-weight: bold; color: #333; display: block; margin-bottom: 5px;">Onay</label>
    <div style="width: 60mm; height: 20mm; border: 2px solid #333; border-radius: 3px; cursor: pointer;"></div>
</div>

<!-- Notlar Tablo Satırı -->
<div style="position: absolute; top: 240mm; left: 20mm; right: 20mm;">
<table style="width: 60%; border-collapse: collapse; font-size: 11pt;">
        <tr>
            <td style="background-color: #0056b3 !important; color: white !important; padding: 10px !important; border-radius: 5px !important; text-align: left !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
                <strong style="color: white !important;">Notlar:</strong> {{ $offer->notes ?? 'Not eklenmedi.' }}
            </td>
        </tr>
    </table>
</div>

   <!-- Şirket Bilgileri -->
<div style="position: absolute; bottom: 10mm; left: 0; width: 100%; text-align: center; font-size: 12pt; color: #333;">
    <strong>TA Teknik Otomasyon Sanayi ve Ticaret Limited Şirketi</strong><br>
    info@tateknik.com
</div>


<style>
    @media print {
        body {
            -webkit-print-color-adjust: exact !important; /* Chrome ve Safari */
            print-color-adjust: exact !important; /* Diğer tarayıcılar */
        }

        /* Sayfa arka planı ve üst kısım */
        .container {
            background: url('{{ asset('images/background.png') }}') no-repeat center center !important;
            background-size: cover !important;
            width: 210mm !important;
            height: 297mm !important;
            padding: 20mm !important;
            margin: auto !important;
            border: 1px solid #ddd !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) !important;
            position: relative !important;
        }

        /* Sağ Üst Köşe Bilgilerinin Çerçeve ve Arka Planının Kaldırılması */
    .container div {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    /* Sağ Üst Köşe Bilgilerini Yukarı Taşıma */
    .right-info {
        position: absolute !important;
        top: 30mm !important; /* Daha yukarı taşındı */
        right: 10mm !important;
    }

    /* Sağ Üst Köşe Bilgilerinin Fontunu Küçült */
    .container .right-info {
        font-size: 10pt !important; /* Sadece sağ üst köşe bilgileri küçültüldü */
        text-align: right !important;
        line-height: 1.4 !important; /* Daha kompakt görünüm için satır yüksekliği ayarlandı */
    }

    /* Sağ Üst Köşe Bilgilerinin Fontunu Küçült */
    .right-info table {
        font-size: 9pt !important; /* Sadece sağ üst köşe bilgileri küçültüldü */
        line-height: 1.2 !important; /* Daha sıkı bir görünüm için satır yüksekliği */
    }

    .right-info table td {
        padding: 2px 5px !important; /* Hücre iç boşluklarını daralttık */
    }

    /* Sağ Üst Köşe Bilgi Tablosu */
    .container table {
        margin-top: 10mm !important; /* Başlıktan biraz aşağı kaydırıldı */
        border: none !important;
        background: transparent !important;
    }

    .container table td {
        border: none !important;
        background: transparent !important;
    }

    /* Onay Kutusu */
    .onay-kutusu {
            border: 2px solid #333 !important;
            border-radius: 5px !important;
            width: 80mm !important;
            height: 40mm !important;
        }

        /* Tablo Başlığı */
        thead tr {
            background-color: #0056b3 !important; /* Arka plan mavi */
            color: white !important; /* Yazı rengi beyaz */
        }

        thead th {
            color: white !important; /* Tablo başlığı hücreleri yazı rengi */
            background-color: #0056b3 !important; /* Hücre arka planı */
            border: 1px solid #ddd !important;
            padding: 10px !important;
        }

        /* Tablo Satırları */
        tbody tr {
            background-color: transparent !important; /* Şeffaf arka plan */
            color: black !important; /* Yazı rengi siyah */
        }

        tbody td {
            border: 1px solid #ddd !important;
            padding: 10px !important;
            color: black !important; /* Satır yazı rengi */
        }

        /* Toplam Bölümü */
        .total-box {
            background-color: #0056b3 !important; /* Arka plan mavi */
            color: white !important; /* Yazı rengi beyaz */
            padding: 5px 15px !important;
            border-radius: 5px !important;
            text-align: right !important;
            font-size: 14pt !important;
            font-weight: bold !important;
            display: inline-block !important;
        }

        .notlar-kutusu {
        background-color: #0056b3 !important; /* Arka plan mavi */
        color: white !important; /* Yazı rengi beyaz */
        padding: 10px !important;
        border-radius: 5px !important;
        text-align: left !important;
        -webkit-print-color-adjust: exact !important; /* Chrome ve Safari için */
        print-color-adjust: exact !important; /* Diğer tarayıcılar için */
    }

    .notlar-kutusu strong {
        color: white !important; /* Notlar yazısı beyaz */
    }

        table, th, td {
            border: 1px solid #ddd !important;
            padding: 10px !important;
        }
    }
</style>
@endsection

@section('javascript')
<!-- Gerekli Kütüphaneleri Yükle -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    document.getElementById("downloadPdfBtn").addEventListener("click", function () {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF('p', 'mm', 'a4');

        html2canvas(document.querySelector(".container"), {
            scale: 2, // Kaliteyi artırmak için
            useCORS: true // Farklı kaynaklardan gelen img desteği
        }).then(canvas => {
            let imgData = canvas.toDataURL("image/png");
            let imgWidth = 210; // A4 genişliği mm cinsinden
            let pageHeight = 297; // A4 yüksekliği mm cinsinden
            let imgHeight = (canvas.height * imgWidth) / canvas.width;

            doc.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);
            doc.save("Teklif_{{ $offer->offer_no }}.pdf"); // Dosya adını Teklif No'ya göre kaydeder
        }).catch(error => {
            console.error("PDF oluşturulurken hata oluştu:", error);
        });
    });
</script>
@endsection
