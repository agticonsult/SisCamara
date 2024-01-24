<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModeloProposicaoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'assunto' => 'required',
            'conteudo' => 'required',
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
            'assunto.required' => 'Assunto obrigatório.',

            'conteudo.required' => 'Conteúdo obrigatório.'
        ];
    }
}
