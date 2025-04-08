@extends('voyager::master')

@section('content')
<!-- Tüm sayfa bembeyaz olsun diye dış bir div kullanıyoruz. -->
<div style="background-color: #fff; min-height: 100vh;">

    <!-- İçeriklerin hizalaması ve genişlik ayarı için container -->
    <div class="container py-4">
        
        <!-- Kart (Card) -->
        <div class="card border-0 shadow-sm" style="background-color: #fff;">
            
            <!-- Kart Başlığı (header) -->
            <!-- justify-content-between ile başlık solda, buton sağda kalır -->
            <div class="card-header border-0 bg-white d-flex align-items-center justify-content-between" style="padding: 1rem;">
                <!-- Siyah renkte başlık -->
                <h4 class="mb-0" style="color: #000;">
                    {{ $offer->title ?? 'Teklif Başlığı Bulunamadı' }}
                </h4>
                
                <!-- Ekle Butonu (sağ tarafta) -->
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#faturaModal">
                    <i class="voyager-plus"></i> Ekle
                </button>
            </div>
            
            <!-- Kart Gövdesi -->
            <div class="card-body" style="background-color: #fff;">
                <!-- Proje Bilgileri Tablosu -->
                <table class="table table-bordered mb-4">
                    <thead class="thead-light">
                        <tr>
                            <th>Kabul Bedeli</th>
                            <th>Çalışacak Kişi Sayısı</th>
                            <th>Proje Bitiş Tarihi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $project->kabul_bedeli }}</td>
                            <td>{{ $project->caliscak_kisi_sayisi }}</td>
                            <td>{{ $project->proje_bitis_tarihi }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Fatura Listesi Başlık -->
                <h5 class="mb-3">Faturalar</h5>

                @if($bills->count() > 0)
                    <!-- Fatura Tablosu -->
                    <table class="table table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Fatura Adı</th>
                                <th>Fatura Numarası</th>
                                <th>Fatura Bedeli</th>
                                <th>Para Birimi</th>
                                <th>Tedarikçi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills as $bill)
                                <tr>
                                    <td>{{ $bill->fatura_adi }}</td>
                                    <td>{{ $bill->fatura_numarasi }}</td>
                                    <td>{{ $bill->fatura_bedeli }}</td>
                                    <td>{{ $bill->para_birimi }}</td>
                                    <td>{{ $bill->tedarikci }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Bu projeye ait henüz fatura bulunmuyor.</p>
                @endif
            </div>
            <!-- Kart Gövdesi Sonu -->

        </div>
        <!-- Kart Sonu -->
    </div>
    <!-- Container Sonu -->

    <!-- Güncel Döviz Kurlarını Gösteren Bölüm -->
    <div class="container py-4">
        <hr>
        <div class="row">
            <div class="col-md-12 text-center">
                <p>
                    <strong>Güncel Dolar Kuru:</strong>
                    <i class="fa fa-usd" aria-hidden="true"></i>
                    {{ $usdRate }} TL
                </p>
                <p>
                    <strong>Güncel Euro Kuru:</strong>
                    <i class="fa fa-eur" aria-hidden="true"></i>
                    {{ $eurRate }} TL
                </p>
            </div>
        </div>
    </div>
    <!-- Güncel Döviz Kurları Bölümü Sonu -->

</div>
<!-- Tüm sayfa beyaz arkaplan kapatma -->

<!-- Fatura Ekle Modal -->
<div class="modal fade" id="faturaModal" tabindex="-1" role="dialog" aria-labelledby="faturaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Form, fatura verilerini kaydedeceğiniz rotaya gider -->
            <form action="/fatura/store" method="POST">
                @csrf

                <!-- Gizli input: project_id -->
                <input type="hidden" name="project_id" value="{{ $project->id }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="faturaModalLabel">Fatura Ekle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- 1) Fatura Adı -->
                    <div class="form-group">
                        <label for="fatura_adi">Fatura Adı</label>
                        <input type="text" class="form-control" id="fatura_adi" name="fatura_adi" required>
                    </div>
                    <!-- 2) Fatura Numarası (min=0) -->
                    <div class="form-group">
                        <label for="fatura_numarasi">Fatura Numarası</label>
                        <input type="number" class="form-control" id="fatura_numarasi" name="fatura_numarasi" min="0" required>
                    </div>
                    <!-- 3) Fatura Bedeli (min=0) -->
                    <div class="form-group">
                        <label for="fatura_bedeli">Fatura Bedeli</label>
                        <input type="number" class="form-control" id="fatura_bedeli" name="fatura_bedeli" min="0" required>
                    </div>
                    <!-- 3.1) Para Birimi (Dropdown) -->
                    <div class="form-group">
                        <label for="para_birimi">Para Birimi</label>
                        <select class="form-control" id="para_birimi" name="para_birimi" required>
                            <option value="">Seçiniz</option>
                            <option value="TRY">Türk Lirası</option>
                            <option value="USD">Dolar</option>
                            <option value="EUR">Euro</option>
                        </select>
                    </div>
                    <!-- 4) Tedarikçi -->
                    <div class="form-group">
                        <label for="tedarikci">Tedarikçi</label>
                        <input type="text" class="form-control" id="tedarikci" name="tedarikci" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- Modal Kapat Butonu -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <!-- Kaydet Butonu -->
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
