<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'saving_goal_id' => 'nullable|exists:saving_goals,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:1000',
            'transaction_date' => 'required|date',
        ];
    }
}
