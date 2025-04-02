@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid" style="color: #000;">
        <h1>Arıza Düzenleme</h1>
        
        <form action="{{ route('yeni-gelen-is-emirleri.update', $fault->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- ID (readonly) -->
            <div class="form-group">
                <label for="id">ID</label>
                <input type="text" name="id" id="id" class="form-control" value="{{ $fault->id }}" readonly>
            </div>
            
            <!-- Durum (status) - Dropdown -->
            <div class="form-group">
                <label for="status">Durum</label>
                <select name="status" id="status" class="form-control">
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @if($fault->status == $status) selected @endif>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Ekipman (equipment) - Dropdown -->
            <div class="form-group">
                <label for="equipment_id">Ekipman</label>
                <select name="equipment_id" id="equipment_id" class="form-control">
                    <option value="">Seçiniz</option>
                    @foreach($equipments as $equipment)
                        <option value="{{ $equipment->id }}" @if($equipment->id == $fault->equipment_id) selected @endif>
                            {{ $equipment->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Arıza Tipi (fault_type) - Dropdown -->
            <div class="form-group">
                <label for="fault_type">Arıza Tipi</label>
                <select name="fault_type" id="fault_type" class="form-control">
                    @foreach($fault_types as $type)
                        <option value="{{ $type }}" @if($fault->fault_type == $type) selected @endif>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Arıza Kodu (fault_code) - Dropdown -->
            <div class="form-group">
                <label for="fault_code">Arıza Kodu</label>
                <select name="fault_code" id="fault_code" class="form-control">
                    @foreach($fault_codes as $code => $description)
                        <option value="{{ $code }}" @if($fault->fault_code == $code) selected @endif>
                            {{ $code }} - {{ $description }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Arıza Açıklaması -->
            <div class="form-group">
                <label for="fault_comment">Arıza Açıklaması</label>
                <input type="text" name="fault_comment" id="fault_comment" class="form-control" value="{{ $fault->fault_comment }}">
            </div>
            
            <!-- Bildiren Personel -->
            <div class="form-group">
                <label for="reporting_user">Bildiren Personel</label>
                <input type="text" name="reporting_user" id="reporting_user" class="form-control" value="{{ $fault->reporting_user }}">
            </div>
            
            <!-- Oluşturma Tarihi (readonly) -->
            <div class="form-group">
                <label for="created_at">Oluşturma Tarihi</label>
                <input type="text" name="created_at" id="created_at" class="form-control" value="{{ $fault->created_at }}" readonly>
            </div>
            
            <!-- Arıza Tamamlanma Zamanı -->
            <div class="form-group">
                <label for="finish_at">Arıza Tamamlanma Zamanı</label>
                <input type="text" name="finish_at" id="finish_at" class="form-control" value="{{ $fault->finish_at }}">
            </div>
            
            <!-- Bakımcı -->
            <div class="form-group">
                <label for="staff">Bakımcı</label>
                <input type="text" name="staff" id="staff" class="form-control" value="{{ optional($fault->staff)->name }}">
            </div>
            
            <!-- Bakımcı Notu -->
            <div class="form-group">
                <label for="maintainer_note">Bakımcı Notu</label>
                <textarea name="maintainer_note" id="maintainer_note" class="form-control" rows="4">{{ $fault->maintainer_note }}</textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </form>
    </div>
@endsection
