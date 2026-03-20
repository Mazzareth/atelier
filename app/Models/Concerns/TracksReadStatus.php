<?php

namespace App\Models\Concerns;

use App\Models\User;
use Carbon\Carbon;

trait TracksReadStatus
{
    abstract protected function getReadAtFor(User $user): ?Carbon;

    abstract protected function setReadAtFor(User $user): void;

    public function unreadCountFor(User $user): int
    {
        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->when($this->getReadAtFor($user), fn ($query, $readAt) => $query->where('created_at', '>', $readAt))
            ->count();
    }

    public function markReadFor(User $user): void
    {
        $this->setReadAtFor($user);
    }
}
