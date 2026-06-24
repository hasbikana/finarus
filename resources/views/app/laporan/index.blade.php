@extends('layouts.app')

@section('title', 'Laporan - Finarus')
@section('page-title', 'Laporan Keuangan')
@section('page-description', 'Analisis mendalam tentang keuangan Anda')

@section('page-actions')
<div class="flex gap-2">
    <button onclick="exportReport('pdf')" class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        Ekspor PDF
    </button>
</div>
@endsection

@section('content')
<div class="bg-card rounded-lg shadow-lg p-5 mb-4">
    <div class="flex flex-col sm:flex-row gap-3">
        <select id="report-period" onchange="loadData()" class="h-9 px-3 rounded-md border border-border bg-card text-foreground">
            <option value="week">Minggu Ini</option>
            <option value="month" selected>Bulan Ini</option>
            <option value="year">Tahun Ini</option>
        </select>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    <div class="bg-card rounded-lg shadow-lg p-5">
        <p class="text-xs text-muted-foreground mb-1">Total Pemasukan</p>
        <div id="total-income-skel" class="h-8 w-32 bg-muted rounded animate-pulse"></div>
        <p id="total-income" class="text-2xl font-bold text-green-500 hidden">Rp 0</p>
    </div>
    <div class="bg-card rounded-lg shadow-lg p-5">
        <p class="text-xs text-muted-foreground mb-1">Total Pengeluaran</p>
        <div id="total-expense-skel" class="h-8 w-32 bg-muted rounded animate-pulse"></div>
        <p id="total-expense" class="text-2xl font-bold text-red-500 hidden">Rp 0</p>
    </div>
    <div class="bg-card rounded-lg shadow-lg p-5">
        <p class="text-xs text-muted-foreground mb-1">Saldo Bersih</p>
        <div id="net-savings-skel" class="h-8 w-32 bg-muted rounded animate-pulse"></div>
        <p id="net-savings" class="text-2xl font-bold text-blue-500 hidden">Rp 0</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-card rounded-lg shadow-lg p-5">
        <h3 class="font-semibold mb-4">Tren Bulanan</h3>
        <div class="h-72 relative">
            <div id="chart-trend-loader" class="absolute inset-0 flex items-center justify-center"><x-spinner class="w-8 h-8 text-muted-foreground" /></div>
            <canvas id="chart-trend"></canvas>
        </div>
    </div>
    <div class="bg-card rounded-lg shadow-lg p-5">
        <h3 class="font-semibold mb-4">Distribusi Pengeluaran</h3>
        <div class="h-72 relative">
            <div id="chart-cat-loader" class="absolute inset-0 flex items-center justify-center"><x-spinner class="w-8 h-8 text-muted-foreground" /></div>
            <canvas id="chart-category"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let chartTrend = null, chartCategory = null;

function chartTextColor() { return Finarus.isDarkMode() ? '#e2e8f0' : '#1e293b'; }
function chartGridColor() { return Finarus.isDarkMode() ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)'; }

async function loadData() {
    const period = document.getElementById('report-period').value;
    const now = new Date();
    let month = now.getMonth() + 1, year = now.getFullYear();

    if (period === 'week') {
        const start = new Date(now); start.setDate(now.getDate() - 7);
        month = start.getMonth() + 1; year = start.getFullYear();
    } else if (period === 'year') {
        month = 0;
    }

    try {
        const res = await fetch(`{{ url('laporan/data/monthly') }}?month=${month}&year=${year}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': Finarus.csrf() }
        });
        const d = await res.json();
        document.getElementById('total-income').textContent = Finarus.formatRupiah(d.total_income || 0);
        document.getElementById('total-expense').textContent = Finarus.formatRupiah(d.total_expense || 0);
        document.getElementById('net-savings').textContent = Finarus.formatRupiah(d.balance || 0);
        document.getElementById('total-income').classList.remove('hidden');
        document.getElementById('total-expense').classList.remove('hidden');
        document.getElementById('net-savings').classList.remove('hidden');
        document.getElementById('total-income-skel').classList.add('hidden');
        document.getElementById('total-expense-skel').classList.add('hidden');
        document.getElementById('net-savings-skel').classList.add('hidden');
    } catch(e) { Finarus.toast('Gagal memuat ringkasan', 'error'); }

    try {
        const resCat = await fetch(`{{ url('laporan/data/categories') }}?type=expense&month=${month}&year=${year}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': Finarus.csrf() }
        });
        const catData = await resCat.json();
        renderCategoryChart(catData.categories || []);
    } catch(e) { Finarus.toast('Gagal memuat kategori', 'error'); }

    try {
        const resTrend = await fetch(`{{ url('laporan/data/trend') }}?year=${year}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': Finarus.csrf() }
        });
        const trendData = await resTrend.json();
        renderTrendChart(trendData.trend || []);
    } catch(e) { Finarus.toast('Gagal memuat tren', 'error'); }
}

function renderTrendChart(data) {
    const ctx = document.getElementById('chart-trend').getContext('2d');
    if (chartTrend) chartTrend.destroy();
    chartTrend = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.month_name),
            datasets: [
                { label: 'Pemasukan', data: data.map(d => d.income), backgroundColor: Finarus.chartColors.green, borderRadius: 4 },
                { label: 'Pengeluaran', data: data.map(d => d.expense), backgroundColor: Finarus.chartColors.red, borderRadius: 4 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                x: { ticks: { color: chartTextColor() }, grid: { color: chartGridColor() } },
                y: { ticks: { color: chartTextColor(), callback: v => Finarus.formatRupiah(v) }, grid: { color: chartGridColor() } }
            },
            plugins: { legend: { labels: { color: chartTextColor() } } }
        }
    });
    document.getElementById('chart-trend-loader').classList.add('hidden');
}

function renderCategoryChart(data) {
    const ctx = document.getElementById('chart-category').getContext('2d');
    if (chartCategory) chartCategory.destroy();
    const colors = Object.values(Finarus.chartColors);
    chartCategory = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.category_name || 'Lainnya'),
            datasets: [{ data: data.map(d => d.total), backgroundColor: colors, borderWidth: 0 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: chartTextColor(), padding: 12 } }
            }
        }
    });
    document.getElementById('chart-cat-loader').classList.add('hidden');
}

async function exportReport(format) {
    Finarus.toast('Menyiapkan export...');
    const now = new Date();
    const month = now.getMonth() + 1;
    const year = now.getFullYear();
    try {
        const res = await fetch(`{{ url('laporan/export') }}?format=${format}&month=${month}&year=${year}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': Finarus.csrf() }
        });
        if (res.ok) {
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `laporan_${year}_${month}.${format}`;
            a.click();
            URL.revokeObjectURL(url);
            Finarus.toast('Export berhasil');
        } else {
            Finarus.toast('Export gagal', 'error');
        }
    } catch(e) { Finarus.toast('Koneksi gagal', 'error'); }
}

document.addEventListener('DOMContentLoaded', loadData);
Finarus.onThemeChange(() => { loadData(); });
    document.getElementById('chart-trend-loader').classList.add('hidden');
    document.getElementById('chart-cat-loader').classList.add('hidden');
</script>
@endpush
