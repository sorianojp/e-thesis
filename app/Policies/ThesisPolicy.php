<?php

namespace App\Policies;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ThesisPolicy
{
    public function view(User $user, Thesis $thesis): bool {
        return $user->role === 'admin' || $thesis->user_id === $user->id;
    }

    public function update(User $user, Thesis $thesis): bool {
        return $thesis->user_id === $user->id && $thesis->status === 'pending';
    }

    public function admin(User $user): bool {
        return $user->role === 'admin';
    }

    public function downloadCertificate(User $user, Thesis $thesis): bool {
        return $thesis->status === 'approved'
            && ($user->role === 'admin' || $thesis->user_id === $user->id);
    }
}
