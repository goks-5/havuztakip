@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                
                <!-- Başlık + Filtre + Arama Aynı Satırda -->
                <div class="row" style="margin-top: 30px; margin-bottom: 5px;">
                    <!-- Sol tarafta başlık -->
                    <div class="col-md-4 d-flex align-items-center">
                        <h3 style="color: #444; font-weight: 600; margin: 0;">
                            Tamamlanan İş Emirleri
                        </h3>
                    </div>
                    
                    <!-- Sağ tarafta filtre, tarih aralığı ve arama kutusu -->
                    <div class="col-md-8 text-right">
                        <form action="{{ route('tamamlananlar.browse') }}" method="GET" class="form-inline" style="justify-content: flex-end;">
                            <!-- Tarih Aralığı Tek Input (flatpickr ile) -->
                            <input type="text"
                                   id="date_range"
                                   name="date_range"
                                   value="{{ request('date_range') }}"
                                   class="form-control"
                                   placeholder="Tarih Aralığı Seçin"
                                   style="margin-right: 8px; width: 250px;"
                                   readonly
                            >
                            
                            <!-- Arama Kutusu -->
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Ara..."
                                   onkeyup="this.form.submit()"
                                   style="margin-right: 8px;"
                            >
                        </form>
                    </div>
                </div>
                <!-- /Başlık + Filtre + Arama Satırı -->

                <!-- Flatpickr için CSS ve JS -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <script>
                    flatpickr("#date_range", {
                        mode: "range",
                        enableTime: true,
                        dateFormat: "Y-m-d H:i",
                        time_24hr: true,
                        onClose: function(selectedDates, dateStr, instance) {
                            instance.input.form.submit(); // Tarih seçilince form otomatik submit olur
                        }
                    });
                </script>

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
                                    // "Bitti |1|" statüsü için pastel yeşil tonları
                                    $rowBgColor = '#99ff99';
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
                                'date_range' => request('date_range')
                            ])->links() }}
                        </div>
                    </div>
                </div>
                <!-- /Sayfalama -->

            </div>
        </div>
    </div>
@endsection
