<div class="tool_data row" style="overflow: hidden;">
    @include('dashboard.tools.toolSettings', ['tool' => $tool])

    @if (isset($settings['title']) && !empty($settings['title']))
        <div class="col-xs-12 text-center">{{$settings['title']}}</div>
    @endif

    <!-- Buton, tool'un %90'ını kaplayacak ve ortalanacak -->
    <button class="btn btn-primary" 
            data-dashboard-id="{{ $tool->dashboard_id }}" 
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; height: 90%; opacity: 0;" 
            onclick="goToDashboard(this)">
    </button>

    @php
        // Protokolü al (http ya da https)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        // Sunucunun tam adresini oluştur
        $serverUrl = $protocol . $_SERVER['HTTP_HOST'] . '/dashboard/';
    @endphp

    <script>
        function goToDashboard(button) {
            // Butonun data-dashboard-id özelliğinden dashboard ID'yi al
            const dashboardId = button.getAttribute('data-dashboard-id');
            
            if (dashboardId) {
                // PHP ile oluşturulan sunucu URL'sini kullanarak URL oluştur
                const serverUrl = "{{ $serverUrl }}";
                const newUrl = serverUrl + dashboardId;

                // Yönlendirmeden önce konsola loglama
                console.log("Yönlendirme URL'si: ", newUrl);

                // Yeni URL'ye yönlendir
                window.location.href = newUrl;
            } else {
                alert("Lütfen bir dashboard seçin.");
            }
        }
    </script>
</div>
