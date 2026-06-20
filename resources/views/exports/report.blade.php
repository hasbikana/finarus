<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitle { color: #64748b; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; font-weight: 600; }
        .summary { display: flex; gap: 24px; margin-bottom: 24px; }
        .card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; flex: 1; }
        .card-label { font-size: 11px; color: #64748b; }
        .card-value { font-size: 16px; font-weight: 700; }
        .green { color: #16a34a; } .red { color: #dc2626; } .blue { color: #2563eb; }
    </style>
</head>
<body>
    <h1>Laporan Keuangan - {{ now()->create($year, $month)->format('F Y') }}</h1>
    <p class="subtitle">Finarus - Aplikasi Manajemen Keuangan Pribadi</p>

    <div class="summary">
        <div class="card">
            <div class="card-label">Total Pemasukan</div>
            <div class="card-value green">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="card-label">Total Pengeluaran</div>
            <div class="card-value red">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="card-label">Saldo Bersih</div>
            <div class="card-value blue">Rp {{ number_format($summary['balance'], 0, ',', '.') }}</div>
        </div>
    </div>

    <h2>Detail Pengeluaran per Kategori</h2>
    <table>
        <thead><tr><th>Kategori</th><th style="text-align:right">Total</th></tr></thead>
        <tbody>
            @foreach($categories as $c)
            <tr>
                <td>{{ $c['category_name'] ?? 'Tanpa Kategori' }}</td>
                <td style="text-align:right">Rp {{ number_format($c['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="color:#94a3b8;font-size:10px;margin-top:32px">Dibuat oleh Finarus pada {{ now()->format('d F Y H:i') }}</p>
</body>
</html>
