<?php

namespace App\Http\Requests;

use App\Rules\CnpjRule;
use Illuminate\Foundation\Http\FormRequest;

class UserStorePJRequest extends FormRequest
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
            'cnpj' => ['required', 'unique:users,cnpj', new CnpjRule],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:35',
            'confirmacao' => 'required|min:6|max:35',
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
            // 'email.max' => 'E-mail: Máximo 255 caracteres',
            'email.unique' => 'E-mail já cadastrado no sitema',

            'telefone_celular.max' => 'Telefone/Celular: Máximo 11 caracteres',
            'telefone_celular2.max' => 'Telefone/Celular recado: Máximo 11 caracteres',

            'password.required' => 'Senha obrigatória.',
            'password.min' => 'Senha: Minímo 6 caracteres',
            'password.max' => 'Senha: Máximo 35 caracteres',

            'confirmacao.required' => 'Confirmação obrigatória',
            'confirmacao.min' => 'Confirmação: Minímo 6 caracteres',
            'confirmacao.max' => 'Confirmação: Máximo 35 caracteres',

            'id_perfil.required' => 'Seleção de perfil obrigatório.'
        ];
    }
}
