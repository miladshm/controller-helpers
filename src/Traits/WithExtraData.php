<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Model;

trait WithExtraData
{
    abstract private function extraData(Model $item = null): ?array;

}