<?php

namespace App\Overrides\Policies;

use Statamic\Facades\User;

class EntryPolicy extends \Statamic\Policies\EntryPolicy
{
    public function archive($user, $entry)
    {
        $user = User::fromUser($user);
        return $user->hasPermission("archive {$entry->collectionHandle()} entries");
    }
}
