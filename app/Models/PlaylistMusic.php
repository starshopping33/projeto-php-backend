<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaylistMusic extends Model
{
    protected $table = 'playlist_musics'; 

    protected $fillable = [
        'playlist_id',
        'music_id',
        'music_name',
        'artist_name',
        'cover_url'
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }
}