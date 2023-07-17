<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Foundation\Http\FormRequest;

trait WithRequestClass
{

    abstract private function requestClass(): FormRequest;

}