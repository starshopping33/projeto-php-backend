<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestCadastro extends FormRequest
{
  
    public function authorize(): bool
    {
        return true;
    }

     public function rules(): array
    {
        return [
            'nome' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => [
                'required',
                'string',
            ],
        ];
    }


}