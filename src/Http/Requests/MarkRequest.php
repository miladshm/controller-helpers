<?php

namespace Miladshm\ControllerHelpers\Http\Requests;

class MarkRequest extends ApiFormRequest
{

    public function rules(): array
    {
        return [
            'time' => ['date'],
        ];
    }


}
