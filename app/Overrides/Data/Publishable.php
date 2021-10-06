<?php

namespace App\Overrides\Data;

trait Publishable
{
    use \Statamic\Data\Publishable;

    public function publish($options = [])
    {
        $this->set('archived', false);
        $this->remove('archived_at');
        $this->saveQuietly();

        if (method_exists($this, 'revisionsEnabled') && $this->revisionsEnabled()) {
            return $this->publishWorkingCopy($options);
        }

        $this->published(true)->save();

        return $this;
    }
}
