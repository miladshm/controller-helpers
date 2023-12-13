<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Model;

trait WithExtraData
{
    protected function extraData(Model $item = null): ?array
    {
        return [];
    }

}
