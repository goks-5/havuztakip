@include('dashboard.tools.toolSettings',['tool'=>$tool])

@php
    // Şirket personellerini çekiyoruz (Bakımcı listesi)
    $staffs = App\Staff::where('company_id', Auth::user()->company_id)->get();

    // Tool settings'ini JSON olarak alıp diziye çeviriyoruz
    $settings = json_decode($tool->settings, true);
    // Seçilen durumlar (status) ve limit değerini alıyoruz
    $statuses = $settings['status'] ?? [];
    $limit = $settings['limit'] ?? 10;

    // Fault modelinde 'status' sütununa göre filtreleme yapıp, son eklenen $limit adet kaydı çekiyoruz.
    $faults = App\Fault::whereIn('status', $statuses)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
@endphp

{{-- Tabloyu sarmalayan wrapper, her 10 saniyede bir yenilenecek --}}
<div id="faults-table-tool-{{ $tool->id }}" class="table-responsive">
    <table id="faults_table_tool_{{ $tool->id }}" class="table table-hover">
        <thead>
            <tr>
                <th>Durum</th>
                <th>Ekipman</th>
                <th>Arıza Kodu</th>
                <th>Açıklama</th>
                <th>Oluşturma</th>
                <th>Bildiren Personel</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($faults as $fault)
                @php
                    // Varsayılan arka plan ve metin rengi
                    $rowBgColor = '#FFFFFF';
                    $rowTextColor = '#333';

                    // Statüye göre arka plan rengi ayarlaması
                    switch ($fault->status) {
                        case 'Yeni':
                            $rowBgColor = '#ffcccc';
                            break;
                        case 'Bekliyor |0|':
                            $rowBgColor = '#ffe5cc';
                            break;
                        case 'Bakıma Başlandı |0|':
                            $rowBgColor = '#ffffcc';
                            break;
                        case 'Firma Yönlendirildi |2|':
                            $rowBgColor = '#ccf2ff';
                            break;
                        case 'Malzeme Bekliyor |2|':
                            $rowBgColor = '#99e6ff';
                            break;
                        case 'Onay |1|':
                            $rowBgColor = '#ccffcc';
                            break;
                        case 'Bitti |1|':
                            $rowBgColor = '#99ff99';
                            break;
                    }
                @endphp
                <tr style="background-color: {{ $rowBgColor }}; color: {{ $rowTextColor }};">
                    <td>{{ $fault->status }}</td>
                    <td>{{ optional($fault->equipment)->name ?? 'Belirtilmemiş' }}</td>
                    <td>{{ $fault->fault_code }}</td>
                    <td>{{ $fault->fault_comment }}</td>
                    <td>{{ $fault->created_at }}</td>
                    <td>{{ $fault->reporting_user }}</td>
                    <td>
                        @if($fault->status === 'Yeni')
                            <!-- Arızayı Kabul Et Butonu -->
                            <button type="button"
                                    class="btn btn-sm btn-dark"
                                    data-toggle="modal"
                                    data-target="#acceptModal-{{ $fault->id }}"
                                    title="Arızayı Kabul Et"
                                    style="width: 90px;">
                                <i class="voyager-paper-plane"></i>
                            </button>

                            <!-- Arızayı Kabul Et Modalı -->
                            <div class="modal fade"
                                 id="acceptModal-{{ $fault->id }}"
                                 tabindex="-1"
                                 role="dialog"
                                 aria-labelledby="acceptModalLabel-{{ $fault->id }}"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('yeni-gelen-is-emirleri.accept', $fault->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header bg-primary text-white">
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="acceptModalLabel-{{ $fault->id }}">Arızayı Kabul Et</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="staff_id-{{ $fault->id }}">Bakımcı</label>
                                                    <select name="staff_id" id="staff_id-{{ $fault->id }}" class="form-control">
                                                        <option value="">Seçiniz</option>
                                                        @foreach($staffs as $staff)
                                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Kapat</button>
                                                <button type="submit" class="btn btn-sm btn-primary">Kabul Et</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- İşlem Gir Butonu -->
                            <button type="button"
                                    class="btn btn-sm btn-dark"
                                    data-toggle="modal"
                                    data-target="#processModal-{{ $fault->id }}"
                                    title="İşlem Gir"
                                    style="width: 90px;">
                                <i class="voyager-fire"></i>
                            </button>

                            <!-- İşlem Gir Modalı -->
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
                                            <div class="modal-header bg-primary text-white">
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title text-center" id="processModalLabel-{{ $fault->id }}">İşlem Gir</h4>
                                            </div>
                                            <div class="modal-body">
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
                                                <div class="form-group">
                                                    <label for="comment-{{ $fault->id }}">Açıklama</label>
                                                    <input type="text" name="comment" id="comment-{{ $fault->id }}" class="form-control" placeholder="Açıklama girin...">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Kapat</button>
                                                <button type="submit" class="btn btn-sm btn-primary">Kaydet</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@section('javascript')
    <script>
        $(function(){
            setInterval(function(){
                $('#faults-table-tool-{{ $tool->id }}').load(
                    window.location.href + ' #faults-table-tool-{{ $tool->id }} > *'
                );
            }, 10000);
        });
    </script>
@endsection