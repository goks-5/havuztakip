<!doctype html>
<html lang="tr">
  <body style="font-family: Arial, sans-serif; font-size:14px; color:#222;">
    <h2 style="margin:0 0 8px;">
      Durum Özeti @isset($companyName) - {{ $companyName }} @endisset
    </h2>
    <p style="margin:0 0 12px;">
      <strong>Cihaz:</strong> {{ $summary['deviceCount'] }}
      &nbsp;|&nbsp;
      <strong>Nokta:</strong> {{ $summary['pointCount'] }}
      &nbsp;|&nbsp;
      <strong>Çevrim Dışı:</strong> {{ $summary['oflineCount'] }}
    </p>

    {{-- (Çevrim dışı cihazlar + değişmeyen etiketler tabloları burada kalabilir) --}}
    <p style="color:#888; margin-top:16px;">Bu e-posta otomatik olarak gönderilmiştir.</p>
  </body>
</html>
