<?php

namespace App\Http\Controllers\Cp;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\User;
use Statamic\Http\Controllers\CP\CpController;

class ArchiveController extends CpController
{
    /**
     * Change entry archived status
     * @param Request $request
     * @param $entryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $entryId)
    {
        $entry = Entry::find($entryId);
        $archive = $request->get('archive');

        if ($archive) {
            $entry->doArchive(['user' => User::current()]);

            $entry = $entry->fresh();
            return response()->json(['archivedAt' => $entry->value('archived_at')]);
        }

        $entry->doUnarchive(['user' => User::current()]);
        return response()->json(['archivedAt' => null]);
    }
}
