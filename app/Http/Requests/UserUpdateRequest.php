<?php

namespace App\Http\Requests;

use App\Rules\CpfRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserUpdateRequest extends FormRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  *
    //  * @return bool
    //  */
    // public function authorize()
    // {
    //     $temPermissao = Auth::user()->temPermissao('User', 'Alteração');

    //     return $temPermissao;
    // }

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
            'cpf' => ['sometimes', 'required', new CpfRule, 'unique:users,cpf,' . $this->id],
            'email' => 'required|email|exists:users,email',
            'dt_nascimento_fundacao' => 'required|max:10',
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
            'email.exists' => 'E-mail já cadastrado no sitema',

            'dt_nascimento_fundacao.required' => 'Data nascimento obrigatório',
            'dt_nascimento_fundacao.date' => 'Data nascimento inválida',

            'id_perfil.required' => 'Seleção de perfil obrigatório.'
        ];
    }
}
