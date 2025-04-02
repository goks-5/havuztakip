@extends('voyager::master')

@section('content')
<div class="container" style="margin-top: 30px;">
    <div class="row">
        <div class="col-md-12">
            <h1 style="font-weight: bold; font-size: 28px; margin-bottom: 30px;">Arıza Ekle</h1>

            <form action="{{ route('fault.store') }}" method="POST">
                @csrf

                <!-- Ekipman Dropdown -->
                <div class="form-group">
                    <label for="equipmentSelect" style="font-weight: bold;">Ekipman</label>
                    <select id="equipmentSelect" name="equipment_id" class="form-control" required>
                        <option value="">Seçiniz</option>
                        @foreach(\App\Equipment::all() as $equipment)
                            <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Arıza Tipi Dropdown -->
                <div class="form-group">
                    <label for="faultTypeSelect" style="font-weight: bold;">Arıza Tipi</label>
                    <select id="faultTypeSelect" name="fault_type" class="form-control" required>
                        <option value="">Seçiniz</option>
                        <option value="Arıza">Arıza</option>
                        <option value="Bakım">Bakım</option>
                        <option value="Planlı duruş">Planlı Duruş</option>
                        <option value="Montaj">Montaj</option>
                        <option value="ISG">ISG</option>
                        <option value="Diğer">Diğer</option>
                    </select>
                </div>

                <!-- Arıza Kodu Dropdown -->
                <div class="form-group">
                    <label for="faultCodeSelect" style="font-weight: bold;">Arıza Kodu</label>
                    <select id="faultCodeSelect" name="fault_code" class="form-control" required>
                        <option value="">Seçiniz</option>
                        <option value="100 Mekanik">100 Mekanik</option>
                        <option value="200 Elektrik">200 Elektrik</option>
                        <option value="300 Elektrik">300 Bakım</option>
                        <option value="400 Tesisat">400 Tesisat</option>
                        <option value="500 Kaynak">500 Kaynak</option>
                        <option value="600 İnşaat">600 İnşaat</option>
                        <option value="700 Genel">700 Genel</option>
                        <option value="800 Montaj">800 Montaj</option>
                    </select>
                </div>

                <!-- Arıza Açıklaması -->
                <div class="form-group">
                    <label for="faultComment" style="font-weight: bold;">Arıza Açıklaması</label>
                    <input type="text" id="faultComment" name="fault_comment" class="form-control" placeholder="Arıza açıklaması girin..." required>
                </div>

                <!-- Bildiren Personel -->
                <div class="form-group">
                    <label for="reportingUser" style="font-weight: bold;">Bildiren Personel</label>
                    <input type="text" id="reportingUser" name="reporting_user" class="form-control" value="{{ Auth::user()->name }}" readonly>
                 </div>

                <!-- Kaydet Butonu -->
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
