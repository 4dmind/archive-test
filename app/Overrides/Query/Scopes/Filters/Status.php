<?php

namespace App\Overrides\Query\Scopes\Filters;

use Statamic\Facades\Collection;
use Statamic\Query\Scopes\Filter;

class Status extends \Statamic\Query\Scopes\Filters\Status
{
    public function badge($values)
    {
        if ($values['status'] === 'published') {
            return __('Published');
        } elseif ($values['status'] === 'scheduled') {
            return __('Scheduled');
        } elseif ($values['status'] === 'expired') {
            return __('Expired');
        } elseif ($values['status'] === 'draft') {
            return __('Draft');
        } elseif ($values['status'] === 'archived') {
            return __('Archived');
        }
    }

    protected function options()
    {
        $options = collect([
            'published' => __('Published'),
            'scheduled' => __('Scheduled'),
            'expired' => __('Expired'),
            'draft' => __('Draft'),
            'archived' => __('Archived'),
        ]);

        if (! $collection = $this->collection()) {
            return $options;
        }

        if ($collection->dated() && $collection->futureDateBehavior() === 'private') {
            $options->forget('expired');
        } elseif ($collection->dated() && $collection->pastDateBehavior() === 'private') {
            $options->forget('scheduled');
        } else {
            $options->forget('scheduled');
            $options->forget('expired');
        }

        return $options;
    }
}
