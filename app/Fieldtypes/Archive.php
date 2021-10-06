<?php

namespace App\Fieldtypes;

use Statamic\Entries\Entry;
use Statamic\Facades\User;
use Statamic\Fields\Fieldtype;

class Archive extends Fieldtype
{
    protected $icon = 'collections';

    /**
     * The blank/default value.
     *
     * @return array
     */
    public function defaultValue()
    {
        return null;
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return $data;
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        return $data;
    }

    public function preload()
    {
        return [
            'archived' => $this->entryValue('archived'),
            'archivedAt' => $this->entryValue('archived_at'),

            'hasPermission' => User::current()->can('archive', $this->entry()),
        ];
    }

    /**
     * Get entry
     * @return |null
     */
    private function entry()
    {
        if (!$this->entryExists()) {
            return null;
        }

        return $this->field->parent();
    }

    /**
     * Get entry id
     * @return null
     */
    private function entryId() {
        if (!$this->entryExists()) {
            return null;
        }

        return $this->entry()->id();
    }

    /**
     * Get entry value
     * @param $param
     * @return null
     */
    private function entryValue($param)
    {
        if (!$this->entryExists()) {
            return null;
        }

        return $this->entry()->value($param);
    }

    /**
     * Check if entry exists
     * @return bool
     */
    private function entryExists()
    {
        return $this->field->parent() instanceof Entry;
    }
}
