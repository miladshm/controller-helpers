<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Foundation\Http\FormRequest;

trait WithRequestClass
{

    abstract private function requestClass(): FormRequest;

    protected function updateRequestClass(): ?FormRequest
    {
        return null;
    }

    private function getRequestClass()
    {
        return $this->updateRequestClass() ?? $this->requestClass();
    }
}
