<?php

namespace Miladshm\ControllerHelpers\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Miladshm\ControllerHelpers\Exceptions\ApiValidationException;

class StoreRequest extends FormRequest
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
            'code' => ['required'],
            'status' => ['nullable', 'boolean'],
            'order' => ['required', 'integer'],
            'name' => ['required', 'string'],
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
