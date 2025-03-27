@extends('voyager::master')

@section('content')
<div class="container" style="margin-top: 30px;">
    <div class="row">
        <div class="col-md-12">
            <h1 style="font-weight: bold; font-size: 28px; margin-bottom: 30px;">Arıza Ekle</h1>

            <div class="form-group">
                <label for="equipmentSelect" style="font-weight: bold;">Ekipman</label>
                <select id="equipmentSelect" name="equipment_id" class="form-control" required>
                    <option value="">Seçiniz</option>
                    @foreach(\App\Equipment::all() as $equipment)
                        <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
@endsection
