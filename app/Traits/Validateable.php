<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait Validateable
{
    /**
     * @param array $data
     * @param string|null $functionName
     * @throws ValidationException
     */
    public function validate(array $data, string $functionName = null)
    {
        $rules = $this->rules[$functionName] ?? $this->rules ?? [];

        if ($functionName === 'update' && isset($this->uniqueRules)) {
            collect($this->uniqueRules)->map(function ($key) use (&$rules) {
                $rules[$key] .= request()->id;
            });
        }

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) throw new ValidationException($validator);
    }
}
