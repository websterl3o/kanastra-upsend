<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCollectionListRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => 'required|file|mimes:csv|max:204800',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'O arquivo é obrigatório.',
            'file.file' => 'O arquivo deve ser válido.',
            'file.mimes' => 'O arquivo deve ser do tipo CSV.',
            'file.max' => 'O arquivo não pode ser maior que 200 megabytes.',
        ];
    }
}
