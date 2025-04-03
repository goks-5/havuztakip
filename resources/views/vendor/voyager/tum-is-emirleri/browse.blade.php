@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <!-- Başlık + Filtre + Arama aynı satırda -->
                <div class="row" style="margin-top: 30px; margin-bottom: 5px;">
                    <!-- Sol tarafta başlık -->
                    <div class="col-md-4 d-flex align-items-center">
                        <h3 style="color: #444; font-weight: 600; margin: 0;">
                            Tüm İş Emirleri
                        </h3>
                    </div>
                    
                    <!-- Sağ tarafta dropdown, tarih aralığı ve search box -->
                    <div class="col-md-8 text-right">
                        <form action="{{ route('tum-is-emirleri.browse') }}" method="GET" class="form-inline" style="justify-content: flex-end;">
                            <!-- Durum Filtre Dropdown -->
                            <select name="status" onchange="this.form.submit()" style="vertical-align: middle; margin-right: 8px;" class="form-control">
                                @foreach($statusList as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

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
                            instance.input.form.submit(); // tarih seçilince form otomatik submit olur
                        }
                    });
                </script>

                <!-- Tablo -->
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
                                    // Arka plan rengi, statüye göre örnek ayarlamalar
                                    $rowBgColor = '#FFFFFF';
                                    $rowTextColor = '#333';

                                    switch ($fault->status) {
                                        case 'Yeni':
                                            $rowBgColor = '#ffcccc'; // Açık kırmızı
                                            break;
                                        case 'Bekliyor |0|':
                                            $rowBgColor = '#ffe5cc'; // Açık turuncu
                                            break;
                                        case 'Bakıma Başlandı |0|':
                                            $rowBgColor = '#ffffcc'; // Açık sarı
                                            break;
                                        case 'Firma Yönlendirildi |2|':
                                            $rowBgColor = '#ccf2ff'; // Açık mavi
                                            break;
                                        case 'Malzeme Bekliyor |2|':
                                            $rowBgColor = '#99e6ff'; // Bir tık koyu mavi
                                            break;
                                        case 'Onay |1|':
                                            $rowBgColor = '#ccffcc'; // Açık yeşil
                                            break;
                                        case 'Bitti |1|':
                                            $rowBgColor = '#99ff99'; // Bir tık koyu yeşil
                                            break;
                                    }
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
                                'status' => request('status'),
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
