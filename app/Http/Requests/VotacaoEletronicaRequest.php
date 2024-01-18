<?php

namespace App\Http\Requests;

use App\Rules\LegislaturaRule;
use App\Rules\ProposicaoRule;
use App\Rules\TipoVotacaoRule;
use Illuminate\Foundation\Http\FormRequest;

class VotacaoEletronicaRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data' => 'required|date',
            'id_tipo_votacao' => ['required', 'integer', new TipoVotacaoRule],
            'id_proposicao' => ['required', 'integer', new ProposicaoRule],
            'id_legislatura' => ['required', 'integer', new LegislaturaRule]
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
            'data.required' => 'Data obrigatório.',
            'data.date' => 'Data inválida',

            'id_tipo_votacao.required' => 'Tipo de votação obrigatório.',
            'id_tipo_votacao.integer' => 'Tipo de votação inválida.',

            'id_proposicao.required' => 'Proposição obrigatório.',
            'id_proposicao.integer' => 'Proposição inválida.',

            'id_legislatura.required' => 'Legislatura obrigatório.',
            'id_legislatura.integer' => 'Legislatura inválida.'
        ];
    }
}
