<?php

namespace App\Rules;

use App\Models\ClassificacaoAto;
use Illuminate\Contracts\Validation\Rule;

class ClassificacaoAtoRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $classificacao = ClassificacaoAto::where('id', '=', $value)->where('ativo', '=', ClassificacaoAto::ATIVO)->first();
        if (!$classificacao){
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Classificação inválida';
    }
}
