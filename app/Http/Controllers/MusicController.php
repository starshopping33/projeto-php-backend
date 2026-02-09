<?php

namespace App\Http\Controllers;

use App\Services\LastFmService;

class MusicController extends Controller
{
    private LastFmService $lastFm;

    public function __construct(LastFmService $lastFm)
    {
        $this->lastFm = $lastFm;
    }

    public function topTracks()
    {
        $tracks = $this->lastFm->topTracks(20);

        return response()->json($tracks);
    }

    public function topTracksByTag($tag)
    {
        $tracks = $this->lastFm->topTracksByTag($tag, 20);

        return response()->json($tracks);
    }
}
