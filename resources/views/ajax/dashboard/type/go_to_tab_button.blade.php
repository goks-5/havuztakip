@php
    use App\Dashboard;
    $dashboards = Dashboard::where('user_id', Auth::user()->id)->get();
    $selectedDashboardId = $dashboards->first() ? $dashboards->first()->id : null;
@endphp

@if ($dashboards->isEmpty())
    <p>Hiç izleme ekranı bulunamadı.</p>
@else
    <div class="form-group">
        <label for="dashboardDropdown">İzleme Ekranı</label>
        <select class="form-control" id="dashboardDropdown" name="dashboard_id" onchange="setDashboardId(this.value)">
            @foreach($dashboards as $dashboard)
                <option value="{{ $dashboard->id }}" {{ $selectedDashboardId == $dashboard->id ? 'selected' : '' }}>
                    {{ $dashboard->title }}
                </option>
            @endforeach
        </select>
    </div>
@endif