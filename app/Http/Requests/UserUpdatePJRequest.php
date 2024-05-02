<?php

namespace App\Http\Requests;

use App\Rules\CnpjRule;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdatePJRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Obtenha o valor do campo 'cnpj' do request
        $cnpj = $this->input('cnpj');

        // Remova caracteres não numéricos do cnpj
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Atualize o valor do campo 'cnpj' no request
        $this->merge([
            'cnpj' => $cnpj,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => 'required|max:255',
            'cnpj' => ['sometimes', 'required', new CnpjRule, 'unique:users,cnpj,' . $this->id],
            'email' => 'required|email|exists:users,email',
            'id_perfil' => 'required',
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
            'nome.max' => 'Nome: Máximo 255 caracteres.',

            'cnpj.required' => 'CNPJ obrigatório.',
            'cnpj.unique' => 'CNPJ já cadastrado no sistema',

            'email.required' => 'E-mail obrigatório.',
            'email.email' => 'E-mail inválido',
            'email.exists' => 'E-mail já cadastrado no sitema',

            'id_perfil.required' => 'Seleção de perfil obrigatório.'
        ];
    }
}
