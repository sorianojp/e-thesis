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

        $thesis->loadMissing(['thesisTitle', 'thesisTitle.members']);
        $thesisTitle = $thesis->thesisTitle;

        if ($user->isAdviser()) {
            return $thesisTitle && (int) $thesisTitle->adviser_id === $user->id;
        }

        if (! $thesisTitle) {
            return false;
        }

        if ((int) $thesisTitle->user_id === $user->id) {
            return true;
        }

        return $thesisTitle->hasMember($user->id);
    }

    public function update(User $user, Thesis $thesis): bool {
        $thesis->loadMissing('thesisTitle');

        return $thesis->status === 'pending'
            && $thesis->thesisTitle
            && (int) $thesis->thesisTitle->user_id === $user->id;
    }

    public function admin(User $user): bool
    {
        return $user->isAdviser() || $user->isAdmin();
    }

    public function review(User $user, Thesis $thesis): bool
    {
        $thesis->loadMissing('thesisTitle');

        return $user->isAdviser()
            && $thesis->thesisTitle
            && (int) $thesis->thesisTitle->adviser_id === $user->id;
    }

    public function downloadCertificate(User $user, Thesis $thesis): bool {
        $thesis->loadMissing('thesisTitle');

        $stage = request()->query('stage', 'final');

        $eligible = false;
        if ($thesis->thesisTitle) {
            $eligible = $stage === 'title'
                ? $thesis->thesisTitle->titleDefenseApproved()
                : $thesis->thesisTitle->chaptersAreApproved();
        }

        return $eligible
            && $thesis->status === 'approved'
            && (
                ($thesis->thesisTitle && (int) $thesis->thesisTitle->user_id === $user->id)
                || $this->review($user, $thesis)
                || $user->isAdmin()
            );
    }
}
