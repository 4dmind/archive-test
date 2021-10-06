<?php

namespace App\Actions;

use Statamic\Actions\Action;
use Statamic\Contracts\Entries\Entry;
use Statamic\Facades\User;

class UnarchiveEntry extends Action
{
    protected static $title = 'Unarchive';

    /**
     * The run method
     *
     * @return void
     */
    public function run($entries, $values)
    {
        $entries->each(function ($entry) {
            $entry->doUnarchive(['user' => User::current()]);
        });
    }

    public function visibleTo($item)
    {
        return $item instanceof Entry && $item->archived();
    }

    public function confirmationText()
    {
        return 'Are you sure you want to unarchive this entry?|Are you sure you want to unarchive these :count entries?';
    }

    public function buttonText()
    {
        return 'Unarchive Entry|Unarchive :count Entries';
    }

    public function authorize($user, $entry)
    {
        return $user->can('archive', $entry);
    }
}
