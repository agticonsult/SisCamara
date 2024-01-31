<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoDocumentoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => 'required|max:200',
            'tipoDocumento' => 'required|max:200',
            'id_departamento' => 'required|integer'
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
            'nome.required' => 'Nome obrigatório.',
            'nome.max' => 'Nome: Máximo 200 caracteres.',

            'tipoDocumento.required' => 'Tipo Documento obrigatório.',
            'tipoDocumento.max' => 'Tipo Documento: Máximo 200 caracteres.',

            'id_departamento.required' => 'Seleção Departamento obrigatório',
            'id_departamento.integer' => 'Requer número inteiro'
        ];
    }
}
