<div class="tool_data row" style="position: relative; width: 100%; height: 100%;">
    @include('dashboard.tools.toolSettings', ['tool' => $tool])
    
    @if (isset($settings['title']) && !empty($settings['title']))
        <div class="col-xs-12 text-center">{{ $settings['title'] }}</div>
    @endif

    @php
    // Kullanıcının kaydettiği dashboard ID'sini veritabanından alıyoruz
    $dashboardId = isset($settings['dashboard_id']) ? $settings['dashboard_id'] : null;
    $dashboard = $dashboardId ? App\Dashboard::find($dashboardId) : null;
    @endphp
    
    @if ($dashboard)
        <button class="redirect-btn" onclick="window.location.href='{{ url('dashboard/' . $dashboard->id) }}';">
            <!-- Butonun içeriğini buraya koyabiliriz ama görünmeyecek -->
        </button>
    @else
        <div class="col-xs-12 text-center">
            <p>Dashboard seçilmemiş.</p>
        </div>
    @endif
</div>

<!-- CSS Kodları -->
<style>
    .redirect-btn {
        display: block; /* Butonun block yapıda olmasını sağlar */
        width: 90%; /* Tool genişliğini %90 yapar */
        height: 90%; /* Tool yüksekliğini %90 yapar */
        background-color: transparent; /* Butonun arka planını şeffaf yapar */
        border: none; /* Buton kenarlıklarını kaldır */
        position: absolute; /* Butonun tool container'ının tamamını kaplaması için */
        top: 5px; /* Üstten sıfırlanmış */
        left: 5px; /* Soldan sıfırlanmış */
        cursor: pointer; /* Tıklanabilir olduğunu göstermek için imleci değiştir */
        z-index: 10; /* Butonu ön plana getirir */
    }
</style>
