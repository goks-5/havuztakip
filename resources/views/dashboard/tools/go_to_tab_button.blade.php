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
    
    <div id="content_{{$tool->id}}">
        <button class="redirect-btn" data-dashboard-id="{{ $dashboardId }}">
            <!-- Buton görünmeyecek şekilde tasarlandı, içeriği buraya koyabiliriz -->
        </button>
        
        @if (!$dashboard)
            <div class="col-xs-12 text-center">
                <p>Dashboard seçilmemiş.</p>
            </div>
        @endif
    </div>
</div>

@push('javascript')
<script>
$(document).ready(function () {
    // Tüm redirect butonlarına click event ekliyoruz
    $('.redirect-btn').on('click', function (e) {
        e.preventDefault();

        // Butonun data-dashboard-id özelliğinden dashboard ID'sini alıyoruz
        var dashboardId = $(this).data('dashboard-id');

        // Eğer dashboard ID varsa yönlendirme yap
        if (dashboardId) {
            var url = '/dashboard/' + dashboardId; // Dinamik olarak URL'yi oluşturuyoruz
            window.location.href = url; // Kullanıcıyı yönlendiriyoruz
        } else {
            alert('Dashboard seçilmemiş.');
        }
    });
});
</script>
@endpush

<!-- CSS Kodları -->
<style>
    .redirect-btn {
        display: block;
        width: 90%;
        height: 90%;
        background-color: transparent;
        border: none;
        position: absolute;
        top: 5px;
        left: 5px;
        cursor: pointer;
        z-index: 10;
    }
</style>
