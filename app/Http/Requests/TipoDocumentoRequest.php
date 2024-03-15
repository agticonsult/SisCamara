<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoDocumentoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => 'required|max:200',
            'tipoDocumento' => 'required|max:200',
            'nivel' => 'required|integer',
            'id_departamento' => 'required'
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
            'nome.max' => 'Nome: Máximo 200 caracteres.',

            'tipoDocumento.required' => 'Tipo Documento obrigatório.',
            'tipoDocumento.max' => 'Tipo Documento: Máximo 200 caracteres.',

            'nivel.required' => 'O nível é obrigatório.',
            'nivel.integer' => 'O nível deve ser um número inteiro.',

            'id_departamento.required' => 'O departamento é obrigatório.'
            // 'id_departamento.integer' => 'Requer número inteiro'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $validated = $this->validated();

            if ($validated['nivel'] != count($validated['id_departamento'])) {
                $validator->errors()->add('nivel', 'A quantidade de campos de departamentos deve ser igual o valor no campo nível.');
            }

            $contagem = array_count_values($validated['id_departamento']);

            foreach ($contagem as $quantidade) {
                if ($quantidade > 1) {
                    $validator->errors()->add('id_departamento', 'Os campos de departamentos devem ser diferentes.');
                }
            }
        });
    }
}
