@extends('voyager::master')

@section('content')
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            
            <!-- Başlık + Arama + Kapat (aynı satırda) -->
            <div class="row" style="margin-top: 30px; margin-bottom: 5px;">
                <div class="col-md-4 d-flex align-items-center">
                    <h3 style="color: #444; font-weight: 600; margin: 0;">
                        Onay Bekleyen İş Emirleri
                    </h3>
                </div>
                <div class="col-md-8 text-right">
                    <div class="form-inline" style="justify-content: flex-end;">
                        <form action="{{ route('onay-bekleyenler.browse') }}" method="GET" class="d-inline-block">
                            <input type="hidden" name="status" value="Onay |1|">
                            <input type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control"
                                placeholder="Ara..."
                                onkeyup="this.form.submit()"
                                style="margin-right: 8px;">
                        </form>
                        <form method="POST" action="{{ route('arizalar.kapat') }}" id="closeForm" class="d-inline-block">
                           @csrf
                            <input type="hidden" name="action" value="close_selected">
                            <input type="hidden" name="selected_ids" id="selected_ids_input">
                            <button type="submit" class="btn btn-danger" onclick="return handleCloseClick()">Arızayı Kapat</button>
                        </form>
                    </div>
                </div>
            </div>

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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faults as $fault)
                                <tr style="background-color: #ccffcc; color: #333;">
                                    <td><input type="checkbox" name="selected_faults[]" value="{{ $fault->id }}"></td>
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
                </form>
            </div>

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
