<?php

namespace App\Http\Controllers;

use App\Models\ReleaseNote;
use Illuminate\Http\Request;

class ReleaseNoteController extends Controller
{
    /**
     * Returns the last release note
     */
    public function currentVersion()
    {
        $releaseNote = ReleaseNote::where('release_note', '>', 0)->orderBy('release_note', 'desc')->first();
        returnResponse(['version' => $releaseNote ? $releaseNote->version : null]);
    }
}
