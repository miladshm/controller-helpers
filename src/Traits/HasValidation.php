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
        $requestClass = $this->updateRequestClass() ?? $this->requestClass();

        return Validator::make($request->all(), $requestClass->rules(), $requestClass->messages())
            ->setException(ApiValidationException::class)->validate();
    }

    protected function updateRequestClass(): ?FormRequest
    {
        return null;
    }

    abstract private function requestClass(): FormRequest;
}