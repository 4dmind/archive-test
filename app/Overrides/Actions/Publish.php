<?php

namespace App\Overrides\Actions;

use Statamic\Contracts\Entries\Entry;

class Publish extends \Statamic\Actions\Publish
{
    public function visibleTo($item)
    {
        return $item instanceof Entry && ! $item->published() && !$item->archived();
    }

    public function visibleToBulk($items)
    {
        if ($items->whereInstanceOf(Entry::class)->count() !== $items->count()) {
            return false;
        }

        if ($items->filter->published()->count() === $items->count()) {
            return false;
        }

        return true;
    }
}
