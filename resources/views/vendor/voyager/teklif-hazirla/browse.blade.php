@extends('voyager::master')

@section('content')
<div class="container-fluid">
    <!-- Sayfa Başlığı, Ekle Butonu ve Durum Filtre Dropdown'ı -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
        <!-- Sol kısım: Başlık -->
        <h1 class="page-title" style="margin: 0;">
            <i class="voyager-documentation"></i> Teklifler
        </h1>

        <!-- Sağ kısım: Dropdown ve Ekle Butonu -->
        <div style="display: flex; align-items: center;">
            <!-- Durum Filtre Dropdown -->
            <select id="statusFilter" class="form-control" style="width: 250px; margin-right: 10px;">
                <option value="">Tüm Durumlar</option>
                <option value="1">Teklifin onaylanması bekleniyor</option>
                <option value="0">Teklif oluşturuldu</option>
                <option value="2">Proje oluşturuldu</option>
                <option value="3">Teklif iptal edildi</option>
                <option value="4">Proje iptal edildi</option>
            </select>

            <!-- Ekle Butonu -->
            <a href="#" class="btn btn-success" id="addOfferButton">
                <i class="voyager-plus"></i> Ekle
            </a>
        </div>
    </div>

    <!-- Teklifler Tablosu -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <!-- Yeni eklenen sütun: Küçük yuvarlak (durum göstergesi) -->
                        <th>#</th>
                        <th>ID</th>
                        <th>Firma</th>
                        <th>Teklif No</th>
                        <th>Talep No</th>
                        <th>Başlık</th>
                        <th>Teslimat Tarihi</th>
                        <th>İşlemler</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offers as $offer)
  @php
    $circleColor = 'gray';
    switch ($offer->is_editable) {
        case 1: $circleColor = 'yellow'; break;
        case 0: $circleColor = 'orange'; break;
        case 2: $circleColor = 'green';  break;
        case 3: $circleColor = 'red';    break;
        case 4: $circleColor = 'red';    break;
        default: $circleColor = 'gray';
    }

    // AÇIKLAMA: önce JSON dene, olmazsa eski CSV'ye dön
    $desc = [];
    if (is_array($offer->explanation)) {
        $desc = $offer->explanation;
    } else {
        $decoded = json_decode($offer->explanation ?? '', true);
        $desc = is_array($decoded) ? $decoded : explode(',', $offer->explanation ?? '');
    }

    $qty   = is_array($offer->quantity)   ? $offer->quantity   : explode(',', $offer->piece        ?? '');
    $unit  = is_array($offer->unit_price) ? $offer->unit_price : explode(',', $offer->unit_price   ?? '');
    $total = is_array($offer->total_price)? $offer->total_price: explode(',', $offer->total_price  ?? '');
