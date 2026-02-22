<?php

namespace App\Models;

use App\Traits\SerializesDatetime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Playlist extends Model
{
    use SoftDeletes, SerializesDatetime;

    protected $table = 'playlists';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'cover',
        'is_collaborative',
        'order',
        'folder_id'
    ];

    protected $casts = [
        'is_collaborative' => 'boolean',
        'order' => 'integer'
    ];

    public $timestamps = true;

    public static function criar(array $data): self
    {
        return self::create($data);
    }

    public function atualizar(array $data): self
    {
        $this->update($data);

        return $this;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'playlist_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }
}
