<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerfilStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'descricao' => 'required|max:255',
            'id_tipo_perfil' => ['required', 'max:255']
        ];
    }

     /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'descricao.required' => 'Descrição obrigatório.',
            'descricao.max' => 'Tamanho máximo da descrição 255 caracteres.',

            'id_tipo_perfil.required' => 'Tipo de Perfil obrigatório.',
            'id_tipo_perfil.max' => 'Tipo de Perfil máximo da descrição 255 caracteres.'
        ];
    }
}
