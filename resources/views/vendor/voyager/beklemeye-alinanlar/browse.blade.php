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
                            Beklemeye Alınan İş Emirleri
                        </h3>
                    </div>
                    
                    <!-- Sağ tarafta arama kutusu -->
                    <div class="col-md-8 text-right">
                        <form action="{{ route('beklemeye-alinanlar.browse') }}" method="GET" class="form-inline" style="justify-content: flex-end;">
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
                                <!-- Yeni sütun: Eylemler -->
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faults as $fault)
                                @php
                                    // Duruma göre satır rengi ayarlaması:
                                    if ($fault->status == 'Firma Yönlendirildi |2|') {
                                        $rowBgColor = '#ccf2ff';
                                    } elseif ($fault->status == 'Malzeme Bekliyor |2|') {
                                        $rowBgColor = '#99e6ff';
                                    } else {
                                        $rowBgColor = '#FFFFFF';
                                    }
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
                                    
                                    <!-- İşlemler sütunu -->
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
                                        
                                        <!-- İşlem Gir Butonu -->
                                        <button type="button"
                                                class="btn btn-sm btn-dark"
                                                data-toggle="modal"
                                                data-target="#processModal-{{ $fault->id }}"
                                                title="İşlem Gir">
                                            <i class="voyager-fire"></i>
                                        </button>
                                        
                                        <!-- Modal (İşlem Gir) -->
                                        <div class="modal fade"
                                             id="processModal-{{ $fault->id }}"
                                             tabindex="-1"
                                             role="dialog"
                                             aria-labelledby="processModalLabel-{{ $fault->id }}"
                                             aria-hidden="true">
                                          <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                              <form action="{{ route('yeni-gelen-is-emirleri.process', $fault->id) }}" method="POST">
                                                  @csrf
                                                  <!-- Modal Header -->
                                                  <div class="modal-header bg-primary text-white" style="position: relative; padding-top: 1rem; padding-bottom: 1rem;">
                                                      <!-- Kapat (×) butonu sağ üst köşede sabit -->
                                                      <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat" style="position: absolute; top: 0.5rem; right: 1rem; outline: none;">
                                                          <span aria-hidden="true">&times;</span>
                                                      </button>
                                                      <!-- Başlık aşağıda göstermek için margin-top ekliyoruz -->
                                                      <h4 class="modal-title w-100 text-center" id="processModalLabel-{{ $fault->id }}" style="font-size: 1.3rem; margin-top: 2rem;">
                                                          İşlem Gir
                                                      </h4>
                                                  </div>
                        
                                                  <!-- Modal Body -->
                                                  <div class="modal-body">
                                                      <!-- Durum Dropdown -->
                                                      <div class="form-group">
                                                          <label for="status-{{ $fault->id }}">Durum</label>
                                                          <select name="status" id="status-{{ $fault->id }}" class="form-control">
                                                              <option value="Bekliyor |0|">Bekliyor</option>
                                                              <option value="Bakıma Başlandı |0|">Bakıma Başlandı</option>
                                                              <option value="Firma Yönlendirildi |2|">Firmaya Yönlendirildi</option>
                                                              <option value="Malzeme Bekliyor |2|">Malzeme Bekliyor</option>
                                                              <option value="Onay |1|">Tamamlandı</option>
                                                          </select>
                                                      </div>
                                                      <!-- Açıklama Alanı -->
                                                      <div class="form-group">
                                                          <label for="comment-{{ $fault->id }}">Açıklama</label>
                                                          <textarea name="comment" id="comment-{{ $fault->id }}" class="form-control" rows="4" placeholder="Açıklama girin..."></textarea>
                                                      </div>
                                                  </div>
                        
                                                  <!-- Modal Footer -->
                                                  <div class="modal-footer">
                                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                                                      <button type="submit" class="btn btn-primary font-weight-bold">Kaydet</button>
                                                  </div>
                                              </form>
                                            </div>
                                          </div>
                                        </div>
                                        <!-- /Modal -->
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
                            {{ $faults->appends(['search' => request('search')])->links() }}
                        </div>
                    </div>
                </div>
                <!-- /Sayfalama -->
                
            </div>
        </div>
    </div>
@endsection
