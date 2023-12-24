<?php

namespace Miladshm\ControllerHelpers\Http\Requests;

class ChangePositionRequest extends ApiFormRequest
{

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:up,down'],
        ];
    }


}
