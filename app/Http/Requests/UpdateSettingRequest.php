<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email_notifications' => 'nullable|boolean',
            'budget_alerts' => 'nullable|boolean',
            'theme' => 'nullable|in:light,dark',
        ];
    }
}
