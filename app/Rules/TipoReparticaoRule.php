<?php

namespace App\Rules;

use App\Models\TipoReparticao;
use Illuminate\Contracts\Validation\Rule;

class TipoReparticaoRule implements Rule
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
        $tipo_reparticao = TipoReparticao::where('id', '=', $value)->where('ativo', '=', TipoReparticao::ATIVO)->first();
        if (!$tipo_reparticao){
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
        return 'Tipo de repartição inválida';
    }
}
