<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'balance' => (float) ($this['balance'] ?? 0),
            'total_income' => (float) ($this['total_income'] ?? 0),
            'total_expense' => (float) ($this['total_expense'] ?? 0),
            'active_saving_goals' => $this['active_saving_goals'] ?? 0,
            'recent_transactions' => TransactionResource::collection($this['recent_transactions'] ?? collect()),
            'budget_progress' => BudgetResource::collection($this['budget_progress'] ?? collect()),
        ];
    }
}