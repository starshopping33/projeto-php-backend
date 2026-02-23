<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaylistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s\'-]+$/u'],
            'description' => ['nullable', 'string', 'max:255'],
            'cover' => ['nullable', 'string', 'max:255'],
            'is_collaborative' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da playlist é obrigatório.',
            'name.string' => 'O nome da playlist deve ser um texto.',
            'name.min' => 'O nome da playlist deve ter no mínimo 3 caracteres.',
            'name.max' => 'O nome da playlist não pode exceder 100 caracteres.',
            'name.regex' => 'O nome da playlist pode conter apenas letras, espaços, hífens e apóstrofos.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição não pode exceder 255 caracteres.',
            'cover.string' => 'A referência da capa deve ser um texto.',
            'cover.max' => 'A referência da capa não pode exceder 255 caracteres.',
            'is_collaborative.boolean' => 'O campo de colaboração deve ser booleano.',
            'order.integer' => 'A ordem deve ser um número inteiro.',
            'order.min' => 'A ordem não pode ser negativa.',
            'folder_id.integer' => 'O identificador da pasta deve ser um número inteiro.',
            'folder_id.exists' => 'A pasta selecionada não existe.',
        ];
    }
}
