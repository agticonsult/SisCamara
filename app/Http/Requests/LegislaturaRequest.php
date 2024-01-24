<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LegislaturaRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'inicio_mandato' => 'required|integer',
            'fim_mandato' => 'required|integer|after:inicio_mandato',
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
            'inicio_mandato.required' => 'Início mandato obrigatório.',
            'inicio_mandato.integer' => 'Início mandato: Requer número inteiro',

            'fim_mandato.required' => 'Fim mandato obrigatório.',
            'fim_mandato.integer' => 'Fim mandato: Requer número inteiro',
            'fim_mandato.after' => 'Fim mandato não pode ser maior ou igual o mandato início',
        ];
    }
}
