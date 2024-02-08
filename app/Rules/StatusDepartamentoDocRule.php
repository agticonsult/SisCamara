<?php

namespace App\Rules;

use App\Models\StatusDepartamentoDocumento;
use Illuminate\Contracts\Validation\Rule;

class StatusDepartamentoDocRule implements Rule
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
        $status = StatusDepartamentoDocumento::where('id', '=', $value)->where('ativo', '=', StatusDepartamentoDocumento::ATIVO)->first();
        if (!$status){
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
        return 'Status inválido.';
    }
}
