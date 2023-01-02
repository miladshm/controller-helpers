<?php

namespace Miladshm\ControllerHelpers\Exceptions;

use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ApiValidationException extends ValidationException
{
    public $validator;


    public function __construct(Validator $validator) {
        $this->validator = $validator;
//        parent::__construct($validator);
    }

    public function render() {
        // return a json with desired format
        return ResponderFacade::setMessage($this->validator->getMessageBag()->first())
            ->setData($this->validator->errors()->toArray())
            ->setHttpCode($this->status)
            ->respond();
    }


}
