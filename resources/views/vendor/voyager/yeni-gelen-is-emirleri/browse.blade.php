@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <!-- Başlık + Arama Kutusu Aynı Satırda -->
                <div class="row" style="margin-top: 30px; margin-bottom: 5px;">
                    <!-- Sol tarafta başlık -->
                    <div class="col-md-4 d-flex align-items-center">
                        <h3 style="color: #444; font-weight: 600; margin: 0;">
                            Yeni Gelen İş Emirleri
                        </h3>
                    </div>
                    
                    <!-- Sağ tarafta arama kutusu -->
                    <div class="col-md-8 text-right">
                        <form action="{{ route('yeni-gelen-is-emirleri.browse') }}" method="GET" class="form-inline" style="justify-content: flex-end;">
                            <!-- Gizli input ile statü sabit "Yeni" gönderiliyor -->
                            <input type="hidden" name="status" value="Yeni">
                            
                            <!-- Arama Kutusu (buton olmadan, otomatik submit) -->
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Ara..."
                                   onkeyup="this.form.submit()"
                                   style="margin-right: 8px;">
                        </form>
                    </div>
                </div>
                <!-- /Başlık + Arama Kutusu Satırı -->

                <!-- Tabloyu sarmalayan container -->
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Durum</th>
                                <th>Ekipman</th>
                                <th>Arıza Tipi</th>
                                <th>Arıza Kodu</th>
                                <th>Arıza Açıklaması</th>
                                <th>Bildiren Personel</th>
                                <th>Oluşturma</th>
                                <th>Arıza Tamamlanma Zamanı</th>
                                <th>Bakımcı</th>
                                <th>Bakımcı Notu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faults as $fault)
                                @php
                                    // "Yeni" statüsü için örnek arka plan ve metin rengi ayarlıyoruz
                                    $rowBgColor = '#ffcccc'; // Açık kırmızı ton
                                    $rowTextColor = '#333';
                                @endphp
                                <tr style="background-color: {{ $rowBgColor }}; color: {{ $rowTextColor }};">
                                    <td>{{ $fault->status }}</td>
                                    <td>{{ optional($fault->equipment)->name }}</td>
                                    <td>{{ $fault->fault_type }}</td>
                                    <td>{{ $fault->fault_code }}</td>
                                    <td>{{ $fault->fault_comment }}</td>
                                    <td>{{ $fault->reporting_user }}</td>
                                    <td>{{ $fault->created_at }}</td>
                                    <td>{{ $fault->finish_at }}</td>
                                    <td>{{ optional($fault->staff)->name }}</td>
                                    <td>{{ $fault->maintainer_note }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /Tablo -->

                <!-- Sayfalama -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
                            {{ $faults->appends([
                                'search' => request('search'),
                                'status' => 'Yeni'
                            ])->links() }}
                        </div>
                    </div>
                </div>
                <!-- /Sayfalama -->

            </div>
        </div>
    </div>
@endsection
