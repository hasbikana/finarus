<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'transaction_date' => $this->transaction_date?->toDateString(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'account' => new AccountResource($this->whenLoaded('account')),
            'saving_goal' => new SavingGoalResource($this->whenLoaded('savingGoal')),
            'is_pending' => $this->is_pending ?? false,
            'pending_source' => $this->pending_source,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}