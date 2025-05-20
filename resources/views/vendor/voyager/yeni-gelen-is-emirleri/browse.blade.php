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
                <div id="faults-table" class="table-responsive mb-3">
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
                                <!-- Yeni sütun: Eylemler -->
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faults as $fault)
                                @php
                                    // Örnek arka plan ve metin rengi
                                    $rowBgColor = '#ffcccc';
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

                                    <!-- İşlemler sütunu: Göster, Düzenle, Sil, Arızayı Kabul Et -->
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

                                        <!-- Arızayı Kabul Et Butonu (Uçak ikonu) -->
                                        <button type="button"
                                                class="btn btn-sm btn-dark"
                                                data-toggle="modal"
                                                data-target="#acceptModal-{{ $fault->id }}"
                                                title="Arızayı Kabul Et">
                                            <i class="voyager-paper-plane"></i>
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade"
                                             id="acceptModal-{{ $fault->id }}"
                                             tabindex="-1"
                                             role="dialog"
                                             aria-labelledby="acceptModalLabel-{{ $fault->id }}"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('yeni-gelen-is-emirleri.accept', $fault->id) }}" method="POST">
                                                       @csrf
                                                        <!-- Modal Header -->
                                                        <div class="modal-header bg-primary text-white" style="position: relative; padding-top: 1rem; padding-bottom: 1rem;">
                                                            <!-- Çarpı butonu sağ üst köşede -->
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat" style="outline: none; position: absolute; top: 0.5rem; right: 1rem;">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            <!-- Başlık, ekstra margin-top ile aşağı çekildi -->
                                                            <h4 class="modal-title text-center" id="acceptModalLabel-{{ $fault->id }}" style="font-size: 1.2rem; margin-top: 2.5rem; width: 100%;">
                                                                Arızayı Kabul Et
                                                            </h4>
                                                        </div>

                                                        <!-- Modal Body -->
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="staff_id-{{ $fault->id }}">Bakımcı</label>
                                                                <select name="staff_id"
                                                                        id="staff_id-{{ $fault->id }}"
                                                                        class="form-control">
                                                                    <option value="">Seçiniz</option>
                                                                    @foreach($staffs as $staff)
                                                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Modal Footer -->
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                                                            <!-- Kabul Et butonunu btn-primary yaparak header ile aynı renge getiriyoruz -->
                                                            <button type="submit" class="btn btn-primary">Kabul Et</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Modal -->
                                    </td>
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

@section('javascript')
    <script>
        $(function(){
            setInterval(function(){
                $('#faults-table').load(
                    window.location.href + ' #faults-table > *'
                );
            }, 10000); // Her 10 saniyede bir güncelle
        });
    </script>
@endsection
