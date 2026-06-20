<?php

namespace App\Services;

use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetService
{
    public function getBudgetsWithSpent(?int $month = null, ?int $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return Auth::user()->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->get();
    }
}