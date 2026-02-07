<?php

namespace App\Models;

use App\Traits\SerializesDatetime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Login extends Model
{
    use SoftDeletes, HasApiTokens, SerializesDatetime;

    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];
    protected $hidden = ['password'];

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