@endphp

                        <!-- Her satıra data-id ve data-status ekledik -->
                        <tr data-id="{{ $offer->id }}" data-status="{{ $offer->is_editable }}">
                            <!-- Durum göstergesi sütunu: Küçük yuvarlak (16x16 px) -->
                            <td style="text-align: center;">
                                <div style="width: 16px; height: 16px; border-radius: 50%; margin: 0 auto; background-color: {{ $circleColor }};"></div>
                            </td>
                            <td>{{ $offer->id }}</td>
                            <td>{{ $offer->company }}</td>
                            <td>{{ $offer->offer_no }}</td>
                            <td>{{ $offer->demand_no }}</td>
                            <td>{{ $offer->title }}</td>
                            <td>{{ $offer->delivery_date }}</td>
                            <td>
                                <!-- Görüntüle Butonu -->
                                <a href="{{ route('offer.view', ['id' => $offer->id]) }}" class="btn btn-info btn-sm" title="Görüntüle">
                                    <i class="voyager-eye"></i>
                                </a>

                                <!-- Düzenle Butonu (sadece is_editable==1) -->
                                <button class="btn btn-warning btn-sm edit-offer-button"
                                    title="Düzenle"
                                    data-id="{{ $offer->id }}"
                                    data-title="{{ $offer->title }}"
                                    data-demand_no="{{ $offer->demand_no }}"
                                    data-delivery_date="{{ $offer->delivery_date }}"
                                    data-company="{{ $offer->company }}"
                                    data-person_name="{{ e($offer->person_name) }}"
                                    data-person_email="{{ e($offer->person_email) }}"
                                    data-currency="{{ e($offer->currency) }}"
                                    data-desc='@json($desc)'
                                    data-qty='@json($qty)'
                                    data-unit='@json($unit)'
                                    data-total='@json($total)'
                                    data-notes="{{ e($offer->notes ?? '') }}"
                                    data-is_editable="{{ $offer->is_editable }}"
                                    style="display: {{ $offer->is_editable == 1 ? 'inline-block' : 'none' }};">
                                    <i class="voyager-pen"></i>
                                </button>

                                <!-- Detay Butonu (sadece is_editable==1) -->
                                <button class="btn btn-sm detail-offer-button"
                                    title="Detay"
                                    data-id="{{ $offer->id }}"
                                    style="
                                        display: {{ $offer->is_editable == 1 ? 'inline-block' : 'none' }};
                                        background-color: #9B59B6;
                                        border-color: #9B59B6;
                                        color: #fff;
                                    ">
                                    <i class="voyager-list"></i>
                                </button>
                                
                                <!-- Oluştur Butonu (sadece is_editable==1) -->
                                <button class="btn btn-success btn-sm create-offer-button"
                                    title="Oluştur"
                                    data-id="{{ $offer->id }}"
                                    data-is_editable="{{ $offer->is_editable }}"
                                    style="display: {{ $offer->is_editable == 1 ? 'inline-block' : 'none' }};">
                                    <i class="voyager-file-text"></i>
                                </button>
                                
                                <!-- Gönder Butonu (sadece is_editable==0) -->
                                <button class="btn btn-dark btn-sm send-offer-button"
                                    title="Gönder"
                                    data-id="{{ $offer->id }}"
                                    data-is_editable="{{ $offer->is_editable }}"
                                    style="display: {{ $offer->is_editable == 0 ? 'inline-block' : 'none' }};">
                                    <i class="voyager-paper-plane"></i>
                                </button>

                                <!-- Proje Oluştur Butonu (sadece is_editable==0) -->
                                <button class="btn btn-sm project-create-button"
                                    title="Proje Oluştur"
                                    data-id="{{ $offer->id }}"
                                    data-kabul_bedeli="{{ $offer->kabul_bedeli ?? '' }}"  
                                    data-caliscak_kisi="{{ $offer->caliscak_kisi_sayisi ?? '' }}"
                                    style="
                                        display: {{ $offer->is_editable == 0 ? 'inline-block' : 'none' }};
                                        background-color: #006400;
                                        border-color: #006400;
                                        color: #fff;
                                    ">
                                    <i class="voyager-folder"></i>
                                </button>

                                <!-- Teklifi İptal Et Butonu (sadece is_editable==0) -->
                                <button class="btn btn-danger btn-sm project-cancel-button"
                                    title="Teklifi İptal Et"
                                    data-id="{{ $offer->id }}"
                                    style="display: {{ $offer->is_editable == 0 ? 'inline-block' : 'none' }};">
                                    <i class="voyager-x"></i>
                                </button>

                                <!-- Projeye Git Butonu (sadece is_editable==2) -->
                                <a href="{{ route('project.view', ['offerId' => $offer->id]) }}"
                                class="btn btn-primary btn-sm"
                                title="Projeye Git"
                                style="display: {{ $offer->is_editable == 2 ? 'inline-block' : 'none' }};">
                                    <i class="voyager-forward"></i>
                                </a>
                            </td>
                            <!-- Durum sütunu -->
                            <td>
                                @switch($offer->is_editable)
                                    @case(1)
                                        Teklifin onaylanması bekleniyor
                                        @break
                                    @case(0)
                                        Teklif oluşturuldu
                                        @break
                                    @case(2)
                                        Proje oluşturuldu
                                        @break
                                    @case(3)
                                        Teklif iptal edildi
                                        @break
                                    @case(4)
                                        Proje iptal edildi
                                        @break
                                    @default
                                        Durum bilinmiyor
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Henüz bir teklif eklenmemiş.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Sayfalama linki -->
            <div class="d-flex justify-content-end mt-3">
                <nav aria-label="Sayfalama">
                    <ul class="pagination justify-content-end">
                        {{-- Önceki Sayfa Butonu --}}
                        @if ($offers->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">Önceki</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $offers->previousPageUrl() }}" rel="prev">Önceki</a>
                            </li>
                        @endif

                        {{-- Sayfa Numaraları --}}
                        @foreach ($offers->links()->elements as $element)
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <li class="page-item {{ $page == $offers->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Sonraki Sayfa Butonu --}}
                        @if ($offers->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $offers->nextPageUrl() }}" rel="next">Sonraki</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Sonraki</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Teklif Oluştur / Düzenle Modal -->
