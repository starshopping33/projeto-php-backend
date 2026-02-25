<?php

namespace App\Models;

use App\Traits\SerializesDatetime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use SoftDeletes, SerializesDatetime;

    protected $table = 'favorites';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'music_id',
        'music_name',
        'artist_name',
        'music_url',
        'image',
        'playlist_id'
    ];

    public $timestamps = true;

    public static function criar(array $data): self
    {
        return self::create($data);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class, 'playlist_id');
    }
}
