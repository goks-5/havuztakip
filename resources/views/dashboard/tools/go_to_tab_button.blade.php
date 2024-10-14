<div class="tool_data row">
  @include('dashboard.tools.toolSettings',['tool'=>$tool])

@php
    // Tool'un settings kısmını çözümlüyoruz
    $settings = json_decode($tool->settings, true);
    $dashboardUrl = $settings['dashboard_url'] ?? '#'; // Eğer URL kaydedilmemişse varsayılan #
@endphp

<div class="form-group">
    <button id="goToDashboardBtn" class="btn btn-primary" onclick="redirectToDashboard()">İzleme Ekranına Git</button>
</div>

<script>
    const dashboardUrl = "{{ $dashboardUrl }}"; // Settings'ten URL alınıyor

    function redirectToDashboard() {
        if (dashboardUrl && dashboardUrl !== '#') {
            window.location.href = dashboardUrl; // URL'ye yönlendiriliyor
        } else {
            alert('Geçerli bir dashboard URL mevcut değil.');
        }
    }
</script>