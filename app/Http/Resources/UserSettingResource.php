<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email_notifications' => $this->email_notifications,
            'budget_alerts' => $this->budget_alerts,
            'email_fetch_enabled' => $this->email_fetch_enabled,
            'theme' => $this->theme,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}