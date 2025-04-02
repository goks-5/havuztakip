@extends('voyager::master')

@section('content')
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">

            <!-- Başlık + Arama + Kapat (tek satır, sağa yaslı) -->
            <div class="row" style="margin-top: 30px; margin-bottom: 5px;">
                <!-- Sol tarafta başlık -->
                <div class="col-md-4 d-flex align-items-center">
                    <h3 style="color: #444; font-weight: 600; margin: 0;">
                        Onay Bekleyen İş Emirleri
                    </h3>
                </div>
                
                <!-- Sağ tarafta: arama + kapat butonu, inline-block -->
                <div class="col-md-8 text-right">
                    <!-- Arama Formu -->
                    <form action="{{ route('onay-bekleyenler.browse') }}"
                          method="GET"
                          style="display: inline-block; vertical-align: middle; margin-right: 10px;">
                        <input type="hidden" name="status" value="Onay |1|">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Ara..."
                               onkeyup="this.form.submit()"
                               style="display: inline-block; width: auto; vertical-align: middle;">
                    </form>

                    <!-- Arıza Kapat Formu -->
                    <form method="POST"
                          action="{{ route('arizalar.kapat') }}"
                          id="closeForm"
                          style="display: inline-block; vertical-align: middle;">
                        @csrf
                        <input type="hidden" name="action" value="close_selected">
                        <input type="hidden" name="selected_ids" id="selected_ids_input">
                        <button type="submit"
                                class="btn btn-danger"
                                style="vertical-align: middle;"
                                onclick="return handleCloseClick()">
                            Arızayı Kapat
                        </button>
                    </form>
                </div>
            </div>
            <!-- /Başlık + Arama + Kapat Satırı -->

            <!-- Tablo -->
            <div class="table-responsive mb-3">
                <form method="POST" action="{{ route('faultsActions') }}">
                    @csrf
                    <input type="hidden" name="action" value="batch">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
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
                                <!-- Mevcut sütunlar bitince, son sütun: İşlemler -->
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faults as $fault)
                                @php
                                    // Tüm satırlara açık yeşil arka plan veriyoruz:
                                    $rowBgColor = '#ccffcc';
                                    $rowTextColor = '#333';
                                @endphp
                                <tr style="background-color: {{ $rowBgColor }}; color: {{ $rowTextColor }};">
                                    <td>
                                        <input type="checkbox" name="selected_faults[]" value="{{ $fault->id }}">
                                    </td>
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
                                    
                                    <!-- İşlemler Sütunu: Göster, Düzenle, Sil Butonları -->
                                    <td>
                                        <!-- Göster Butonu -->
                                        <a href="{{ route('yeni-gelen-is-emirleri.show', $fault->id) }}"
                                           class="btn btn-sm btn-info"
                                           title="Göster">
                                            <i class="voyager-eye"></i>
                                        </a>
                                        
                                        <!-- Düzenle Butonu -->
                                        <a href="{{ route('yeni-gelen-is-emirleri.edit', $fault->id) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Düzenle">
                                            <i class="voyager-edit"></i>
                                        </a>
                                        
                                        <!-- Sil Butonu -->
                                        <form action="{{ route('yeni-gelen-is-emirleri.destroy', $fault->id) }}"
                                              method="POST"
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Kaydı silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                <i class="voyager-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
            <!-- /Tablo -->

            <!-- Sayfalama -->
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-right">
                        {{ $faults->appends([
                            'status' => 'Onay |1|',
                            'search' => request('search')
                        ])->links() }}
                    </div>
                </div>
            </div>
            <!-- /Sayfalama -->

        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    document.getElementById('select-all').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('input[name="selected_faults[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    function handleCloseClick() {
        const selected = [];
        document.querySelectorAll('input[name="selected_faults[]"]:checked').forEach(cb => {
            selected.push(cb.value);
        });

        if (selected.length === 0) {
            alert('Lütfen en az bir iş emri seçin.');
            return false;
        }

        document.getElementById('selected_ids_input').value = selected.join(',');
        return true;
    }
</script>
@endsection
