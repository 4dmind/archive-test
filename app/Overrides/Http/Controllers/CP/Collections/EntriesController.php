<?php

namespace App\Overrides\Http\Controllers\CP\Collections;

use App\Support\EntriesPermissions;
use Statamic\Extensions\Pagination\LengthAwarePaginator;
use Statamic\Http\Requests\FilteredRequest;
use Statamic\Http\Resources\CP\Entries\Entries;

class EntriesController extends \Statamic\Http\Controllers\CP\Collections\EntriesController
{
    public function index(FilteredRequest $request, $collection)
    {
        $this->authorize('view', $collection);

        $query = $this->indexQuery($collection);

        $activeFilterBadges = $this->queryFilters($query, $request->filters, [
            'collection' => $collection->handle(),
            'blueprints' => $collection->entryBlueprints()->map->handle(),
        ]);

        $sortField = request('sort');
        $sortDirection = request('order', 'asc');

        if (!$sortField && !request('search')) {
            $sortField = $collection->sortField();
            $sortDirection = $collection->sortDirection();
        }

        if ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        }

        // EXTENSION STARTS HERE
        // prepare pagination params
        $currentPage = request('page', 1);
        $limit = request('perPage', 10);
        $offset = ($currentPage - 1) * $limit;

        // get filtered entries from parent tag and filter them
        $entries = EntriesPermissions::filterEntries($query->get());

        $paginated = new LengthAwarePaginator(
            $entries->slice($offset, $limit),
            $entries->count(),
            $limit
        );
        // EXTENSION ENDS

        return (new Entries($paginated))
            ->blueprint($collection->entryBlueprint())
            ->columnPreferenceKey("collections.{$collection->handle()}.columns")
            ->additional(['meta' => [
                'activeFilterBadges' => $activeFilterBadges,
            ]]);
    }
}
