<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhotoUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_photo_base64' => [
                'required',
                'string',
                'regex:/^data:image\/(jpeg|png|jpg);base64,/'
            ]
        ];
    }

    public function messages()
    {
        return [
            'profile_photo_base64.required' => 'Foto é obrigatória',
            'profile_photo_base64.regex' => 'A foto deve estar em formato base64 válido'
        ];
    }
}
