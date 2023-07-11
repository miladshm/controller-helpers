<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasExtraData
{
    abstract private function extraData(Model $item = null): ?array;

}