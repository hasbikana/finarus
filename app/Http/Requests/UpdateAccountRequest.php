<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'provider' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:ewallet,bank,credit_card',
            'account_number' => 'nullable|string|max:100',
            'balance' => 'nullable|numeric|min:0',
            'logo' => 'nullable|string|max:100',
        ];
    }
}
