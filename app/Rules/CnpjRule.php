<?php

namespace App\Rules;

use App\Services\ValidaCNPJService;
use Illuminate\Contracts\Validation\Rule;

class CnpjRule implements Rule
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
        $validator = new ValidaCNPJService($value);
        return $validator->valida($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'CNPJ Inválido.';
    }
}
