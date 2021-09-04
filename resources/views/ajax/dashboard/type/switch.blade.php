<div class="form-group">
    <label for="image">Buton</label>
    @include('ajax.dashboard.type.element.main',['slug'=>'switch', 'ek' => 1,'filter' => ['00:00:00:00:00:02']])

    <div class="col-xs-4">
        <label class="control-label">Aktif Durumu</label>
        <input type="text" name="setting[on]" class="form-control" value="{{ $settings['on'] ?? 'Açık' }}" />
    </div>
    <div class="col-xs-4">
        <label class="control-label">Aktif Değeri</label>
        <input type="text" name="setting[onValue]" class="form-control" value="{{ $settings['onValue'] ?? '1' }}" />
    </div>
    <div class="col-xs-4">
        <label class="control-label">Aktif Rengi</label>
        @php
            $onstyle = isset($setting['onstyle']) ? $setting['onstyle'] : 'success';
        @endphp
        <select class="form-control select2" name="setting[onstyle]">
            <option value="primary" style="background-color: #007bff;color: #FFFFFF;"
                {{ $onstyle == 'primary' ? 'selected' : '' }}>Mavi</option>
            <option value="secondary" style="background-color: #6c757d;color: #FFFFFF;"
                {{ $onstyle == 'secondary' ? 'selected' : '' }}>Gri</option>
            <option value="success" style="background-color: #28a745;color: #FFFFFF;"
                {{ $onstyle == 'success' ? 'selected' : '' }}>Yeşil</option>
            <option value="danger" style="background-color: #dc3545;color: #FFFFFF;"
                {{ $onstyle == 'danger' ? 'selected' : '' }}>Kırmızı</option>
            <option value="warning" style="background-color: #ffc107;color: #FFFFFF;"
                {{ $onstyle == 'warning' ? 'selected' : '' }}>Turuncu</option>
            <option value="info" style="background-color: #17a2b8;color: #FFFFFF;"
                {{ $onstyle == 'info' ? 'selected' : '' }}>Soluk Mavi</option>
            <option value="light" style="background-color: #f8f9fa;color: #343a40;"
                {{ $onstyle == 'light' ? 'selected' : '' }}>Beyaz</option>
            <option value="dark" style="background-color: #343a40;color: #FFFFFF;"
                {{ $onstyle == 'dark' ? 'selected' : '' }}>Siyah</option>

        </select>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Pasif Durumu</label>
        <input type="text" name="setting[off]" class="form-control" value="{{ $settings['off'] ?? 'Kapalı' }}" />
    </div>
    <div class="col-xs-4">
        <label class="control-label">Pasif Değeri</label>
        <input type="text" name="setting[offValue]" class="form-control"
            value="{{ $settings['offValue'] ?? '0' }}" />
    </div>
    <div class="col-xs-4">
        <label class="control-label">Pasif Rengi</label>
        @php
            $offstyle = isset($setting['offstyle']) ? $setting['offstyle'] : 'secondary';
        @endphp
        <select class="form-control select2" name="setting[offstyle]">
            <option value="primary" style="background-color: #007bff;color: #FFFFFF;"
                {{ $offstyle == 'primary' ? 'selected' : '' }}>Mavi</option>
            <option value="secondary" style="background-color: #6c757d;color: #FFFFFF;"
                {{ $offstyle == 'secondary' ? 'selected' : '' }}>Gri</option>
            <option value="success" style="background-color: #28a745;color: #FFFFFF;"
                {{ $offstyle == 'success' ? 'selected' : '' }}>Yeşil</option>
            <option value="danger" style="background-color: #dc3545;color: #FFFFFF;"
                {{ $offstyle == 'danger' ? 'selected' : '' }}>Kırmızı</option>
            <option value="warning" style="background-color: #ffc107;color: #FFFFFF;"
                {{ $offstyle == 'warning' ? 'selected' : '' }}>Turuncu</option>
            <option value="info" style="background-color: #17a2b8;color: #FFFFFF;"
                {{ $offstyle == 'info' ? 'selected' : '' }}>Soluk Mavi</option>
            <option value="light" style="background-color: #f8f9fa;color: #343a40;"
                {{ $offstyle == 'light' ? 'selected' : '' }}>Beyaz</option>
            <option value="dark" style="background-color: #343a40;color: #FFFFFF;"
                {{ $offstyle == 'dark' ? 'selected' : '' }}>Siyah</option>
        </select>
    </div>
</div>
<br>
<br>
