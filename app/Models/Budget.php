<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\BudgetObserver;

#[Fillable(['user_id', 'category_id', 'amount', 'month', 'year'])]
#[ObservedBy(BudgetObserver::class)]
class Budget extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getSpentAttribute(): float
    {
        return (float) Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $this->month)
            ->whereYear('transaction_date', $this->year)
            ->sum('amount');
    }

    public function getProgressAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }

        return min(100, round(($this->spent / $this->amount) * 100, 1));
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->spent > $this->amount;
    }
}
