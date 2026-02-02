<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class planos extends Model
{
    protected $fillable = [
        'nome',
        'preco',
        'duracao_em_dias',
        'descricao'

    ];

    public function assinaturas()

    {
        return $this->hasMany(assinaturas::class);
        
    }
}
