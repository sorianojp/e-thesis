<?php

namespace App\Policies;

use App\Models\Thesis;
use App\Models\User;

class ThesisPolicy
{
    public function view(User $user, Thesis $thesis): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAdviser()) {
            return (int) $thesis->adviser_id === $user->id;
        }

        return $thesis->user_id === $user->id;
    }

    public function update(User $user, Thesis $thesis): bool {
        return $thesis->user_id === $user->id && $thesis->status === 'pending';
    }

    public function admin(User $user): bool
    {
        return $user->isAdviser() || $user->isAdmin();
    }

    public function review(User $user, Thesis $thesis): bool
    {
        return $user->isAdviser() && (int) $thesis->adviser_id === $user->id;
    }

    public function downloadCertificate(User $user, Thesis $thesis): bool {
        return in_array($thesis->status, ['approved', 'passed'], true)
            && (
                $thesis->user_id === $user->id
                || $this->review($user, $thesis)
                || $user->isAdmin()
            );
    }
}
