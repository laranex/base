<?php

namespace laranex\LaravelMyanmarNRC\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class MyanmarNRC implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {

    }
}
