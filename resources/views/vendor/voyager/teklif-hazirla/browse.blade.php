@extends('voyager::master')

@section('content')
<div class="container-fluid">
    <!-- Sayfa Başlığı ve Ekle Butonu -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
        <h1 class="page-title">
            <i class="voyager-documentation"></i> Teklifler
        </h1>
        <a href="#" class="btn btn-success" id="addOfferButton">
            <i class="voyager-plus"></i> Ekle
        </a>
    </div>

    <!-- Teklifler Tablosu -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Teklif No</th>
                        <th>Talep No</th>
                        <th>Başlık</th>
                        <th>Teslimat Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offers as $offer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $offer->offer_no }}</td>
                            <td>{{ $offer->demand_no }}</td>
                            <td>{{ $offer->title }}</td>
                            <td>{{ $offer->delivery_date }}</td>
                            <td>
                                <a href="{{ route('offer.view', ['id' => $offer->id]) }}" class="btn btn-info btn-sm">
                                    Görüntüle
                                </a>
                                @if ($offer->is_editable)
                                <button class="btn btn-warning btn-sm edit-offer-button"
                                    data-id="{{ $offer->id }}"
                                    data-title="{{ $offer->title }}"
                                    data-demand_no="{{ $offer->demand_no }}"
                                    data-delivery_date="{{ $offer->delivery_date }}"
                                    data-company="{{ $offer->company }}"
                                    data-person_name="{{ $offer->person_name }}"
                                    data-person_email="{{ $offer->person_email }}"
                                    data-currency="{{ $offer->currency }}"
                                    data-description="{{ $offer->explanation ?? '' }}"
                                    data-quantity="{{ $offer->piece ?? '' }}"
                                    data-unit_price="{{ $offer->unit_price ?? '' }}"
                                    data-total_price="{{ $offer->total_price ?? '' }}"
                                    data-notes="{{ $offer->notes ?? '' }}"
                                    data-is_editable="{{ $offer->is_editable }}">
                                    Düzenle
                                </button>
                                @endif
                                @if ($offer->is_editable == 1)
                                    <button class="btn btn-primary btn-sm create-offer-button"
                                        data-id="{{ $offer->id }}"
                                        data-is_editable="{{ $offer->is_editable }}">
                                        Oluştur
                                    </button>
                                @endif
                                <button class="btn btn-success btn-sm send-offer-button"
                                    data-id="{{ $offer->id }}"
                                    data-is_editable="{{ $offer->is_editable }}"
                                    style="display: {{ $offer->is_editable == 1 ? 'none' : 'inline-block' }};">
                                    Gönder
                                </button>
                            </td>
                            <td>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Henüz bir teklif eklenmemiş.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Modal -->
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
                                <!-- Dinamik olarak doldurulacak -->
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
                                    <td><input type="text" name="description[]" class="form-control" placeholder="Açıklama" required></td>
                                    <td><input type="number" name="quantity[]" class="form-control quantity" required></td>
                                    <td><input type="number" name="unit_price[]" class="form-control unit-price" required></td>
                                    <td><input type="number" name="total_price[]" class="form-control total-price" readonly></td>
                                    </tr>
                            </tbody>
                            </table>
                        </div>
                        <!-- Satır Ekle ve Satır Sil Butonları -->
                        <div class="d-flex justify-content-start mt-3">
                            <button type="button" class="btn btn-primary me-2" id="addRowButton">Satır Ekle</button>
                            <button type="button" class="btn btn-danger" id="removeRowButton">Satır Sil</button>
                        </div>

                    <!-- Notlar Alanı -->
                    <div class="form-group">
                        <label for="notes">Notlar</label>
                        <textarea class="form-control" id="notes" name="notes" placeholder="Notlarınızı buraya yazabilirsiniz"></textarea>
                    </div>

                    </div>

                    <div class="modal-footer">
                        <!-- Kapat Butonu -->
                        <button type="button" class="btn btn-secondary close-modal">Kapat</button>
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        // Modal açılma işlemi
        $('#addOfferButton').on('click', function (e) {
            e.preventDefault();
            $('#addOfferModal').modal('show');
        });

        // Geçmiş tarih seçimini engelle
        const today = new Date().toISOString().split('T')[0];
        $('#delivery_date').attr('min', today);

        // Satır ekleme işlemi
        $('#addRowButton').on('click', function () {
            const rowNumber = $('#offerTableBody tr').length + 1;
            $('#offerTableBody').append(`
                <tr>
                    <td>${rowNumber}</td>
                    <td><input type="text" name="description[]" class="form-control" placeholder="Açıklama"></td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" placeholder="Adet" step="1" min="0"></td>
                    <td><input type="number" name="unit_price[]" class="form-control unit-price" placeholder="Birim fiyat" step="0.01" min="0"></td>
                    <td><input type="number" name="total_price[]" class="form-control total-price" placeholder="Toplam fiyat" readonly></td>
                </tr>
            `);
        });

        // Satır silme işlemi
        $('#removeRowButton').on('click', function () {
            const rowCount = $('#offerTableBody tr').length;
            if (rowCount > 1) {
                $('#offerTableBody tr:last').remove();
            } else {
                alert('İlk satır silinemez!');
            }
        });

            // Firma seçildiğinde ilgili kullanıcıları getir
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

        // Adet - olmamalı ve birer birer artmalı
        $(document).on('input', '.quantity', function () {
            let value = parseInt($(this).val());
            if (isNaN(value) || value < 0) {
                $(this).val(0); // Negatif değer girilirse 0 olarak ayarlanır
            } else {
                $(this).val(value); // Tam sayı olarak kalır
            }
            updateTotalPrice($(this).closest('tr'));
        });

        // Birim fiyat - olmamalı ve küsuratlı değer alabilir
        $(document).on('input', '.unit-price', function () {
            let value = parseFloat($(this).val());
            if (isNaN(value) || value < 0) {
                $(this).val(0); // Negatif değer girilirse 0 olarak ayarlanır
            } else {
                $(this).val(value.toFixed(2)); // Ondalıklarla çalışır
            }
            updateTotalPrice($(this).closest('tr'));
        });

        // Toplam fiyat hesaplama
        function updateTotalPrice(row) {
            const quantity = parseInt(row.find('.quantity').val()) || 0;
            const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
            const totalPrice = quantity * unitPrice;
            row.find('.total-price').val(totalPrice.toFixed(2));
        }

        // Kapat butonu ile modal kapatma
        $('.close-modal').on('click', function () {
            $('#addOfferModal').modal('hide');
        });

       // Düzenle butonuna tıklama işlemi
       $(document).on('click', '.edit-offer-button', function () {
        const id = $(this).data('id');
        const title = $(this).data('title') || '';
        const demandNo = $(this).data('demand_no') || '';
        const deliveryDate = $(this).data('delivery_date') || '';
        const company = $(this).data('company') || '';
        const personName = $(this).data('person_name') || '';
        const personEmail = $(this).data('person_email') || '';
        const currency = $(this).data('currency') || '';
        const descriptions = $(this).data('description') || '';
        const quantities = $(this).data('quantity') || '';
        const unitPrices = $(this).data('unit_price') || '';
        const totalPrices = $(this).data('total_price') || '';
        const notes = $(this).data('notes') || '';

        // Eksik veri kontrolü
        console.log({ id, title, demandNo, deliveryDate, personName, personEmail, currency, descriptions, quantities, unitPrices, totalPrices });

        if (!title || !demandNo || !deliveryDate || !personName || !personEmail || !currency) {
            alert('Bazı veriler eksik. Lütfen kontrol edin.');
            return;
        }

        // Tablo verisi kontrolü
        if (!descriptions || !quantities || !unitPrices || !totalPrices) {
            alert('Açıklama, adet veya fiyat bilgileri eksik.');
            return;
        }

        // Split işlemi için string olup olmadığını kontrol et
        const descriptionArray = typeof descriptions === 'string' ? descriptions.split(',') : [];
        const quantityArray = typeof quantities === 'string' ? quantities.split(',') : [];
        const unitPriceArray = typeof unitPrices === 'string' ? unitPrices.split(',') : [];
        const totalPriceArray = typeof totalPrices === 'string' ? totalPrices.split(',') : [];

        // Modal formunu doldur
        $('#addOfferModal form').attr('action', `/offer/update/${id}`);
        $('#addOfferModal form').attr('method', 'POST');

        // PUT methodunu ekle
        if (!$("input[name='_method']").length) {
            $('#addOfferModal form').append('<input type="hidden" name="_method" value="PUT">');
        }

        $('#title').val(title);
        $('#demand_no').val(demandNo);
        $('#delivery_date').val(deliveryDate);
        $('#company').val(company).trigger('change');
        $('#person_name').val(personName).trigger('change');
        $('#person_email').val(personEmail);
        $('#currency').val(currency);
        $('#notes').val(notes);

        // Notlar alanını doldurun
        $('#notes').val(notes);

        // Tabloyu yeniden doldur
        $('#offerTableBody').empty();
        descriptionArray.forEach((description, index) => {
            $('#offerTableBody').append(`
                <tr>
                    <td>${index + 1}</td>
                    <td><input type="text" name="description[]" class="form-control" value="${description}" required></td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" value="${quantityArray[index] || ''}" required></td>
                    <td><input type="number" name="unit_price[]" class="form-control unit-price" value="${unitPriceArray[index] || ''}" required></td>
                    <td><input type="number" name="total_price[]" class="form-control total-price" value="${totalPriceArray[index] || ''}" readonly></td>
                </tr>
            `);
        });

        $('#addOfferModalLabel').text('Teklif Düzenle');
        $('#addOfferModal').modal('show');
    });

    // Oluştur butonuna tıklama işlemi
    $(document).on('click', '.create-offer-button', function () {
        const offerId = $(this).data('id'); // Teklif ID'sini al

        // AJAX isteği gönder
        $.ajax({
            url: `/offer/set-editable/${offerId}`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // CSRF koruması için
                is_editable: false // Düzenlenemez olarak işaretle
            },
            success: function () {
                // Düzenle butonunu gizle
                $(`button.edit-offer-button[data-id="${offerId}"]`).hide();
                 // "Gönder" butonunu göster
                $(`button.send-offer-button[data-id="${offerId}"]`).show();
                // "Oluştur" butonunu gizle
                $(`button.create-offer-button[data-id="${offerId}"]`).hide();
            },
            error: function (error) {
                console.error('Güncelleme sırasında bir hata oluştu:', error);
            }
        });
    });

    // Gönder butonuna tıklama
$(document).on('click', '.send-offer-button', function () {
    const offerId = $(this).data('id');

    if (!confirm('Bu teklifi göndermek istediğinizden emin misiniz?')) {
        return;
    }

    $.ajax({
        url: `/offer/send/${offerId}`,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            alert(response.message || 'Teklif başarıyla gönderildi.');
        },
        error: function (xhr) {
            alert(xhr.responseJSON?.message || 'Teklif gönderimi sırasında bir hata oluştu.');
        }
    });
});

    $('.create-offer-button').each(function () {
        const isEditable = $(this).data('is_editable');

        if (isEditable == 0) {
            $(this).hide(); // is_editable = 0 ise butonu gizle
        }
    });

    $('.send-offer-button').each(function () {
        const isEditable = $(this).closest('tr').find('.edit-offer-button').data('is_editable');

        if (isEditable == 1) {
            $(this).hide(); // is_editable = 1 ise butonu gizle
        }
    });

    });

</script>
@endsection
