<?php

namespace App\Overrides\Entries;

use App\Traits\Archivable;

class Entry extends \Statamic\Entries\Entry
{
    use Archivable;

    public function status()
    {
        $collection = $this->collection();

        if ($this->archived()) {
            return 'archived';
        }

        if (! $this->published()) {
            return 'draft';
        }

        if (! $collection->dated() && $this->published()) {
            return 'published';
        }

        if ($collection->futureDateBehavior() === 'private' && $this->date()->isFuture()) {
            return 'scheduled';
        }

        if ($collection->pastDateBehavior() === 'private' && $this->date()->isPast()) {
            return 'expired';
        }

        return 'published';
    }

    public function private()
    {
        $collection = $this->collection();

        if ($this->archived()) {
            return true;
        }

        if (! $collection->dated()) {
            return false;
        }

        if ($collection->futureDateBehavior() === 'private' && $this->date()->isFuture()) {
            return true;
        }

        if ($collection->pastDateBehavior() === 'private' && $this->date()->lte(now())) {
            return true;
        }

        return false;
    }
}
