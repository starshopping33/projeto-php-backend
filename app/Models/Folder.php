<?php

namespace App\Models;

use App\Traits\SerializesDatetime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use SoftDeletes, SerializesDatetime;

    protected $table = 'folders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'name',
        'parent_id'
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

    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class, 'folder_id');
    }
}
