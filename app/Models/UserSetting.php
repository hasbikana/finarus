<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'email_notifications', 'budget_alerts', 'theme', 'email_fetch_enabled', 'last_fetch_at'])]
class UserSetting extends Model
{
    use HasFactory;

    protected $casts = [
        'email_notifications' => 'boolean',
        'budget_alerts' => 'boolean',
        'email_fetch_enabled' => 'boolean',
        'last_fetch_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
