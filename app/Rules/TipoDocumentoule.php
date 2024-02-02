<?php

namespace App\Rules;

use App\Models\TipoDocumento;
use Illuminate\Contracts\Validation\Rule;

class TipoDocumentoule implements Rule
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
        $tipoDocumento = TipoDocumento::where('id', '=', $value)->where('ativo', '=', TipoDocumento::ATIVO)->first();
        if (!$tipoDocumento){
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
        return 'Tipo documento inválido.';
    }
}
