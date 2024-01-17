<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentePoliticoVincularRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_pleito_eleitoral' =>  'required|integer',
            'id_usuario' =>  'required',
            'id_cargo_eletivo' => 'required|integer',
            'dataInicioMandato' => 'required|date',
            'dataFimMandato' => 'required|after:dataInicioMandato',
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
            'id_pleito_eleitoral.required' => 'Seleção de Pleito Eleitoral obrigatório.',
            'id_pleito_eleitoral.integer' => 'Seleção de Pleito Eleitoral é um número inteiro.',

            'id_usuario.required' => 'Seleção de Usuário obrigatório.',

            'id_cargo_eletivo.required' => 'Seleção de Cargo Eletivo obrigatório.',
            'id_cargo_eletivo.integer' => 'Seleção de Cargo Eletivo é um número inteiro.',

            'dataInicioMandato.required' => 'Data de Início de Mandato obrigatório',
            'dataInicioMandato.date' => 'Data de Início de Mandato inválida',
            'dataFimMandato.required' => 'Data de Fim de Mandato obrigatório',
            'dataFimMandato.date' => 'Data de Fim de Mandato inválida',
            'dataFimMandato.after' => 'Data de Fim de Mandato tem que posterior da Data Início',
        ];
    }
}
