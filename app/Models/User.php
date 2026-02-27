<?php

namespace App\Models;

use App\Traits\SerializesDatetime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens, SerializesDatetime;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_base64'
    ];
    protected $hidden = ['password'];
    public $timestamps = true;


    public static function criar(array $data): self
    {
        if (!array_key_exists('profile_photo_base64', $data) || !$data['profile_photo_base64']) {
            $data['profile_photo_base64'] = self::getRandomDefaultProfilePhotoBase64();
        }

        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
            'profile_photo_base64' => $data['profile_photo_base64']
        ]);
    }

    public static function criarAdmin(array $data): self
    {
        if (!array_key_exists('profile_photo_base64', $data) || !$data['profile_photo_base64']) {
            $data['profile_photo_base64'] = self::getRandomDefaultProfilePhotoBase64();
        }

        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'admin',
            'profile_photo_base64' => $data['profile_photo_base64']
        ]);
    }

    private static function getRandomDefaultProfilePhotoBase64(): string
    {
        $files = [
            'music_avatar_1.svg',
            'music_avatar_2.svg',
            'music_avatar_3.svg',
            'music_avatar_4.svg',
            'music_avatar_5.svg',
        ];

        $filename = $files[array_rand($files)];
        $path = public_path('images/default_profiles/' . $filename);

        if (!file_exists($path)) {
            return '';
        }

        $content = file_get_contents($path);

        return 'data:image/svg+xml;base64,' . base64_encode($content);
    }

    

    public function atualizar(array $data): self
    {
        if (array_key_exists('password', $data) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        }

        $this->update($data);

        return $this;
    }

    public function atualizarSenha(string $novaSenha): self
    {
        $this->update([
            'password' => Hash::make($novaSenha)
        ]);

        return $this;
    }

    public function isPremium(): bool
{
    $tier = $this->planTier();
    return $tier === 2 || $tier === 3;
}
    

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'user_id')
            ->where('status', 'active');
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'user_id');
    }

    public function isPro(): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->whereHas('plan', function ($query) {
                $query->where('name', 'pro');
            })
            ->exists();
    }

    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class, 'user_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class, 'user_id');
    }

       public static function login(string $email, string $password): self
    {
        $user = self::where('email', $email)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception('Credenciais inválidas');
        }

        return $user;;
    }

    public function gerarToken(): string
    {
        return $this->createToken('auth_token')->plainTextToken;
    }

    public function logout(): void
    {
        $this->tokens()->delete();
    }

    public function planTier(): ?int
    {
        $sub = $this->activeSubscription()->first();
        return $sub && $sub->plan ? (int) ($sub->plan->tier ?? null) : null;
    }

    public function canLike(): bool
    {
        $tier = $this->planTier();
        if ($tier === 2 || $tier === 3) {
            return true;
        }

        $count = $this->favorites()->count();
        return $count < 20;
    }

    public function remainingLikes(): ?int
    {
        $tier = $this->planTier();
        if ($tier === 2 || $tier === 3) {
            return null;
        }

        $count = $this->favorites()->count();
        return max(0, 20 - $count);
    }

    public function canCreatePlaylist(): bool
    {
        $tier = $this->planTier();
        if ($tier === 2 || $tier === 3) {
            return true;
        }

        return $this->playlists()->count() < 5;
    }

    public function canUseTheme(): bool
    {
        $tier = $this->planTier();
        return $tier === 2 || $tier === 3;
    }

    public function hasHistoryFeature(): bool
    {
        $tier = $this->planTier();
        return $tier === 2 || $tier === 3;
    }

    public function canAccessStats(): bool
    {
        $tier = $this->planTier();
        return $tier === 3;
    }

    public function canAddFavoriteArtist(): bool
    {
        $tier = $this->planTier();
        return $tier === 3;
    }

   public function canEditProfile(): bool
{
    $tier = $this->planTier();
    return $tier === 2 || $tier === 3;
}
}

