<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    public function monthly(Request $request): JsonResponse
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $summary = $this->reportService->getMonthlySummary((int) $month, (int) $year);

        return response()->json($summary);
    }

    public function categories(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2020|max:2099',
        ]);

        $type = $request->input('type');
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $breakdown = $this->reportService->getCategoryBreakdown($type, (int) $month, (int) $year);

        return response()->json([
            'type' => $type,
            'month' => (int) $month,
            'year' => (int) $year,
            'categories' => $breakdown,
        ]);
    }

    public function trend(Request $request): JsonResponse
    {
        $year = $request->input('year', now()->year);

        $trend = $this->reportService->getMonthlyTrend((int) $year);

        return response()->json([
            'year' => (int) $year,
            'trend' => $trend,
        ]);
    }

    public function export(Request $request): Response
    {
        $format = $request->input('format', 'csv');
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $summary = $this->reportService->getMonthlySummary($month, $year);
        $categories = $this->reportService->getCategoryBreakdown('expense', $month, $year);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.report', compact('summary', 'categories', 'month', 'year'));
            return $pdf->download("laporan_{$year}_{$month}.pdf");
        }

        $csv = $this->generateCsv($summary, $categories, $month, $year);
        return response($csv)->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=laporan_{$year}_{$month}.csv");
    }

    protected function generateCsv(array $summary, array $categories, int $month, int $year): string
    {
        $lines = [];
        $lines[] = '"Laporan Keuangan - ' . now()->create($year, $month)->format('F Y') . '"';
        $lines[] = '';
        $lines[] = '"Ringkasan"';
        $lines[] = '"Total Pemasukan","Rp ' . number_format($summary['total_income'], 0, ',', '.') . '"';
        $lines[] = '"Total Pengeluaran","Rp ' . number_format($summary['total_expense'], 0, ',', '.') . '"';
        $lines[] = '"Saldo Bersih","Rp ' . number_format($summary['balance'], 0, ',', '.') . '"';
        $lines[] = '';
        $lines[] = '"Kategori","Total"';

        foreach ($categories as $c) {
            $lines[] = '"' . ($c['category_name'] ?? 'Tanpa Kategori') . '","Rp ' . number_format($c['total'], 0, ',', '.') . '"';
        }

        return implode("\n", $lines);
    }
}