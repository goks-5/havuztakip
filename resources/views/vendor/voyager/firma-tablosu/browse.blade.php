@extends('voyager::master')

@section('content')
<div class="container-fluid">
    <!-- Sayfa Başlığı ve Ekle Butonu -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
        <h1 class="page-title">
            <i class="voyager-company"></i> Firmalar
        </h1>
        <a href="#" class="btn btn-success" id="addCompanyButton">
            <i class="voyager-plus"></i> 
        </a>
    </div>

    <!-- Firmalar Tablosu -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Firma Adı</th>
                        <th>Adres</th>
                        <th>Telefon</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($company as $firma)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $firma->company_name }}</td>
                            <td>{{ $firma->address }}</td>
                            <td>{{ $firma->telephone }}</td>
                            <td>
                                <!-- Düzenle Butonu -->
                                <a href="#" class="btn btn-primary btn-sm editCompanyButton" 
                                   data-id="{{ $firma->id }}" 
                                   data-name="{{ $firma->company_name }}" 
                                   data-address="{{ $firma->address }}" 
                                   data-telephone="{{ $firma->telephone }}">
                                    <i class="voyager-edit"></i> 
                                </a>
                                
                                <!-- Sil Butonu -->
                                <form action="{{ route('firma-tablosu.destroy', $firma->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Bu firmayı silmek istediğinizden emin misiniz?');">
                                        <i class="voyager-trash"></i> 
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Henüz bir firma eklenmemiş.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Yeni Firma Modalı -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" role="dialog" aria-labelledby="addCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('firma-tablosu.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addCompanyModalLabel">Yeni Firma Ekle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="company_name">Şirket Adı</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Adres</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Telefon</label>
                        <input type="text" class="form-control" id="telephone" name="telephone" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Düzenle Modalı -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" role="dialog" aria-labelledby="editCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="editCompanyForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editCompanyModalLabel">Firmayı Düzenle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_company_name">Şirket Adı</label>
                        <input type="text" class="form-control" id="edit_company_name" name="company_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_address">Adres</label>
                        <input type="text" class="form-control" id="edit_address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_telephone">Telefon</label>
                        <input type="text" class="form-control" id="edit_telephone" name="telephone" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        // "Ekle" butonuna tıklayınca modal açılır
        $('#addCompanyButton').on('click', function (e) {
            e.preventDefault();
            $('#addCompanyModal').modal('show');
        });

        // "Düzenle" butonuna tıklayınca modal açılır ve veriler doldurulur
        $('.editCompanyButton').on('click', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const address = $(this).data('address');
            const telephone = $(this).data('telephone');

            $('#editCompanyForm').attr('action', `/firma-tablosu/update/${id}`);
            $('#edit_company_name').val(name);
            $('#edit_address').val(address);
            $('#edit_telephone').val(telephone);

            $('#editCompanyModal').modal('show');
        });
    });
</script>
@endsection
