<?php

namespace App\Http\Requests;

use App\Rules\CpfRule;
use Illuminate\Foundation\Http\FormRequest;

class HomeUpdateRequest extends FormRequest
{

    protected function prepareForValidation(): void
    {
        // Obtenha o valor do campo 'cpf' do request
        $cpf = $this->input('cpf');

        // Remova caracteres não numéricos do CPF
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Atualize o valor do campo 'cpf' no request
        $this->merge([
            'cpf' => $cpf,
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
            'cpf' => ['sometimes', 'required', new CpfRule, 'unique:users,cpf,' . $this->id],
            'email' => 'required|email|exists:users,email',

            //Pessoa
            'nome' => 'required|max:255',
            'apelidoFantasia' => 'max:255',
            'dt_nascimento_fundacao' => 'required|date',
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

            'cpf.required' => 'CPF obrigatório.',
            'cpf.unique' => 'CPF já cadastrado no sistema',

            'email.required' => 'E-mail obrigatório.',
            'email.email' => 'E-mail inválido',
            'email.exists' => 'E-mail já cadastrado no sitema',

            'dt_nascimento_fundacao.required' => 'Data nascimento obrigatório',
            'dt_nascimento_fundacao.date' => 'Data nascimento inválida',

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
