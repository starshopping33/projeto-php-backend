<?php

namespace App\Models;

use App\Traits\SerializesDatetime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    use SoftDeletes, HasApiTokens, SerializesDatetime;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];
    protected $hidden = ['password'];
    public $timestamps = true;


    public static function criar(array $data): self
    {
        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user'
        ]);
    }

    public static function criarAdmin(array $data): self
    {
        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'admin'
        ]);
    }

    public function atualizar(array $data): self
    {
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'user_id')
            ->where('status', 'active');
    }

       public static function login(string $email, string $password): self
    {
        $user = self::where('email', $email)
            ->whereNull('deleted_at')
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
}
