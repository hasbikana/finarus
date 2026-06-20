<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\SavingGoal;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getDashboardData(): array
    {
        $user = Auth::user();

        $totalIncome = $user->transactions()
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $totalExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $balance = $user->accounts()->sum('balance');

        $activeSavingGoals = $user->savingGoals()
            ->whereColumn('current_amount', '<', 'target_amount')
            ->count();

        $recentTransactions = $user->transactions()
            ->with(['category', 'account'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(5)
            ->get();

        $budgetProgress = $user->budgets()
            ->with('category')
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->get();

        return [
            'balance' => $balance,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'active_saving_goals' => $activeSavingGoals,
            'recent_transactions' => $recentTransactions,
            'budget_progress' => $budgetProgress,
        ];
    }
}