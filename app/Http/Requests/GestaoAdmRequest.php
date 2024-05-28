<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GestaoAdmRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_departamento' => ['required', 'integer'],
            'aprovacaoCadastro' => 'required',
            'recebimentoDocumento' => 'required'
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
            'id_departamento.required' => 'Departamento obrigatório',
            'id_departamento.integer' => 'Departamento número inteiro',

            'aprovacaoCadastro' => 'Aprovação de cadastro obrigatório',
            'recebimentoDocumento' => 'Recebimento de documento obrigatório',
        ];
    }
}
