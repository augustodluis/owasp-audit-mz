<?php

namespace App\Policies;

use App\Models\Audit;
use App\Models\User;

class AuditPolicy
{
    public function view(User $user, Audit $audit): bool
    {
        return $user->id === $audit->user_id || $user->isAdmin();
    }

    public function delete(User $user, Audit $audit): bool
    {
        return $user->id === $audit->user_id || $user->isAdmin();
    }
}
