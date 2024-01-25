<?php

namespace App\Http\Requests;

use App\Rules\ModeloProposicaoRule;
use Illuminate\Foundation\Http\FormRequest;

class ProposicaoStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'titulo' => 'required',
            'id_modelo' => ['required', 'integer', new ModeloProposicaoRule],
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
            'titulo.required' => 'Título obrigatório.',
            'id_modelo.required' => 'Modelo obrigatório.',
            'id_modelo.integer' => 'Modelo obrigatório inválido.',
            'assunto.required' => 'Assunto obrigatório.',
            'conteudo.required' => 'Conteúdo obrigatório.',
        ];
    }
}
