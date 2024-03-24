<?php

namespace App\Http\Requests;

use App\Rules\StatusDocRule;
use Illuminate\Foundation\Http\FormRequest;

class StatusDocRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
    */
    public function rules()
    {
        return [
            'id_status' => ['required', 'integer', new StatusDocRule],
            'parecer' => 'required'
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
            'parecer.required' => 'Parecer obrigatório.',
            'id_status.required' => 'Status obrigatório.',
            'id_status.integer' => 'Status inválido.'
        ];
    }
}
