@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <!-- Başlık + Arama Kutusu -->
                <div class="row" style="margin-top:30px; margin-bottom:5px;">
                    <div class="col-md-4 d-flex align-items-center">
                        <h3 style="color:#444; font-weight:600; margin:0;">Yeni Gelen İş Emirleri</h3>
                    </div>
                    <div class="col-md-8 text-right">
                        <form action="{{ route('yeni-gelen-is-emirleri.browse') }}" method="GET" class="form-inline justify-content-end">
                            <input type="hidden" name="status" value="Yeni">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Ara..."
                                   onkeyup="this.form.submit()"
                                   style="margin-right:8px;">
                        </form>
                    </div>
                </div>

                <!-- Tablo -->
                <div id="faults-table" class="table-responsive mb-3">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Durum</th>
                                <th>Ekipman</th>
                                <th>Arıza Tipi</th>
                                <th>Arıza Kodu</th>
                                <th>Açıklama</th>
                                <th>Bildiren</th>
                                <th>Oluşturma</th>
                                <th>Bitirme</th>
                                <th>Bakımcı</th>
                                <th>Not</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faults as $fault)
                                @php
                                    $rowBg = '#ffcccc';
                                @endphp
                                <tr style="background-color: {{ $rowBg }}; color: #333;">
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
                                    <td>
                                        <!-- Göster, Düzenle, Sil -->
                                        <a href="{{ route('yeni-gelen-is-emirleri.show', $fault->id) }}" class="btn btn-sm btn-info" title="Göster"><i class="voyager-eye"></i></a>
                                        <a href="{{ route('yeni-gelen-is-emirleri.edit', $fault->id) }}" class="btn btn-sm btn-warning" title="Düzenle"><i class="voyager-edit"></i></a>
                                        <form action="{{ route('yeni-gelen-is-emirleri.destroy', $fault->id) }}"
                                              method="POST"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Sil"><i class="voyager-trash"></i></button>
                                        </form>
                                        <!-- Arızayı Kabul Et -->
                                        <button type="button"
                                                class="btn btn-sm btn-dark accept-btn"
                                                data-fault-id="{{ $fault->id }}"
                                                title="Arızayı Kabul Et">
                                            <i class="voyager-paper-plane"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Sayfalama -->
                    <div class="pull-right">
                        {{ $faults->appends([
                            'search'=>request('search'),
                            'status'=>'Yeni'
                        ])->links() }}
                    </div>
                </div>

                <!-- Global “Arızayı Kabul Et” Modal (table dışı, sabit) -->
                <div class="modal fade" id="globalAcceptModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <form id="acceptForm" method="POST" action="">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title">Arızayı Kabul Et</h5>
                          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                          <label for="globalStaffSelect">Bakımcı</label>
                          <select name="staff_id" id="globalStaffSelect" class="form-control mt-2">
                            <option value="">Seçiniz</option>
                            @foreach($staffs as $staff)
                              <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                          <button type="submit" class="btn btn-primary">Kabul Et</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
    $(function(){
        // tablo yenile
        setInterval(function(){
            $('#faults-table').load(window.location.href + ' #faults-table > *');
        }, 10000);

        // Arızayı Kabul Et Butonu
        const baseUrl = "{{ url('yeni-gelen-is-emirleri') }}";
        $(document).on('click', '.accept-btn', function(){
            const id = $(this).data('fault-id');
            $('#acceptForm').attr('action', baseUrl + '/' + id + '/accept');
            $('#globalAcceptModal').modal('show');
        });
    });
    </script>
@endsection
