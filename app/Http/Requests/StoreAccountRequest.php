<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'type' => 'required|in:cash,ewallet,bank,credit_card',
            'account_number' => 'nullable|string|max:100',
            'balance' => 'nullable|numeric|min:0',
            'logo' => 'nullable|string|max:100',
            'email_scopes' => 'nullable|array',
            'email_scopes.*' => 'email:rfc',
        ];
    }
}
