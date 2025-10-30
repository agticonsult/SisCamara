<?php

namespace App\Http\Requests;

use App\Rules\CpfRule;
use App\Rules\PleitoCargoRule;
use Illuminate\Foundation\Http\FormRequest;

class AgentePoliticoStoreRequest extends FormRequest
{

    protected function prepareForValidation(): void
    {
        $cpf = $this->input('cpf');
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

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
            'id_pleito_eleitoral' =>  'required|integer',
            'id_cargo_eletivo' => 'required|integer',
            'dataInicioMandato' => 'required|date',
            'dataFimMandato' => 'required|after:dataInicioMandato',

            'nome' => 'required|max:255',
            'cpf' => ['required', 'unique:users,cpf', new CpfRule],
            'email' => 'required|email|unique:users,email',
            'email' => 'required|email',
            'dt_nascimento_fundacao' => 'required|date',
            'password' => 'required|min:6|max:35',
            'confirmacao' => 'required|min:6|max:35',
            'telefone_celular' => 'max:15',
            'telefone_celular2' => 'max:15',

            'apelidoFantasia' => 'max:255',
            'cep' => 'max:255',
            'endereco' => 'max:255',
            'bairro' => 'max:255',
            'numero' => 'max:255',
            'complemento' => 'max:255',
            'ponto_referencia' => 'max:255',

            'password' => ['required', 'min:6', 'max:35'],
            'confirmacao' => ['required', 'min:6', 'max:35', 'same:password'],

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
            'email.unique' => 'E-mail já cadastrado no sitema',

            'dt_nascimento_fundacao.required' => 'Data nascimento obrigatório',
            'dt_nascimento_fundacao.date' => 'Data nascimento inválida',

            'telefone_celular.max' => 'Telefone/Celular: Máximo 11 caracteres',
            'telefone_celular2.max' => 'Telefone/Celular recado: Máximo 11 caracteres',

            'password.required' => 'Senha obrigatória.',
            'password.min' => 'Senha: Minímo 6 caracteres',
            'password.max' => 'Senha: Máximo 35 caracteres',

            'confirmacao.required' => 'Confirmação obrigatória',
            'confirmacao.min' => 'Confirmação: Minímo 6 caracteres',
            'confirmacao.max' => 'Confirmação: Máximo 35 caracteres',
            'confirmacao.same' => 'Confirmação de senha não coincide.',

            'id_pleito_eleitoral.required' => 'Seleção de Pleito Eleitoral obrigatório.',
            'id_pleito_eleitoral.integer' => 'Seleção de Pleito Eleitoral é um número inteiro.',

            'id_cargo_eletivo.required' => 'Seleção de Cargo Eletivo obrigatório.',
            'id_cargo_eletivo.integer' => 'Seleção de Cargo Eletivo é um número inteiro.',

            'dataInicioMandato.required' => 'Data de Início de Mandato obrigatório',
            'dataInicioMandato.date' => 'Data de Início de Mandato inválida',
            'dataFimMandato.required' => 'Data de Fim de Mandato obrigatório',
            'dataFimMandato.date' => 'Data de Fim de Mandato inválida',
            'dataFimMandato.after' => 'Data de Fim de Mandato tem que posterior da Data Início',

            'apelidoFantasia.max' => 'Apelido Fantasia: Máximo 255 caracteres.',
            'cep.max' => 'CEP: Máximo 255 caracteres.',
            'endereco.max' => 'Endereço: Máximo 255 caracteres.',
            'bairro.max' => 'Bairro: Máximo 255 caracteres.',
            'numero.max' => 'Número: Máximo 255 caracteres.',
            'complemento.max' => 'Complemento: Máximo 255 caracteres.',
            'ponto_referencia.max' => 'Ponto de Referência: Máximo 255 caracteres.',
        ];
    }
}
