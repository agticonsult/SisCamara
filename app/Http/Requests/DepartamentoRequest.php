<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartamentoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'descricao' => 'required|max:200',
            'id_coordenador' => 'nullable',
            'id_user' => 'nullable',
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
            'descricao.max' => 'Descricao: Máximo 200 caracteres.',

            // 'id_coordenador.required' => 'Seleção COORDENADOR obrigatório',
            // 'id_coordenador.integer' => 'Requer número inteiro',

            // 'id_user.required' => 'Seleção USUÁRIO obrigatório',
            // 'id_user.integer' => 'Requer número inteiro'
        ];
    }
}
