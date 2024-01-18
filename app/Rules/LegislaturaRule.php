<?php

namespace App\Rules;

use App\Models\Legislatura;
use Illuminate\Contracts\Validation\Rule;

class LegislaturaRule implements Rule
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
        $legislatura = Legislatura::where('id', '=', $value)->where('ativo', '=', Legislatura::ATIVO)->first();
        if (!$legislatura){
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
        return 'Legislatura inválida.';
    }
}
