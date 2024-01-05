<?php

namespace App\Rules;

use App\Models\TipoAto;
use Illuminate\Contracts\Validation\Rule;

class TipoAtoRule implements Rule
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
        $tipo_ato = TipoAto::where('id', '=', $value)->where('ativo', '=', TipoAto::ATIVO)->first();
        if (!$tipo_ato){
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
        return 'Tipo de ato inválido.';
    }
}
