<?php

namespace App\Rules;

use App\Models\OrgaoAto;
use Illuminate\Contracts\Validation\Rule;

class OrgaoRule implements Rule
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
        $orgao = OrgaoAto::where('id', '=', $value)->where('ativo', '=', OrgaoAto::ATIVO)->first();
        if (!$orgao){
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
        return 'Órgão que editou o ato inválido.';
    }
}
