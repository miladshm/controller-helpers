<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Foundation\Http\FormRequest;

trait WithRequestClass
{

    private FormRequest $requestClass;

    public function getRequestClass(): FormRequest
    {
        return $this->requestClass ?? $this->updateRequestClass();
    }

    public function setRequestClass(FormRequest $requestClass): static
    {
        $this->requestClass = $requestClass;

        return $this;
    }

    protected function updateRequestClass(): ?FormRequest
    {
        return $this->requestClass();
    }

    abstract private function requestClass(): FormRequest;
}
