<?php

namespace App\Http\Requests;

use App\Rules\ClassificacaoAtoRule;
use App\Rules\FormaPublicacaoRule;
use App\Rules\GrupoRule;
use App\Rules\OrgaoRule;
use App\Rules\TipoAtoRule;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AtoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $temPermissao = Auth::user()->temPermissao('Ato', 'Cadastro');

        return $temPermissao;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Texto
            'titulo' => 'required',
            'subtitulo' => 'nullable',
            'corpo_texto' => 'required',

            // Dados Gerais
            'id_classificacao' => ['required', 'integer', 'exists:classificacao_atos,id', new ClassificacaoAtoRule],
            'ano' => 'required|integer',
            'numero' => 'required',
            // 'id_grupo' => ['required', 'integer', 'exists:grupos,id', new GrupoRule],
            'id_tipo_ato' => ['required', 'integer', 'exists:tipo_atos,id', new TipoAtoRule],
            'id_assunto' => 'required|integer|exists:assunto_atos,id',
            'id_orgao' => ['required', 'integer', 'exists:orgao_atos,id', new OrgaoRule],
            'id_forma_publicacao' => ['nullable', 'integer', 'exists:forma_publicacao_atos,id', new FormaPublicacaoRule],
            'data_publicacao' => 'nullable|date',

            // Anexos
            'arquivo[]' => 'nullable',
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
            'titulo.required' => 'Título obrigatório',
            'subtitulo.nullable' => 'Subtítulo obrigatório',
            'corpo_texto.required' => 'Corpo do Texto obrigatório',

            'id_classificacao.exists' => 'Classificação inválida.',
            'id_classificacao.required' => 'Classificação do ato obrigatório',
            'id_classificacao.integer' => 'Classificação do ato é um número inteiro',

            // 'id_grupo.required' => 'Grupo do ato obrigatório',
            // 'id_grupo.integer' => 'Grupo do ato é um número inteiro',
            // 'id_grupo.exists' => 'Grupo inválido.',

            'id_tipo_ato.required' => 'Tipo do ato obrigatório',
            'id_tipo_ato.integer' => 'Tipo do ato é um número inteiro',
            'id_tipo_ato.exists' => 'Tipo inválido.',

            'id_assunto.required' => 'Assunto do ato obrigatório',
            'id_assunto.integer' => 'Assunto do ato é um número inteiro',
            'id_assunto.exists' => 'Assunto inválido.',

            'id_orgao.required' => 'Órgão do ato obrigatório',
            'id_orgao.integer' => 'Órgão do ato é um número inteiro',
            'id_orgao.exists' => 'Órgão inválido.',

            'id_forma_publicacao.nullable' => 'Forma de publicação não nulo',
            'id_forma_publicacao.integer' => 'Forma de publicação do ato é um número inteiro',
            'id_forma_publicacao.exists' => 'Forma de publicação inválido.',

            'data_publicacao.nullable' => 'Data de publicação não nulo',
            'data_publicacao.date' => 'Data de publicação inválida',

            'ano.required' => 'Ano obrigatório',
            'ano.integer' => 'Ano é um número inteiro',
            'numero.required' => 'Número obrigatório',
        ];
    }
}
