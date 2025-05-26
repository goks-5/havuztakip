@include('dashboard.tools.toolSettings',['tool'=>$tool])

@php
    // Şirket personellerini çekiyoruz (Bakımcı listesi)
    $staffs   = App\Staff::where('company_id', Auth::user()->company_id)->get();
    // Tool ayarları
    $settings = json_decode($tool->settings, true);
    $statuses = $settings['status'] ?? [];
    $limit    = $settings['limit']  ?? 10;
    // Kayıtları alıyoruz
    $faults   = App\Fault::whereIn('status', $statuses)
                  ->orderBy('created_at', 'desc')
                  ->limit($limit)
                  ->get();
@endphp

<div class="table-responsive">
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
        <tbody id="faults_body_{{ $tool->id }}">
            @foreach($faults as $fault)
                @php
                    $rowBg = '#FFFFFF';
                    switch($fault->status){
                        case 'Yeni':                $rowBg = '#ffcccc'; break;
                        case 'Bekliyor |0|':        $rowBg = '#ffe5cc'; break;
                        case 'Bakıma Başlandı |0|': $rowBg = '#ffffcc'; break;
                        case 'Firma Yönlendirildi |2|': $rowBg = '#ccf2ff'; break;
                        case 'Malzeme Bekliyor |2|':    $rowBg = '#99e6ff'; break;
                        case 'Onay |1|':            $rowBg = '#ccffcc'; break;
                        case 'Bitti |1|':           $rowBg = '#99ff99'; break;
                    }
                @endphp
                <tr style="background-color: {{ $rowBg }}; color: #333;">
                    <td>{{ $fault->status }}</td>
                    <td>{{ optional($fault->equipment)->name ?: 'Belirtilmemiş' }}</td>
                    <td>{{ $fault->fault_code }}</td>
                    <td>{{ $fault->fault_comment }}</td>
                    <td>{{ $fault->created_at }}</td>
                    <td>{{ $fault->reporting_user }}</td>
                    <td>
                        @if($fault->status === 'Yeni')
                            <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#acceptModal-{{ $fault->id }}" style="width:90px;">
                                <i class="voyager-paper-plane"></i>
                            </button>
                            <div class="modal fade" id="acceptModal-{{ $fault->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('yeni-gelen-is-emirleri.accept', $fault->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">Arızayı Kabul Et</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <label>Bakımcı</label>
                                                <select name="staff_id" class="form-control mt-2">
                                                    <option value="">Seçiniz</option>
                                                    @foreach($staffs as $staff)
                                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                                    @endforeach
                                                </select>
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
                            <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#processModal-{{ $fault->id }}" style="width:90px;">
                                <i class="voyager-fire"></i>
                            </button>
                            <div class="modal fade" id="processModal-{{ $fault->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('yeni-gelen-is-emirleri.process', $fault->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">İşlem Gir</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Durum</label>
                                                    <select name="status" class="form-control mt-2">
                                                        <option value="Bekliyor |0|">Bekliyor</option>
                                                        <option value="Bakıma Başlandı |0|">Bakıma Başlandı</option>
                                                        <option value="Firma Yönlendirildi |2|">Firmaya Yönlendirildi</option>
                                                        <option value="Malzeme Bekliyor |2|">Malzeme Bekliyor</option>
                                                        <option value="Onay |1|">Tamamlandı</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label>Açıklama</label>
                                                    <input type="text" name="comment" class="form-control mt-2" placeholder="Açıklama girin...">
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbodyId = 'faults_body_{{ $tool->id }}';
    const selfUrl = window.location.href;

    function refreshFaults() {
        fetch(selfUrl, { method: 'GET' })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newBody = doc.getElementById(tbodyId);
                if (newBody) {
                    document.getElementById(tbodyId).innerHTML = newBody.innerHTML;
                }
            })
            .catch(console.error);
    }

    // İlk yüklemede ve her 10 saniyede bir
    refreshFaults();
    setInterval(refreshFaults, 10000);
});
</script>
