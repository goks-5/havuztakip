<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { background-color: #e74c3c; color: #ffffff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .content { padding: 30px; color: #333333; line-height: 1.6; }
        .data-card { background-color: #f9f9f9; border-left: 4px solid #e74c3c; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .data-row { margin-bottom: 10px; display: flex; border-bottom: 1px solid #eeeeee; padding-bottom: 5px; }
        .label { font-weight: bold; width: 150px; color: #7f8c8d; }
        .value { font-weight: 600; color: #2c3e50; }
        .alert-value { color: #e74c3c; font-size: 18px; }
        .footer { background-color: #f4f4f4; color: #95a5a6; padding: 15px; text-align: center; font-size: 12px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #3498db; color: #ffffff; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LİMİT AŞIM UYARISI</h1>
        </div>
        <div class="content">
            <p>Merhaba,</p>
            <p>Enerji Yönetim Sistemi tarafından yapılan otomatik kontrolde, bir cihazınızda belirlenen limitlerin dışına çıkıldığı tespit edilmiştir.</p>
            
            <div class="data-card">
                <div class="data-row">
                    <div class="label">Cihaz:</div>
                    <div class="value">{{ $eventData['device_name'] }}</div>
                </div>
                <div class="data-row">
                    <div class="label">Parametre:</div>
                    <div class="value">{{ $eventData['tag_name'] }}</div>
                </div>
                <div class="data-row" style="border:none;">
                    <div class="label">Ölçülen Değer:</div>
                    <div class="value alert-value">{{ $eventData['current_value'] }}</div>
                </div>
            </div>

            <p><strong>Belirlenen Güvenli Aralık:</strong> 
                <span style="color: #27ae60;">{{ $eventData['min'] }}</span> - 
                <span style="color: #27ae60;">{{ $eventData['max'] }}</span>
            </p>

            <p style="font-size: 13px; color: #7f8c8d;">
                <i class="voyager-clock"></i> Olay Zamanı: {{ date('d.m.Y H:i:s') }}
            </p>

            <center>
                <a href="" class="btn">Paneli Görüntüle</a>
            </center>
        </div>
        <div class="footer">
            Bu e-posta <b>T3 Yazılım Enerji Yönetim Sistemi</b> tarafından otomatik olarak oluşturulmuştur.<br>
            © {{ date('Y') }} T3 Yazılım / Bursa
        </div>
    </div>
</body>
</html>