<?php

namespace App\Http\Requests;

use App\Rules\CnpjRule;
use Illuminate\Foundation\Http\FormRequest;

class HomeUpdatePJRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Obtenha o valor do campo 'cpf' do request
        $cnpj = $this->input('cnpj');

        // Remova caracteres não numéricos do CPF
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
            //Usuário
            'cnpj' => ['sometimes', 'required', new CnpjRule, 'unique:users,cnpj,' . $this->id],
            'email' => 'required|email|exists:users,email',

            //Pessoa
            'nome' => 'required|max:255',
            'apelidoFantasia' => 'max:255',
            'cep' => 'max:255',
            'endereco' => 'max:255',
            'bairro' => 'max:255',
            'numero' => 'max:255',
            'complemento' => 'max:255',
            'ponto_referencia' => 'max:255',

            'telefone_celular' => 'max:15',
            'telefone_celular2' => 'max:15',

            'password' => 'nullable|min:6|max:35',
            'confirmacao' => 'nullable|min:6|max:35'
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

            'apelidoFantasia.max' => 'Apelido Fantasia: Máximo 255 caracteres.',
            'cep.max' => 'CEP: Máximo 255 caracteres.',
            'endereco.max' => 'Endereço: Máximo 255 caracteres.',
            'bairro.max' => 'Bairro: Máximo 255 caracteres.',
            'numero.max' => 'Número: Máximo 255 caracteres.',
            'complemento.max' => 'Complemento: Máximo 255 caracteres.',
            'ponto_referencia.max' => 'Ponto de Referência: Máximo 255 caracteres.',
            'telefone_celular.max' => 'Telefone Celular: Máximo 255 caracteres.',
            'telefone_celular2.max' => 'Telefone Celular: Máximo 255 caracteres.',

            'password.min' => 'Senha: Minímo 6 caracteres',
            'password.max' => 'Senha: Máximo 35 caracteres',

            'confirmacao.min' => 'Confirmação: Minímo 6 caracteres',
            'confirmacao.max' => 'Confirmação: Máximo 35 caracteres',
        ];
    }
}
