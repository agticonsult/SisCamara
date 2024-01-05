<?php

namespace App\Rules;

use App\Models\Grupo;
use Illuminate\Contracts\Validation\Rule;

class FormaPublicacaoRule implements Rule
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
        if ($value != null){
            $forma_publicacao = Grupo::where('id', '=', $value)->where('ativo', '=', Grupo::ATIVO)->first();
            if (!$forma_publicacao){
                return false;
            }
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
        return 'Forma de publicação do ato inválido.';
    }
}
