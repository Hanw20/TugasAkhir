<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Laporan') — Toko Bahan Bangunan X</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        @page { size: auto; margin: 10mm; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #fff; color: #000; padding: 10px; font-size: 9pt; }
        .d-print-none { display: none !important; }
        .d-print-block { display: block !important; }
        table { width: 100% !important; border-collapse: collapse !important; font-size: 8.5pt !important; margin-top: 10px; }
        th, td { border: 1px solid #444 !important; padding: 5px 8px !important; vertical-align: middle !important; color: #000 !important; }
        thead th { background-color: #f1f5f9 !important; font-weight: bold !important; text-align: center; }
        tfoot td { font-weight: bold !important; background-color: #f8fafc !important; }
        .badge { border: 1px solid #555 !important; color: #000 !important; background: transparent !important; font-size: 7.5pt !important; padding: 2px 4px !important; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
