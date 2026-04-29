@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid" style="color: #000;">
        <div class="row">
            <div class="col-md-12">
                <!-- Başlık ve Butonlar -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-6">
                        <h1>Arıza Detayları</h1>
                    </div>
                    <div class="col-md-6 text-right" style="padding-top: 20px;">
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
                        
                        <!-- PDF Butonu -->
                        <a href="{{ route('yeni-gelen-is-emirleri.pdf', $fault->id) }}"
                        class="btn btn-sm btn-success"
                        title="PDF">
                            <i class="voyager-file-text"></i>
                        </a>

                        <!-- Listeye Dön Butonu -->
                        <a href="{{ route('yeni-gelen-is-emirleri.browse') }}"
                           class="btn btn-sm btn-primary"
                           title="Listeye Dön">
                            <i class="voyager-list"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Detayları Gösteren Tablo -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" style="color: #000;">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">ID</th>
                                <td>{{ $fault->id }}</td>
                            </tr>
                            <tr>
                                <th>Ekipman</th>
                                <td>{{ optional($fault->equipment)->name }}</td>
                            </tr>
                            <tr>
                                <th>Arıza Tipi</th>
                                <td>{{ $fault->fault_type }}</td>
                            </tr>
                            <tr>
                                <th>Arıza Kodu</th>
                                <td>{{ $fault->fault_code }}</td>
                            </tr>
                            <tr>
                                <th>Arıza Açıklaması</th>
                                <td>{{ $fault->fault_comment }}</td>
                            </tr>
                            <tr>
                                <th>Bildiren Personel</th>
                                <td>{{ $fault->reporting_user }}</td>
                            </tr>
                            <tr>
                                <th>Oluşturma Tarihi</th>
                                <td>{{ $fault->created_at }}</td>
                            </tr>
                            <tr>
                                <th>Arıza Tamamlanma Zamanı</th>
                                <td>{{ $fault->finish_at }}</td>
                            </tr>
                            <tr>
                                <th>Bakımcı</th>
                                <td>{{ optional($fault->staff)->name }}</td>
                            </tr>
                            <tr>
                                <th>Bakımcı Notu</th>
                                <td>{{ $fault->maintainer_note }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
