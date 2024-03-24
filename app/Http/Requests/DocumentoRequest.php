<?php

namespace App\Http\Requests;

use App\Rules\TipoDocumentoule;
use Illuminate\Foundation\Http\FormRequest;

class DocumentoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'titulo' => 'required|max:200',
            'conteudo' => 'required',
            // 'id_status' => 'required|integer',
            'id_tipo_documento' => ['required', new TipoDocumentoule],
            'id_tipo_workflow' => 'required',
            'id_departamento' => 'required_if:id_tipo_documento,2'
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
            'titulo.required' => 'Descrição obrigatório.',
            'titulo.max' => 'Titulo: Máximo 200 caracteres.',

            'conteudo.required' => 'Conteúdo do documento obrigatório.',

            // 'id_status.required' => 'Seleção STATUS obrigatório',
            // 'id_status.integer' => 'Requer número inteiro',

            'id_tipo_documento.required' => 'Seleção TIPO DE DOCUMENTO obrigatório',

            'id_departamento.required' => 'Seleção DEPARTAMENTO obrigatório',

            'id_tipo_workflow.required' => 'O workflow de tramitação é obrigatório'
        ];
    }
}
