<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Exceptions\ApiValidationException;

trait WithValidation
{
    use WithRequestClass;

    private ?array $rules;
    private ?array $messages;

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules ?? $this->getRequestClass()->rules();
    }

    /**
     * @param array $rules
     * @return WithValidation
     */
    public function setRules(array $rules): static
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages ?? $this->getRequestClass()->messages();
    }

    /**
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    private function getValidationData(Request $request): array
    {
        $validator = Validator::make($request->all(), $this->getRules(), $this->getMessages());

        if ($request->expectsJson())
            $validator = $validator->setException(ApiValidationException::class);

        return $validator->validate();
    }

    /**
     * @param array $messages
     * @return WithValidation
     */
    protected function setMessages(array $messages): static
    {
        $this->messages = $messages;
        return $this;
    }

}
