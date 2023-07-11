<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasModel
{
    abstract private function model(): Model;

}