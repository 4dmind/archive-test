<?php

namespace App\Traits;

trait Archivable
{
    public function archived(): bool
    {
        return !!$this->value('archived');
    }

    public function doArchive($options = [])
    {
        $this->unpublish($options);

        $this->set('archived', true);
        $this->set('archived_at', date('Y-m-d H:i'));
        $this->published(false);

        $this->saveQuietly();

        return $this;
    }

    public function doUnarchive($options = [])
    {
        $this->publish($options);

        $this->set('archived', false);
        $this->remove('archived_at');
        $this->published(true);

        $this->saveQuietly();

        return $this;
    }
}
