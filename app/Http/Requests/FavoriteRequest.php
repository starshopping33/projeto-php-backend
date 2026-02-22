<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'music_id' => ['required', 'string', 'max:255'],
            'playlist_id' => ['nullable', 'integer', 'exists:playlists,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'music_id.required' => 'O identificador da música é obrigatório.',
            'music_id.string' => 'O identificador da música deve ser um texto.',
            'music_id.max' => 'O identificador da música não pode exceder 255 caracteres.',
            'playlist_id.integer' => 'O identificador da playlist deve ser um número inteiro.',
            'playlist_id.exists' => 'A playlist selecionada não existe.',
        ];
    }
}
