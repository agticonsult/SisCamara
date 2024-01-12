<?php

namespace App\Http\Requests;

use App\Rules\CpfRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $temPermissao = Auth::user()->temPermissao('User', 'Cadastro');

        return $temPermissao;
    }

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
            'nome' => 'required|max:255',
            'cpf' => ['required', 'unique:users,cpf', new CpfRule],
            'email' => 'required|email|unique:users,email',
            'dt_nascimento_fundacao' => 'required|max:10',
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

            'cpf.required' => 'CPF obrigatório.',
            'cpf.unique' => 'CPF já cadastrado no sistema',

            'email.required' => 'E-mail obrigatório.',
            'email.email' => 'E-mail inválido',
            'email.max' => 'E-mail: Máximo 255 caracteres',
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

            'id_perfil.required' => 'Seleção de perfil obrigatório.'
        ];
    }
}
