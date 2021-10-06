<?php

namespace App\Overrides\Actions;

use Statamic\Contracts\Entries\Entry;
use Statamic\Facades\User;

class Unpublish extends \Statamic\Actions\Unpublish
{
    public function visibleTo($item)
    {
        return $item instanceof Entry && $item->published() && !$item->archived();
    }

    public function visibleToBulk($items)
    {
        if ($items->whereInstanceOf(Entry::class)->count() !== $items->count()) {
            return false;
        }

        if ($items->reject->published()->count() === $items->count()) {
            return false;
        }

        return true;
    }
}
