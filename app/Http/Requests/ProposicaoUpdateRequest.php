<?php

namespace App\Http\Requests;

use App\Rules\LocalizacaoProposicaoRule;
use App\Rules\ModeloProposicaoRule;
use App\Rules\StatusProposicaoRule;
use Illuminate\Foundation\Http\FormRequest;

class ProposicaoUpdateRequest extends FormRequest
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
            'id_localizacao' => ['required', 'integer', new LocalizacaoProposicaoRule],
            'id_status' => ['required', 'integer', new StatusProposicaoRule],
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
            'id_localizacao.required' => 'Localização obrigatório.',
            'id_localizacao.integer' => 'Localização obrigatório inválido.',
            'id_status.required' => 'Status obrigatório.',
            'id_status.integer' => 'Status obrigatório inválido.',
        ];
    }
}
