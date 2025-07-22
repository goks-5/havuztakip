@extends('voyager::master')

@section('content')
<!-- İndir Butonu -->
<div style="position: absolute; top: 17mm; right: 20mm;">
    <button id="downloadPdfBtn" class="btn btn-primary">📥 İndir</button>
</div>

<!-- Sayfa 1 (Orijinal) -->
<div class="container-page1" style="background: url('{{ asset('images/background.png') }}') no-repeat center center; background-size: cover; width: 210mm; height: 297mm; padding: 20mm; margin: auto; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.1); position: relative;">
    <!-- Logo -->
    <div style="position: absolute; top: 10mm; left: 20mm;">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 120px; height: auto;">
    </div>

    <!-- Sağ Üst Köşe Bilgileri -->
    <div class="right-info" style="position: absolute; top: 37mm; right: 10mm; text-align: left; color: #000;"> 
        <!-- top: 40mm --> 
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
                <td style="font-weight: bold; text-align: left; padding-right: 10px;">Talep No:</td>
                <td style="text-align: left;">{{ $offer->demand_no }}</td>
            </tr>
        </table>
    </div>

    <!-- Başlık (Title) -->
    <div style="text-align: center; margin-top: 57mm; font-size: 14pt; font-weight: bold; color: #333;">
        {{ $offer->title }}
    </div>

    <!-- İlgili Şirket -->
    <div style="text-align: left; margin-top: 3mm; font-size: 12pt; font-weight: normal; color: #333;">
        {{ $offer->company }}
    </div>

    <!-- İlgili Kişi -->
    <div style="text-align: left; margin-top: 3mm; font-size: 12pt; font-weight: normal; color: #333;">
        Sayın {{ $offer->person_name }},
    </div>

    <!-- Ürün veya Hizmet Tablosu -->
    <div style="margin-top: 10mm;">
        <table style="width: 100%; border-collapse: collapse; font-size: 8pt; text-align: left;">
            <thead>
                <tr style="background-color: #0056b3; color: white;">
                    <th style="padding: 6px; border: 1px solid #ddd;">Açıklama</th>
                    <th style="padding: 6px; border: 1px solid #ddd;">Adet</th>
                    <th style="padding: 6px; border: 1px solid #ddd;">Birim Fiyat</th>
                    <th style="padding: 6px; border: 1px solid #ddd;">Toplam</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($offer->unit_price) && !empty($offer->total_price))
                    @foreach (explode(',', $offer->unit_price) as $index => $unit_price)
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ddd;">
                            {{ explode(',', $offer->explanation)[$index] ?? 'Açıklama yok' }}
                        </td>
                        <td style="padding: 6px; border: 1px solid #ddd;">{{ explode(',', $offer->piece)[$index] ?? '0' }}</td>
                        <td style="padding: 6px; border: 1px solid #ddd;">{{ number_format($unit_price, 2) }} {{ $offer->currency }}</td>
                        <td style="padding: 6px; border: 1px solid #ddd;">{{ number_format(explode(',', $offer->total_price)[$index] ?? 0, 2) }} {{ $offer->currency }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="padding: 6px; text-align: center; border: 1px solid #ddd;">Veri bulunamadı.</td>
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
    <div style="margin-top: 10mm; text-align: left; font-size: 10pt; color: #333;">
        <em>*Fiyatlara KDV dahil değildir ve peşin ödeme geçerlidir.</em>
    </div>

    <!-- Onay Kutusu -->
    <!-- 15-20 px yukarı: top: 215mm (önce 230mm idi) -->
    <div class="onay-kutusu" style="position: absolute; top: 210mm; left: 130mm; text-align: center; font-size: 12pt;">
        <label style="font-weight: bold; color: #333; display: block; margin-bottom: 5px;">Onay</label>
        <div style="width: 60mm; height: 20mm; border: 2px solid #333; border-radius: 3px; cursor: pointer;"></div>
    </div>

    <!-- Notlar Tablo Satırı -->
    <!-- 15-20 px yukarı: top: 225mm (önce 240mm idi) -->
    <div style="position: absolute; top: 220mm; left: 20mm; right: 20mm;">
        <table style="width: 60%; border-collapse: collapse; font-size: 11pt;">
            <tr>
            </tr>
        </table>
    </div>

    <!-- Şirket Bilgileri -->
    <div style="position: absolute; bottom: 10mm; left: 0; width: 100%; text-align: center; font-size: 12pt; color: #333;">
        <strong>TA Teknik Otomasyon Sanayi ve Ticaret Limited Şirketi</strong><br>
        info@tateknik.com
    </div>
</div>
<!-- Sayfa 1 Sonu -->

<!-- Sayfa 2 (Detay) -->
<div class="container-page2" style="background: url('{{ asset('images/background.png') }}') no-repeat center center; background-size: cover; width: 210mm; height: 297mm; margin: auto; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.1); position: relative; margin-top: 20px;">
    <!-- Logo (ikinci sayfa) -->
    <div style="position: absolute; top: 10mm; left: 20mm;">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 120px; height: auto;">
    </div>

    <!-- "Detay" Başlık (sola yaslı, daha küçük font, daha aşağı) -->
    <!-- Örneğin 32mm yukarı, font-size: 22pt (isteğe bağlı) -->
    <div style="margin-top: 32mm; margin-left: 155mm; font-size: 22pt; font-weight: bold; color: #333;">
        Detaylar
    </div>

    <!-- Detay İçerik (offer->details) biraz daha aşağı -->
    <div style="margin-left: 20mm; margin-right: 20mm; margin-top: 20mm; font-size: 12pt; color: #333; text-align: left;">
        {!! nl2br(strip_tags($offer->details ?? 'Detay bulunamadı.')) !!}
    </div>

    <!-- Şirket Bilgileri (2. sayfa) -->
    <div style="position: absolute; bottom: 10mm; left: 0; width: 100%; text-align: center; font-size: 12pt; color: #333;">
        <strong>TA Teknik Otomasyon Sanayi ve Ticaret Limited Şirketi</strong><br>
        info@tateknik.com
    </div>
</div>
<!-- Sayfa 2 Sonu -->

@endsection

@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    document.getElementById("downloadPdfBtn").addEventListener("click", function () {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF('p', 'mm', 'a4');

        // İki container: sayfa1, sayfa2
        const containers = document.querySelectorAll(".container-page1, .container-page2");

        function processContainer(index) {
            if (index >= containers.length) {
                // Son container işlendiğinde PDF'i kaydet
                doc.save("Teklif_{{ $offer->offer_no }}.pdf");
                return;
            }

            html2canvas(containers[index], {
                scale: 1.5,
                useCORS: true
            }).then(canvas => {
                let imgData = canvas.toDataURL("image/png");
                let imgWidth = 210; // A4 genişliği mm
                let imgHeight = (canvas.height * imgWidth) / canvas.width;

                if (index === 0) {
                    // İlk sayfa
                    doc.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight, '', 'FAST'); // kaliteyi düşürür
                } else {
                    // İkinci sayfa
                    doc.addPage();
                    doc.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight, '', 'FAST'); // kaliteyi düşürür
                }

                // Sonraki container'a geç
                processContainer(index + 1);
            }).catch(error => {
                console.error("PDF oluşturulurken hata oluştu:", error);
            });
        }

        // Başlat
        processContainer(0);
    });
</script>
@endsection
