<?php

namespace App\Rules;

use App\Models\Proposicao;
use Illuminate\Contracts\Validation\Rule;

class ProposicaoRule implements Rule
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
        $proposicao = Proposicao::where('id', '=', $value)->where('ativo', '=', Proposicao::ATIVO)->first();
        if (!$proposicao){
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
        return 'Proposição inválida.';
    }
}
