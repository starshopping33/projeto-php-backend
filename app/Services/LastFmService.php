<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LastFmService
{
    private string $baseUrl = 'https://ws.audioscrobbler.com/2.0/';

    private function request(array $params): array
    {
        $response = Http::get($this->baseUrl, array_merge([
            'api_key' => config('services.lastfm.key'),
            'format'  => 'json',
        ], $params));

        if ($response->failed()) {
            throw new \Exception('Erro ao acessar a API do Last.fm');
        }

        return $response->json();
    }

    public function topTracks(int $limit = 20): array
    {
        return $this->request([
            'method' => 'chart.gettoptracks',
            'limit'  => $limit,
        ])['tracks']['track'] ?? [];
    }

    public function topTracksByTag(string $tag, int $limit = 20): array
    {
        return $this->request([
            'method' => 'tag.gettoptracks',
            'tag'    => $tag,
            'limit'  => $limit,
        ])['tracks']['track'] ?? [];
    }
}
