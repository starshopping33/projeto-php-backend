<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s\'-]+$/u'],
            'parent_id' => ['nullable', 'integer', 'exists:folders,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da pasta é obrigatório.',
            'name.string' => 'O nome da pasta deve ser um texto.',
            'name.min' => 'O nome da pasta deve ter no mínimo 3 caracteres.',
            'name.max' => 'O nome da pasta não pode exceder 100 caracteres.',
            'name.regex' => 'O nome da pasta pode conter apenas letras, espaços, hífens e apóstrofos.',
            'parent_id.integer' => 'O identificador do pai deve ser um número inteiro.',
            'parent_id.exists' => 'A pasta pai selecionada não existe.',
        ];
    }
}
