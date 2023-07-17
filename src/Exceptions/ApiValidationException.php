<?php

namespace Miladshm\ControllerHelpers\Exceptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;

class ApiValidationException extends ValidationException
{
    public $validator;


    public function __construct(Validator $validator)
    {
        parent::__construct($validator);
    }

    public function render()
    {
        // return a json with desired format
        return ResponderFacade::setMessage($this->getMessage())
            ->setData($this->errors())
            ->respondInvalid();
    }


}
