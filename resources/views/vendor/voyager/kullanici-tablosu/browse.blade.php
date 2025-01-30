@extends('voyager::master')

@section('content')
<div class="container-fluid">
    <!-- Sayfa Başlığı ve Ekle Butonu -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
        <h1 class="page-title">
            <i class="voyager-person"></i> Kullanıcılar
        </h1>
        <a href="#" class="btn btn-success" id="addUserButton">
            <i class="voyager-plus"></i> 
        </a>
    </div>

    <!-- Kullanıcılar Tablosu -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ad</th>
                        <th>Şirket</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->user_name }}</td>
                            <td>{{ $user->company_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->telephone }}</td>
                            <td>
                                <!-- Düzenle Butonu -->
                                <a href="#" class="btn btn-primary btn-sm editUserButton" 
                                   data-id="{{ $user->id }}" 
                                   data-user_name="{{ $user->user_name }}" 
                                   data-company_name="{{ $user->company_name }}" 
                                   data-email="{{ $user->email }}" 
                                   data-telephone="{{ $user->telephone }}">
                                    <i class="voyager-edit"></i> Düzenle
                                </a>
                                
                                <!-- Sil Butonu -->
                                <form action="{{ route('kullanici-tablosu.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');">
                                        <i class="voyager-trash"></i> Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Henüz bir kullanıcı eklenmemiş.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Yeni Kullanıcı Modalı -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <form method="POST" action="{{ route('kullanici-tablosu.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="user_name">Ad</label>
                    <input type="text" class="form-control" id="user_name" name="user_name" required>
                </div>
                <div class="form-group">
                    <label for="company_name">Şirket</label>
                    <select class="form-control" id="company_name" name="company_name" required>
                        <option value="" disabled selected>Şirket Seçin</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_name }}">{{ $company->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
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
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Kullanıcıyı Düzenle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_user_name">Ad</label>
                        <input type="text" class="form-control" id="edit_user_name" name="user_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_company_name">Şirket</label>
                        <select class="form-control" id="edit_company_name" name="company_name" required>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_name }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
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
        $('#addUserButton').on('click', function (e) {
            e.preventDefault();
            $('#addUserModal').modal('show');
        });

        // Form gönderildiğinde verileri göster
        $('form').on('submit', function (e) {
            console.log("Form gönderiliyor...");
        });

        // "Düzenle" butonuna tıklayınca modal açılır ve veriler doldurulur
        $('.editUserButton').on('click', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            const user_name = $(this).data('user_name');
            const company_name = $(this).data('company_name');
            const email = $(this).data('email');
            const telephone = $(this).data('telephone');

            $('#editUserForm').attr('action', `/kullanici-tablosu/update/${id}`);
            $('#edit_user_name').val(user_name);
            $('#edit_company_name').val(company_name);
            $('#edit_email').val(email);
            $('#edit_telephone').val(telephone);

            $('#editUserModal').modal('show');
        });
    });
</script>
@endsection