<div class="modal fade" id="addOfferModal" tabindex="-1" role="dialog" aria-labelledby="addOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('offer.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addOfferModalLabel">Teklif Oluştur</h5>
                </div>
                <div class="modal-body">
                    <!-- Başlık -->
                    <div class="form-group">
                        <label for="title">Başlık</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Teklif başlığını giriniz" required>
                    </div>
                    <!-- Talep Numarası -->
                    <div class="form-group">
                        <label for="demand_no">Talep Numarası</label>
                        <input type="text" class="form-control" id="demand_no" name="demand_no" placeholder="Talep numarasını giriniz" required>
                    </div>
                    <!-- Teslimat Tarihi -->
                    <div class="form-group">
                        <label for="delivery_date">Teslimat Tarihi</label>
                        <input type="date" class="form-control" id="delivery_date" name="delivery_date" required>
                    </div>
                    <!-- Firma Adı Dropdown -->
                    <div class="form-group">
                        <label for="company">Firma Adı</label>
                        <select class="form-control" id="company" name="company" required>
                            <option value="" disabled selected>Firma seçiniz</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_name }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- İlgili Kişi Adı Dropdown -->
                    <div class="form-group">
                        <label for="person_name">İlgili Kişi Adı</label>
                        <select class="form-control" id="person_name" name="person_name" required>
                            <option value="" disabled selected>Kişi seçiniz</option>
                        </select>
                    </div>
                    <!-- İlgili Kişi Mail -->
                    <div class="form-group">
                        <label for="person_email">İlgili Mail Adresi</label>
                        <input type="email" class="form-control" id="person_email" name="person_email" placeholder="Email adresi otomatik doldurulur" readonly>
                    </div>
                    <!-- Para Birimi -->
                    <div class="form-group">
                        <label for="currency">Para Birimi</label>
                        <select class="form-control" id="currency" name="currency" required>
                            <option value="" disabled selected>Para Birimi Seçiniz</option>
                            <option value="₺">Türk Lirası (₺)</option>
                            <option value="$">Dolar ($)</option>
                            <option value="€">Euro (€)</option>
                        </select>
                    </div>
                    <!-- Tablo -->
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sıra no</th>
                                    <th>Açıklama</th>
                                    <th>Adet</th>
                                    <th>Birim fiyat</th>
                                    <th>Toplam fiyat</th>
                                </tr>
                            </thead>
                            <tbody id="offerTableBody">
                                <tr>
                                    <td>1</td>
                                    <td><textarea name="description[]" class="form-control desc-input" rows="2" placeholder="Açıklama" required></textarea></td>
                                    <td><input type="number" name="quantity[]" class="form-control quantity" step="0.01" min="0" required></td>
                                    <td><input type="number" name="unit_price[]" class="form-control unit-price" step="0.01" min="0" required></td>
                                    <td><input type="number" name="total_price[]" class="form-control total-price" readonly></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Genel Toplam</th>
                                    <th>
                                        <input type="text" id="grandTotalView" class="form-control" readonly>
                                        <input type="hidden" name="grand_total" id="grandTotal" value="0">
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- Satır Ekle / Sil Butonları -->
                    <div class="d-flex justify-content-start mt-3">
                        <button type="button" class="btn btn-primary me-2" id="addRowButton">Satır Ekle</button>
                        <button type="button" class="btn btn-danger" id="removeRowButton">Satır Sil</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal">Kapat</button>
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detay Modal (CKEditor eklendi) -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Teklife Detay Ekle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="detailInput">Detay</label>
          <textarea class="form-control" id="detailInput" rows="5" placeholder="Detayları buraya giriniz"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
        <button type="button" class="btn btn-primary" id="saveDetailBtn">Kaydet</button>
      </div>
    </div>
  </div>
