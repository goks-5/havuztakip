@extends('voyager::master')

@section('content')
<div class="page-content container-fluid">
    <form id="filter-form" action="{{ route('tum-is-emirleri.browse') }}" method="GET">

        {{-- Başlık + Excel İndir --}}
        <div class="row align-items-center mb-3" style="margin-top:30px;">
            <div class="col-md-6">
                <h3 class="m-0" style="color:#444; font-weight:600">
                    Tüm İş Emirleri
                </h3>
            </div>
            <div class="col-md-6 text-right">
                <button type="submit"
                        name="export"
                        value="1"
                        class="btn btn-success btn-sm">
                    📥 
                </button>
            </div>
        </div>

        {{-- Tablo --}}
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Durum</th>
                        <th>Ekipman</th>
                        <th>Arıza Tipi</th>
                        <th>Arıza Kodu</th>
                        <th>Arıza Açıklaması</th>
                        <th>Bildiren Personel</th>
                        <th>Oluşturma</th>
                        <th>Tamamlanma</th>
                        <th>Bakımcı</th>
                        <th>Not</th>
                    </tr>
                    <tr>
                        <th><input type="text" name="durum" value="{{ request('durum') }}" class="form-control form-control-sm" placeholder="Ara" onkeyup="this.form.submit()"></th>
                        <th><input type="text" name="ekipman" value="{{ request('ekipman') }}" class="form-control form-control-sm" placeholder="Ara" onkeyup="this.form.submit()"></th>
                        <th><input type="text" name="fault_type" value="{{ request('fault_type') }}" class="form-control form-control-sm" placeholder="Ara" onkeyup="this.form.submit()"></th>
                        <th><input type="text" name="fault_code" value="{{ request('fault_code') }}" class="form-control form-control-sm" placeholder="Ara" onkeyup="this.form.submit()"></th>
                        <th><input type="text" name="fault_comment" value="{{ request('fault_comment') }}" class="form-control form-control-sm" placeholder="Ara" onkeyup="this.form.submit()"></th>
                        <th><input type="text" name="reporting_user" value="{{ request('reporting_user') }}" class="form-control form-control-sm" placeholder="Ara" onkeyup="this.form.submit()"></th>
                        <th><input type="text" name="created_at" value="{{ request('created_at') }}" class="form-control form-control-sm" placeholder="YYYY-MM-DD" onkeyup="this.form.submit()"></th>
                        <th><input type="text" name="finish_at" value="{{ request('finish_at') }}" class="form-control form-control-sm" placeholder="YYYY-MM-DD" onkeyup="this.form.submit()"></th>
                        <th><input type="text" name="staff" value="{{ request('staff') }}" class="form-control form-control-sm" placeholder="Ara" onkeyup="this.form.submit()"></th>
                        <th><input type="text" name="maintainer_note" value="{{ request('maintainer_note') }}" class="form-control form-control-sm" placeholder="Ara" onkeyup="this.form.submit()"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faults as $fault)
                        @php
                            $bg = '#fff';
                            switch($fault->status) {
                                case 'Yeni':                    $bg = '#ffcccc'; break;
                                case 'Bekliyor |0|':            $bg = '#ffe5cc'; break;
                                case 'Bakıma Başlandı |0|':     $bg = '#ffffcc'; break;
                                case 'Firma Yönlendirildi |2|': $bg = '#ccf2ff'; break;
                                case 'Malzeme Bekliyor |2|':    $bg = '#99e6ff'; break;
                                case 'Onay |1|':                $bg = '#ccffcc'; break;
                                case 'Bitti |1|':               $bg = '#99ff99'; break;
                            }
                        @endphp
                        <tr style="background-color:{{ $bg }}; color:#333;">
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

    </form>

    {{-- Sayfalama --}}
    <div class="row">
        <div class="col-md-12 text-right">
            {{ $faults->appends(request()->all())->links() }}
        </div>
    </div>
</div>
@endsection
