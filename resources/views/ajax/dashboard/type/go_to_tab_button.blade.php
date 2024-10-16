@php
    use App\Dashboard;
    $dashboards = Dashboard::where('user_id', Auth::user()->id)->get();
    $selectedDashboardId = $dashboards->first() ? $dashboards->first()->id : null;
@endphp

<div class="form-group">
    <label for="dashboardDropdown">İzleme Ekranı</label>
    <select class="form-control" id="dashboardDropdown" name="setting[dashboard_id]" >
        @foreach($dashboards as $dashboard)
            <option value="{{ $dashboard->id }}">{{ $dashboard->title }}</option>
        @endforeach
    </select>
</div>

<div class="form-group row">
@include('ajax.dashboard.type.element.main',['slug'=>'go_to_tab_button', 'ek' => 0])
</div>

<div class="form-group">
  <label for="image">Arka Plan</label>

  <input type="file" class="form-control-file" name="backgroud" id="image">
  @if(isset($page->img))
    <img src="../images/{{$page->img}}" style="max-width: 200px;" />
  @endif
</div>