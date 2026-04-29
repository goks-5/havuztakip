<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Arıza Detayları</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        h1 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Arıza Detayları (ID: {{ $fault->id }})</h1>
    <table>
        <tbody>
            <tr>
                <th>ID</th>
                <td>{{ $fault->id }}</td>
            </tr>
            <tr>
                <th>Ekipman</th>
                <td>{{ optional($fault->equipment)->name }}</td>
            </tr>
            <tr>
                <th>Arıza Tipi</th>
                <td>{{ $fault->fault_type }}</td>
            </tr>
            <tr>
                <th>Arıza Kodu</th>
                <td>{{ $fault->fault_code }}</td>
            </tr>
            <tr>
                <th>Arıza Açıklaması</th>
                <td>{{ $fault->fault_comment }}</td>
            </tr>
            <tr>
                <th>Bildiren Personel</th>
                <td>{{ $fault->reporting_user }}</td>
            </tr>
            <tr>
                <th>Oluşturma Tarihi</th>
                <td>{{ $fault->created_at }}</td>
            </tr>
            <tr>
                <th>Arıza Tamamlanma Zamanı</th>
                <td>{{ $fault->finish_at }}</td>
            </tr>
            <tr>
                <th>Bakımcı</th>
                <td>{{ optional($fault->staff)->name }}</td>
            </tr>
            <tr>
                <th>Bakımcı Notu</th>
                <td>{{ $fault->maintainer_note }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
