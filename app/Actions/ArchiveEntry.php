<?php

namespace App\Actions;

use Statamic\Actions\Action;
use Statamic\Contracts\Entries\Entry;
use Statamic\Facades\User;

class ArchiveEntry extends Action
{
    protected static $title = 'Archive';

    /**
     * The run method
     *
     * @return void
     */
    public function run($entries, $values)
    {
        $entries->each(function ($entry) {
            $entry->doArchive(['user' => User::current()]);
        });
    }

    public function visibleTo($item)
    {
        return $item instanceof Entry && !$item->archived();
    }

    public function confirmationText()
    {
        return 'Are you sure you want to archive this entry?|Are you sure you want to archive these :count entries?';
    }

    public function buttonText()
    {
        return 'Archive Entry|Archive :count Entries';
    }

    public function authorize($user, $entry)
    {
        return $user->can('archive', $entry);
    }
}
