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
        if ($this->isMethod('delete')) {
            return [
                'music_id' => 'required|string'
            ];
        }

        if ($this->isMethod('post')) {
            return [
                'music_id'   => 'required|string',
                'music_name' => 'required|string',
                'artist_name'=> 'required|string',
            ];
        }

        return [];
    }

    protected function prepareForValidation()
    {
       
        if ($this->isMethod('delete')) {
            $this->merge([
                'music_id' => $this->route('music_id')
            ]);
        }
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
