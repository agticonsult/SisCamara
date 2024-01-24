<?php

namespace App\Http\Requests;

use App\Rules\LegislaturaRule;
use Illuminate\Foundation\Http\FormRequest;

class PleitoEleitoralRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ano_pleito' => 'required',
            'id_legislatura' => ['required', 'integer', new LegislaturaRule],
            'pleitoEspecial' => 'nullable',
            'dataPrimeiroTurno' => 'required|date',
            'dataSegundoTurno' => 'required|date|after:dataPrimeiroTurno',
            'id_cargo_eletivo' => 'required'
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
            'ano_pleito.required' => 'Ano Pleito obrigatório.',

            'id_legislatura.required' => 'Legislatura obrigatório',
            'id_legislatura.integer' => 'Legislatura número inteiro',

            'dataPrimeiroTurno.required' => 'Data primeiro turno obrigatório',
            'dataPrimeiroTurno.date' => 'Data primeiro inválido',

            'dataSegundoTurno.required' => 'Data segundo turno obrigatório',
            'dataSegundoTurno.date' => 'Data segundo inválido',
            'dataSegundoTurno.after' => 'Data segundo não pode ser menor que a data do primeiro turno',

            'id_cargo_eletivo.required' => 'Cargo eletivo obrigatório'
        ];
    }
}
