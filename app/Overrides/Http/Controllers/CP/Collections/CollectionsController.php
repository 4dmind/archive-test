<?php

namespace App\Overrides\Http\Controllers\CP\Collections;

use App\Support\EntriesPermissions;
use Statamic\Contracts\Entries\Collection as CollectionContract;
use Statamic\CP\Column;
use Statamic\Facades\Collection;
use Statamic\Facades\User;

class CollectionsController extends \Statamic\Http\Controllers\CP\Collections\CollectionsController
{
    public function index()
    {
        $this->authorize('index', CollectionContract::class, __('You are not authorized to view collections.'));

        $collections = Collection::all()->filter(function ($collection) {
            return User::current()->can('view', $collection);
        })->map(function ($collection) {
            // EXTENSION: filter entries by user permissions
            $entries = \Statamic\Facades\Entry::query()->where('collection', $collection->handle())->get();
            $entries = EntriesPermissions::filterEntries($entries);
            // END EXTENSION

            return [
                'id' => $collection->handle(),
                'title' => $collection->title(),
                'entries' => $entries->count(),
                'edit_url' => $collection->editUrl(),
                'delete_url' => $collection->deleteUrl(),
                'entries_url' => cp_route('collections.show', $collection->handle()),
                'blueprints_url' => cp_route('collections.blueprints.index', $collection->handle()),
                'scaffold_url' => cp_route('collections.scaffold', $collection->handle()),
                'deleteable' => User::current()->can('delete', $collection),
                'editable' => User::current()->can('edit', $collection),
                'blueprint_editable' => User::current()->can('configure fields'),
            ];
        })->values();

        return view('statamic::collections.index', [
            'collections' => $collections,
            'columns' => [
                Column::make('title')->label(__('Title')),
                Column::make('entries')->label(__('Entries')),
            ],
        ]);
    }
}
