<?php

namespace App\Rules;

use App\Models\TipoVotacao;
use Illuminate\Contracts\Validation\Rule;

class TipoVotacaoRule implements Rule
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
        $tipo_votacao = TipoVotacao::where('id', '=', $value)->where('ativo', '=', TipoVotacao::ATIVO)->first();
        if (!$tipo_votacao){
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
        return 'Tipo de votação inválida.';
    }
}
