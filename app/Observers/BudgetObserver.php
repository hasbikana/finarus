<?php

namespace App\Observers;

use App\Models\Budget;

class BudgetObserver
{
    public function creating(Budget $budget): void
    {
        $existing = Budget::where('user_id', $budget->user_id)
            ->where('category_id', $budget->category_id)
            ->where('month', $budget->month)
            ->where('year', $budget->year)
            ->exists();

        if ($existing) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                'Budget untuk kategori dan bulan ini sudah ada.'
            );
        }
    }
}
