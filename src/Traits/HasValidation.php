<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Exceptions\ApiValidationException;

trait HasValidation
{

    /**
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    private function getValidationData(Request $request): array
    {

        return Validator::make($request->all(), $this->rules(), $this->messages())
            ->setException(ApiValidationException::class)->validate();
    }


    abstract protected function rules(): array;

    /**
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }

}