</div>

<!-- Proje Oluştur Modal -->
<div class="modal fade" id="projectCreateModal" tabindex="-1" role="dialog" aria-labelledby="projectCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="projectCreateForm" method="POST" action="/project/store">
                @csrf
                <input type="hidden" name="offer_id" id="project_offer_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectCreateModalLabel">Proje Oluştur</h5>
                </div>
                <div class="modal-body">
                    <!-- Teklif Kabul Bedeli -->
                    <div class="form-group">
                        <label for="kabulBedeli">Teklif Kabul Bedeli</label>
                        <input type="number" class="form-control" id="kabulBedeli" min="1" name="kabul_bedeli">
                    </div>
                    <!-- Çalışacak Kişi Sayısı -->
                    <div class="form-group">
                        <label for="caliscakKisi">Çalışacak Kişi Sayısı</label>
                        <input type="number" class="form-control" id="caliscakKisi" min="1" name="caliscak_kisi_sayisi">
                    </div>
                    <!-- Proje Bitiş Tarihi -->
                    <div class="form-group">
                        <label for="projectEndDate">Proje Bitiş Tarihi</label>
                        <input type="date" class="form-control" id="projectEndDate" name="project_end_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<!-- CKEditor CDN -->
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function () {
    function escapeHtml(str) {
    str = (str ?? '').toString();
    return str.replace(/[&<>"']/g, function (m) {
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m];
    });
  }
        // CKEditor'ü 'detailInput' textarea üzerinde başlatma
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('detailInput');
        }

        // Durum filtreleme
        $('#statusFilter').on('change', function () {
            var selectedStatus = $(this).val();
            $('tbody tr').each(function () {
                var rowStatus = $(this).data('status').toString();
                if (selectedStatus === '' || rowStatus === selectedStatus) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Modal açılma
        $('#addOfferButton').on('click', function (e) {
            e.preventDefault();
            $('#addOfferModal').modal('show');
        });

        // Teslimat tarihi için min değeri ayarla
        const today = new Date().toISOString().split('T')[0];
        $('#delivery_date').attr('min', today);

        $('#addRowButton').on('click', function () {
            const rowNumber = $('#offerTableBody tr').length + 1;
            $('#offerTableBody').append(`
                <tr>
                    <td>${rowNumber}</td>
                    <td><textarea name="description[]" class="form-control desc-input" rows="2" required></textarea></td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" step="0.01" min="0" required></td>
                    <td><input type="number" name="unit_price[]" class="form-control unit-price" step="0.01" min="0" required></td>
                    <td><input type="number" name="total_price[]" class="form-control total-price" readonly></td>
                </tr>
            `);
            calcGrandTotal();
        });

        $('#removeRowButton').on('click', function () {
            const rowCount = $('#offerTableBody tr').length;
            if (rowCount > 1) {
                $('#offerTableBody tr:last').remove();
                calcGrandTotal();
            } else {
                alert('İlk satır silinemez!');
            }
        });

        // Firma seçildiğinde kullanıcı getir
        $('#company').on('change', function () {
            const companyName = $(this).val();
            $.ajax({
                url: '/get-users-by-company',
                type: 'GET',
                data: { company_name: companyName },
                success: function (users) {
                    $('#person_name').empty().append('<option value="" disabled selected>Kişi seçiniz</option>');
                    users.forEach(function (user) {
                        $('#person_name').append(`<option value="${user.user_name}" data-email="${user.email}">${user.user_name}</option>`);
                    });
                },
                error: function (xhr) {
                    alert('Bir hata oluştu. Tekrar deneyin.');
                }
            });
        });

        $('#person_name').on('change', function () {
            const email = $(this).find(':selected').data('email');
            $('#person_email').val(email);
        });

        // Virgül/nokta farkını normalize eden helper
        function toNum(v) {
            return parseFloat(String(v).replace(',', '.')) || 0;
        }

        // Sayıyı TR formatında göster
        function fmt(v) {
            return v.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Satır toplamını hesapla
        function calcRow($tr) {
            const qty  = toNum($tr.find('.quantity').val());
            const unit = toNum($tr.find('.unit-price').val());
            const total = qty * unit;
            $tr.find('.total-price').val(total.toFixed(2)); // DB için noktalı
        }

        // Tüm satırları toplayıp footer'a yaz
        function calcGrandTotal() {
            let sum = 0;
            $('#offerTableBody tr').each(function () {
                sum += toNum($(this).find('.total-price').val());
            });
            $('#grandTotal').val(sum.toFixed(2));        // DB için
            $('#grandTotalView').val(fmt(sum));          // kullanıcıya güzel gösterim
        }

        // Adet & birim fiyat değişince toplamı güncelle
        $(document).on('input change', '.quantity, .unit-price', function () {
            const $tr = $(this).closest('tr');
            calcRow($tr);
            calcGrandTotal();
        });

        // Form submit olurken boş kalmış satır olmasın
        $('#addOfferModal form').on('submit', function () {
            $('#offerTableBody tr').each(function () { calcRow($(this)); });
            calcGrandTotal();
        });

        // Kapat butonuyla modal kapatma
        $('.close-modal').on('click', function () {
            $('#addOfferModal').modal('hide');
        });

        // Düzenle butonuna tıklama
        $(document).on('click', '.edit-offer-button', function () {
            // ------------- Genel bilgiler -------------
            const id          = $(this).data('id');
            const title       = $(this).data('title') || '';
            const demandNo    = $(this).data('demand_no') || '';
            const deliveryDate= $(this).data('delivery_date') || '';
            const company     = $(this).data('company');
            const personName  = $(this).data('person_name');
            const personEmail = $(this).data('person_email');
            const currency    = $(this).data('currency') || '';
            const notes       = $(this).data('notes') || '';

            // ------------- Satır dizileri -------------
            // jQuery .data() camelCase karıştırmasın diye attr + JSON.parse kullanıyoruz
            const descriptionArray = JSON.parse($(this).attr('data-desc')  || '[]');
            const quantityArray    = JSON.parse($(this).attr('data-qty')   || '[]');
            const unitPriceArray   = JSON.parse($(this).attr('data-unit')  || '[]');
            const totalPriceArray  = JSON.parse($(this).attr('data-total') || '[]');

            // ------------- Formu update moduna al -------------
            const $form = $('#addOfferModal form');
            $form.attr('action', `/offer/update/${id}`).attr('method', 'POST');
            if (!$form.find('input[name="_method"]').length) {
                $form.append('<input type="hidden" name="_method" value="PUT">');
            }

            // ------------- Üst alanları doldur -------------
            $('#title').val(title);
            $('#demand_no').val(demandNo);
            $('#delivery_date').val(deliveryDate);
            $('#currency').val(currency);
            $('#notes').val(notes);

            // Firma & kişi dropdownlarını doldur
            $('#company').val(company);
            $.ajax({
                url: '/get-users-by-company',
                type: 'GET',
                data: { company_name: company },
                success: function (users) {
                    $('#person_name').empty().append('<option value="" disabled>Kişi seçiniz</option>');
                    users.forEach(function (user) {
                        $('#person_name').append(`<option value="${user.user_name}" data-email="${user.email}">${user.user_name}</option>`);
                    });
                    $('#person_name').val(personName);
                    $('#person_email').val(personEmail);
                }
            });

            // ------------- Satırları doldur -------------
            const $tbody = $('#offerTableBody');
            $tbody.empty();                                   // !!! BURADA $$ DEĞİL $

            if (!descriptionArray.length) {
                // En az 1 boş satır
               $tbody.append(`
                <tr>
                    <td>1</td>
                    <td><textarea name="description[]" class="form-control desc-input" rows="2" required></textarea></td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" step="0.01" min="0" required></td>
                    <td><input type="number" name="unit_price[]" class="form-control unit-price" step="0.01" min="0" required></td>
                    <td><input type="number" name="total_price[]" class="form-control total-price" readonly></td>
                </tr>
                `);

                            } else {
                            descriptionArray.forEach((d, i) => {
                $tbody.append(`
                    <tr>
                    <td>${i + 1}</td>
                    <td>
                        <textarea name="description[]" class="form-control desc-input" rows="2" required>${
                        escapeHtml(d)
                        }</textarea>
                    </td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" step="0.01" min="0" value="${quantityArray[i] ?? ''}" required></td>
                    <td><input type="number" name="unit_price[]" class="form-control unit-price" step="0.01" min="0" value="${unitPriceArray[i] ?? ''}" required></td>
                    <td><input type="number" name="total_price[]" class="form-control total-price" value="${totalPriceArray[i] ?? ''}" readonly></td>
                    </tr>
                `);
                });

            }

            // Modal başlığını değiştir ve aç
            $('#addOfferModalLabel').text('Teklif Düzenle');
            $('#addOfferModal').modal('show');
            $('#addOfferModal').on('shown.bs.modal', function () {
            calcGrandTotal();
});

        });

        // Detay butonuna tıklanınca offerId'yi modal'a aktar ve modalı aç
        $(document).on('click', '.detail-offer-button', function() {
            var offerId = $(this).data('id');
            $('#detailModal').data('offerId', offerId).modal('show');
        });

        $(document).on('keydown', '.desc-input', function(e){
        if (e.key === 'Enter') {
            // Sadece satır atla, form submit olmasın
            e.stopPropagation();
        }
        });

        // Detay modalındaki Kaydet butonuna tıklayınca CKEditor üzerinden veriyi alıp AJAX ile gönderme
        $('#saveDetailBtn').on('click', function() {
            var detailText = CKEDITOR.instances.detailInput.getData();
            var offerId = $('#detailModal').data('offerId');
            $.ajax({
                url: '/offer/update-details/' + offerId,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    details: detailText
                },
                success: function(response) {
                    console.log(response.message);
                    $('#detailModal').modal('hide');
                },
                error: function(xhr) {
                    console.error('Detay güncelleme hatası:', xhr.responseText);
                }
            });
        });

        // Oluştur butonuna tıklama (sayfa yenilemeden güncelleme)
        $(document).on('click', '.create-offer-button', function () {
            const offerId = $(this).data('id');
            $.ajax({
                url: `/offer/set-editable/${offerId}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    is_editable: 0
                },
                success: function () {
                    $(`button.edit-offer-button[data-id="${offerId}"]`).hide();
                    $(`button.create-offer-button[data-id="${offerId}"]`).hide();
                    $(`button.send-offer-button[data-id="${offerId}"]`).show();
                    $(`button.project-create-button[data-id="${offerId}"]`).show();
                    $(`button.project-cancel-button[data-id="${offerId}"]`).show();
                    
                    var newStatus = 0;
                    var circleColor, statusText;
                    switch(newStatus){
                        case 1:
                            circleColor = 'yellow';
                            statusText = 'Teklifin onaylanması bekleniyor';
                            break;
                        case 0:
                            circleColor = 'orange';
                            statusText = 'Teklif oluşturuldu';
                            break;
                        case 2:
                            circleColor = 'green';
                            statusText = 'Proje oluşturuldu';
                            break;
                        case 3:
                            circleColor = 'red';
                            statusText = 'Teklif iptal edildi';
                            break;
                        case 4:
                            circleColor = 'red';
                            statusText = 'Proje iptal edildi';
                            break;
                        default:
                            circleColor = 'gray';
                            statusText = 'Durum bilinmiyor';
                    }
                    const $row = $(`tr[data-id="${offerId}"]`);
                    $row.attr('data-status', newStatus);
                    $row.find('td:first div').css('background-color', circleColor);
                    $row.find('td:last').text(statusText);
                },
                error: function (error) {
                    console.error('Güncelleme sırasında bir hata oluştu:', error);
                }
            });
        });

        // Gönder butonuna tıklama
        $(document).on('click', '.send-offer-button', function () {
            const offerId = $(this).data('id');
            if (!confirm('Bu teklifi göndermek istediğinizden emin misiniz?')) return;
            $.ajax({
                url: `/offer/send/${offerId}`,
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    alert(response.message || 'Teklif başarıyla gönderildi.');
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Teklif gönderimi sırasında bir hata oluştu.');
                }
            });
        });

        // Proje Oluştur butonuna tıklama
        $(document).on('click', '.project-create-button', function () {
            const offerId = $(this).data('id');
            const kabulBedeli = $(this).data('kabul_bedeli') || '';
            const caliscakKisi = $(this).data('caliscak_kisi') || '';
            $('#project_offer_id').val(offerId);
            $('#kabulBedeli').val(kabulBedeli);
            $('#caliscakKisi').val(caliscakKisi);
            $('#projectCreateModal').modal('show');
        });

        // Projeye Git butonuna tıklama - yeni sayfa açılması
        $(document).on('click', '.go-project-button', function () {
            const url = $(this).data('url');
            window.open(url, '_blank');
        });

        // Teklifi İptal Et butonuna tıklama
        $(document).on('click', '.project-cancel-button', function () {
            const offerId = $(this).data('id');
            if (!confirm('Teklifi iptal etmek istediğinizden emin misiniz?')) return;
            $.ajax({
                url: `/offer/cancel/${offerId}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    is_editable: 3
                },
                success: function () {
                    $(`button.edit-offer-button[data-id="${offerId}"]`).hide();
                    $(`button.send-offer-button[data-id="${offerId}"]`).hide();
                    $(`button.project-create-button[data-id="${offerId}"]`).hide();
                    $(`button.project-cancel-button[data-id="${offerId}"]`).hide();
                    
                    var newStatus = 3;
                    var circleColor = 'red';
                    var statusText = 'Teklif iptal edildi';
                    const $row = $(`tr[data-id="${offerId}"]`);
                    $row.attr('data-status', newStatus);
                    $row.find('td:first div').css('background-color', circleColor);
                    $row.find('td:last').text(statusText);
                },
                error: function (error) {
                    console.error('Teklif iptali sırasında bir hata oluştu:', error);
                }
            });
        });

        // Proje Oluştur Modal açıldığında min değeri ayarla
        $('#projectCreateModal').on('show.bs.modal', function () {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const localDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            $('#projectEndDate').attr('min', localDateTime);
        });

        // Sayfa yüklendiğinde mevcut butonların görünürlük ayarları
        $('.create-offer-button').each(function () {
            const isEditable = $(this).data('is_editable');
            if (isEditable != 1) $(this).hide();
        });
        $('.send-offer-button').each(function () {
            const isEditable = $(this).closest('tr').data('status');
            if (isEditable != 0) $(this).hide();
        });
        $('.project-create-button').each(function () {
            const isEditable = $(this).closest('tr').data('status');
            if (isEditable != 0) $(this).hide();
        });
        $('.project-cancel-button').each(function () {
            const isEditable = $(this).closest('tr').data('status');
            if (isEditable != 0) $(this).hide();
        });
        $('.go-project-button').each(function () {
            const isEditable = $(this).closest('tr').data('status');
            if (isEditable != 2) $(this).hide();
        });
    });
</script>
@endsection
