<?php

namespace App\Policies;

use App\Models\PendingNotification;
use App\Models\User;

class PendingNotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PendingNotification $pendingNotification): bool
    {
        return $user->id === $pendingNotification->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PendingNotification $pendingNotification): bool
    {
        return $user->id === $pendingNotification->user_id;
    }

    public function delete(User $user, PendingNotification $pendingNotification): bool
    {
        return $user->id === $pendingNotification->user_id;
    }
}
