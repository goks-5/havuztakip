@extends('voyager::master')

@section('content')

<style>
    /* Genel tablo ferahlığı */
    table.table th {
        padding: 16px 14px;
        font-weight: 600;
    }

    table.table td {
        padding: 16px 14px;
        vertical-align: middle;
        line-height: 1.6;
    }

    /* Card içi boşluk */
    .card-body {
        padding: 30px 30px;
    }

    /* Başlık ile içerik arası */
    .page-title {
        margin-bottom: 25px;
    }

    /* Search input */
    .user-search-input {
        height: 38px;
        padding: 8px 12px;
    }
</style>

<div class="container-fluid">

    <!-- Sayfa Başlığı + Sağ Aksiyonlar -->
    <div class="clearfix mt-5 mb-4">
        <h1 class="page-title pull-left">
            <i class="voyager-person"></i> Kullanıcılar
        </h1>

        <!-- Sağ taraf (Search + +) -->
        <div class="pull-right" style="display:flex; align-items:center; gap:14px; margin-top:15px;">
            <form method="GET"
                  action="{{ route('kullanici-tablosu.index') }}"
                  style="margin:0;">
                <input
                    type="text"
                    name="search"
                    class="form-control user-search-input"
                    placeholder="Ara..."
                    value="{{ request('search') }}"
                    style="width:240px;"
                >
            </form>

            <a href="#" class="btn btn-success" id="addUserButton" style="height:38px;">
                <i class="voyager-plus"></i>
            </a>
        </div>
    </div>

    <!-- Kullanıcılar Tablosu -->
    <div class="card mt-4 mb-4">
        <div class="card-body">
            <table class="table table-bordered table-striped">
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
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td>{{ $user->user_name }}</td>
                            <td>{{ $user->company_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->telephone }}</td>
                            <td>
                                <a href="#"
                                   class="btn btn-primary btn-sm editUserButton"
                                   data-id="{{ $user->id }}"
                                   data-user_name="{{ $user->user_name }}"
                                   data-company_name="{{ $user->company_name }}"
                                   data-email="{{ $user->email }}"
                                   data-telephone="{{ $user->telephone }}"
                                   style="margin-right:6px;">
                                    <i class="voyager-edit"></i> Düzenle
                                </a>

                                <form action="{{ route('kullanici-tablosu.destroy', $user->id) }}"
                                      method="POST"
                                      style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');">
                                        <i class="voyager-trash"></i> Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding:30px;">
                                Kayıt bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="text-right mt-5 pt-3">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Yeni Kullanıcı Modalı -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('kullanici-tablosu.store') }}">
                @csrf
                <div class="modal-body" style="padding:30px;">
                    <div class="form-group">
                        <label>Ad</label>
                        <input type="text" class="form-control" name="user_name" required>
                    </div>

                    <div class="form-group">
                        <label>Şirket</label>
                        <select class="form-control" name="company_name" required>
                            <option disabled selected>Şirket Seçin</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_name }}">
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Telefon</label>
                        <input type="text" class="form-control" name="telephone" required>
                    </div>
                </div>

                <div class="modal-footer" style="padding:20px 30px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Düzenle Modalı -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editUserForm">
                @csrf
                @method('PUT')

                <div class="modal-body" style="padding:30px;">
                    <div class="form-group">
                        <label>Ad</label>
                        <input type="text" class="form-control" id="edit_user_name" name="user_name" required>
                    </div>

                    <div class="form-group">
                        <label>Şirket</label>
                        <select class="form-control" id="edit_company_name" name="company_name" required>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_name }}">
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Telefon</label>
                        <input type="text" class="form-control" id="edit_telephone" name="telephone" required>
                    </div>
                </div>

                <div class="modal-footer" style="padding:20px 30px;">
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

    $('#addUserButton').on('click', function (e) {
        e.preventDefault();
        $('#addUserModal').modal('show');
    });

    $('.editUserButton').on('click', function (e) {
        e.preventDefault();

        $('#editUserForm').attr('action', '/kullanici-tablosu/update/' + $(this).data('id'));
        $('#edit_user_name').val($(this).data('user_name'));
        $('#edit_company_name').val($(this).data('company_name'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_telephone').val($(this).data('telephone'));

        $('#editUserModal').modal('show');
    });
});
</script>

@if (session('success'))
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "timeOut": "3000"
        };

        toastr.success("{{ session('success') }}");
    </script>
@endif

@endsection
