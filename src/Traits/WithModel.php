<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Model;

trait WithModel
{
    abstract private function model(): Model;

}