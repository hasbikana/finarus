<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    public function getMonthlySummary(?int $month = null, ?int $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        $user = Auth::user();

        $income = $user->transactions()
            ->where('type', 'income')
            ->where(fn($q) => $q->where('is_pending', false)->orWhereNull('is_pending'))
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $expense = $user->transactions()
            ->where('type', 'expense')
            ->where(fn($q) => $q->where('is_pending', false)->orWhereNull('is_pending'))
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        return [
            'month' => $month,
            'year' => $year,
            'total_income' => (float) $income,
            'total_expense' => (float) $expense,
            'balance' => (float) ($income - $expense),
        ];
    }

    public function getCategoryBreakdown(string $type, ?int $month = null, ?int $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        $user = Auth::user();

        return $user->transactions()
            ->where('type', $type)
            ->where(fn($q) => $q->where('is_pending', false)->orWhereNull('is_pending'))
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get()
            ->map(fn ($item) => [
                'category_id' => $item->category_id,
                'category_name' => $item->category?->name,
                'category_icon' => $item->category?->icon,
                'category_color' => $item->category?->color,
                'total' => (float) $item->total,
            ]);
    }

    public function getMonthlyTrend(?int $year = null): array
    {
        $year = $year ?? now()->year;
        $user = Auth::user();

        $transactions = $user->transactions()
            ->where(fn($q) => $q->where('is_pending', false)->orWhereNull('is_pending'))
            ->whereYear('transaction_date', $year)
            ->selectRaw('MONTH(transaction_date) as month, type, SUM(amount) as total')
            ->groupBy('month', 'type')
            ->get();

        $trend = [];
        for ($m = 1; $m <= 12; $m++) {
            $income = $transactions->where('month', $m)->where('type', 'income')->first();
            $expense = $transactions->where('month', $m)->where('type', 'expense')->first();

            $trend[] = [
                'month' => $m,
                'month_name' => \DateTime::createFromFormat('!m', $m)->format('F'),
                'income' => (float) ($income->total ?? 0),
                'expense' => (float) ($expense->total ?? 0),
                'net' => (float) (($income->total ?? 0) - ($expense->total ?? 0)),
            ];
        }

        return $trend;
    }
}