<?php

namespace Miladshm\ControllerHelpers\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Miladshm\ControllerHelpers\Exceptions\ApiValidationException;

class DuplicateRequest extends FormRequest
{
    public function wantsJson()
    {
        return true;
    }

    public function expectsJson()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable'],
            'status' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer'],
            'name' => ['nullable', 'string'],
            'parent_id' => ['integer', 'exists:parent_models,id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ApiValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }


}
