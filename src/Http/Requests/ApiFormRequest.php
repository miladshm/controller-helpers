<?php

namespace Miladshm\ControllerHelpers\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Miladshm\ControllerHelpers\Exceptions\ApiValidationException;

class ApiFormRequest extends FormRequest
{
    public function wantsJson()
    {
        return true;
    }

    public function expectsJson()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ApiValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }


}
