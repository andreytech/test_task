<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait CustomValidationTrait
{
    public function failedValidation(Validator $validator)
    {
        $field = key($validator->errors()->messages());
        if(is_array($this->input($field))) {
            $error = current($validator->errors()->messages()[0]);
        }else {
            $invalidValue = $this->input($field);
            $error = "Invalid value for {$field}: {$invalidValue}";
        }

        throw new HttpResponseException(response()->json(['message' => $error], 422));
    }
}
