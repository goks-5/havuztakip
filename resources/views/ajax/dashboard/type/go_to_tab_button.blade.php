@php
    use App\Dashboard;
    $dashboards = Dashboard::where('user_id', Auth::user()->id)->get();
    $selectedDashboardId = $dashboards->first() ? $dashboards->first()->id : null;
@endphp

<div class="form-group">
    <label for="dashboardDropdown">İzleme Ekranı</label>
    <select class="form-control" id="dashboardDropdown" name="dashboard_id" onchange="setDashboardId(this.value)">
        @foreach($dashboards as $dashboard)
            <option value="{{ $dashboard->id }}">{{ $dashboard->title }}</option>
        @endforeach
    </select>
</div>

<!-- Dashboard ID alanı, sadece popup'ta görünüyor -->
<div class="form-group">
    <label for="dashboardId">Dashboard ID</label>
    <input type="text" class="form-control" id="dashboardId" name="dashboard_id" value="{{ $selectedDashboardId }}" readonly>
</div>

<script>
    function setDashboardId(dashboardId) {
        // localStorage'a seçilen dashboardId'yi kaydet
        localStorage.setItem('selectedDashboardId', dashboardId);

        // Dashboard ID input alanını güncelle
        document.getElementById('dashboardId').value = dashboardId;
    }
</script>
