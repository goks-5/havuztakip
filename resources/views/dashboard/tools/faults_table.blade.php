@include('dashboard.tools.toolSettings',['tool'=>$tool])

@php
    // Şirket personellerini çekiyoruz (Bakımcı listesi)
    $staffs   = App\Staff::where('company_id', Auth::user()->company_id)->get();
    // Tool ayarları
    $settings = json_decode($tool->settings, true);
    $statuses = $settings['status'] ?? [];
    $limit    = $settings['limit']  ?? 10;
    // Son kayıtları alıyoruz
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
                            <button
                                type="button"
                                class="btn btn-sm btn-dark accept-btn"
                                data-fault-id="{{ $fault->id }}"
                                style="width:90px;">
                                <i class="voyager-paper-plane"></i>
                            </button>
                        @else
                            <button
                                type="button"
                                class="btn btn-sm btn-dark process-btn"
                                data-fault-id="{{ $fault->id }}"
                                style="width:90px;">
                                <i class="voyager-fire"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Global “Arızayı Kabul Et” Modal --}}
<div class="modal fade" id="globalAcceptModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="acceptForm" method="POST" action="">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Arızayı Kabul Et</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <label>Bakımcı</label>
          <select name="staff_id" class="form-control mt-2" id="globalStaffSelect">
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

{{-- Global “İşlem Gir” Modal --}}
<div class="modal fade" id="globalProcessModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="processForm" method="POST" action="">
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

@push('javascript')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const tbodyId = 'faults_body_{{ $tool->id }}';
    const selfUrl = window.location.href;
    const baseUrl = "{{ url('yeni-gelen-is-emirleri') }}";

    function refreshFaults() {
        fetch(selfUrl, { method: 'GET' })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newBody = doc.getElementById(tbodyId);
                if (newBody) {
                    document.getElementById(tbodyId).innerHTML = newBody.innerHTML;
                }
            })
            .catch(console.error);
    }

    // Buton tıklanınca modal aç
    $(document).on('click', '.accept-btn', function(){
        const id = $(this).data('fault-id');
        $('#acceptForm').attr('action', `${baseUrl}/${id}/accept`);
        $('#globalAcceptModal').modal('show');
    });
    $(document).on('click', '.process-btn', function(){
        const id = $(this).data('fault-id');
        $('#processForm').attr('action', `${baseUrl}/${id}/process`);
        $('#globalProcessModal').modal('show');
    });

    // İlk yükleme ve 10 saniyede bir yenile
    refreshFaults();
    setInterval(refreshFaults, 10000);
});
</script>
@endpush
