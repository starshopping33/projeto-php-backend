<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class MusicController extends Controller
{
    public function topTracks()
    {
        $response = Http::get('https://ws.audioscrobbler.com/2.0/', [
            'method'  => 'chart.gettoptracks',
            'api_key' => config('services.lastfm.key'),
            'format'  => 'json',
            'limit'   => 20
        ]);

        if ($response->failed()) {
            abort(500, 'Erro ao buscar músicas');
        }

        $tracks = $response->json()['tracks']['track'];

        return view('musicas-populares', compact('tracks'));
    }
}