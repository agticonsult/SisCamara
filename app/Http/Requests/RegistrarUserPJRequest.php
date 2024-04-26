<?php

namespace App\Http\Requests;

use App\Rules\CnpjRule;
use Illuminate\Foundation\Http\FormRequest;

class RegistrarUserPJRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => 'required|max:255',
            'cnpj' => ['required', 'unique:users,cpf', new CnpjRule],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:35',
            'confirmacao' => 'required|min:6|max:35',
            'telefone_celular' => 'max:15',
            'telefone_celular2' => 'max:15'
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
            'email.max' => 'E-mail: Máximo 255 caracteres',
            'email.unique' => 'E-mail já cadastrado no sitema',

            'telefone_celular.max' => 'Telefone/Celular: Máximo 11 caracteres',
            'telefone_celular2.max' => 'Telefone/Celular recado: Máximo 11 caracteres',

            'password.required' => 'Senha obrigatória.',
            'password.min' => 'Senha: Minímo 6 caracteres',
            'password.max' => 'Senha: Máximo 35 caracteres',

            'confirmacao.required' => 'Confirmação obrigatória',
            'confirmacao.min' => 'Confirmação: Minímo 6 caracteres',
            'confirmacao.max' => 'Confirmação: Máximo 35 caracteres',
        ];
    }
}
