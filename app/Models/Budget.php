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

    private ?float $cachedSpent = null;

    public function getSpentAttribute(): float
    {
        if ($this->cachedSpent !== null) {
            return $this->cachedSpent;
        }

        return $this->cachedSpent = (float) Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [
                \Carbon\Carbon::create($this->year, $this->month, 1)->startOfMonth(),
                \Carbon\Carbon::create($this->year, $this->month, 1)->endOfMonth(),
            ])
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
