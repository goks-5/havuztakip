@extends('voyager::master')

@section('content')
<div class="container-fluid">
    <h1 style="color: black;">Projeler</h1>
    <div class="card" style="margin-top: 20px;">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <!-- Küçük yuvarlak durum göstergesi sütunu -->
                        <th>#</th>
                        <th>ID</th>
                        <th>Firma</th>
                        <th>Teklif No</th>
                        <th>Talep No</th>
                        <th>Başlık</th>
                        <th>Teslimat Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offers as $offer)
                        @php
                            // is_editable değerine göre renk belirleme
                            $circleColor = 'gray';
                            switch ($offer->is_editable) {
                                case 1:
                                    $circleColor = 'yellow'; // Teklifin onaylanması bekleniyor
                                    break;
                                case 0:
                                    $circleColor = 'orange'; // Teklif oluşturuldu
                                    break;
                                case 2:
                                    $circleColor = 'green'; // Proje oluşturuldu
                                    break;
                                case 3:
                                    $circleColor = 'red'; // Teklif iptal edildi
                                    break;
                                case 4:
                                    $circleColor = 'red'; // Proje iptal edildi
                                    break;
                                default:
                                    $circleColor = 'gray';
                            }
                        @endphp
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
                                <!-- Projeye Git Butonu (sadece is_editable == 2) -->
                                <a href="{{ route('project.view', ['offerId' => $offer->id]) }}"
                                   class="btn btn-primary btn-sm"
                                   title="Projeye Git"
                                   style="display: {{ $offer->is_editable == 2 ? 'inline-block' : 'none' }};">
                                    <i class="voyager-forward"></i> 
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
