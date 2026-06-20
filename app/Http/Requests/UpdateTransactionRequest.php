<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|required|exists:categories,id',
            'account_id' => 'nullable|exists:accounts,id',
            'saving_goal_id' => 'nullable|exists:saving_goals,id',
            'type' => 'sometimes|required|in:income,expense',
            'amount' => 'sometimes|required|numeric|gt:0',
            'description' => 'nullable|string|max:1000',
            'transaction_date' => 'sometimes|required|date',
        ];
    }
}
