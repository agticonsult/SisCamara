<?php

namespace App\Rules;

use App\Models\Departamento;
use Illuminate\Contracts\Validation\Rule;

class DepartamentoRule implements Rule
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
        $departamento = Departamento::where('id', '=', $value)->where('ativo', '=', Departamento::ATIVO)->first();
        if (!$departamento) {
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
        return 'Departamento inválido.';
    }
}
