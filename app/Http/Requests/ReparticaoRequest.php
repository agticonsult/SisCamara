<?php

namespace App\Http\Requests;

use App\Rules\TipoReparticaoRule;
use Illuminate\Foundation\Http\FormRequest;

class ReparticaoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
    */
    public function rules()
    {
        return [
            'descricao' => 'required|max:255',
            'id_tipo_reparticao' => ['required', 'integer', new TipoReparticaoRule]
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
            'descricao.required' => 'Descricao obrigatório.',
            'descricao.max' => 'Descricao: Máximo 255 caracteres.',

            'id_tipo_reparticao.required' => 'Repartição obrigatório.',
            'id_tipo_reparticao.integer' => 'Repartição inválida.'
        ];
    }
}
