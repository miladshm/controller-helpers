<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait WithFilters
{
    protected function filters(Builder $builder): null|Builder
    {
        return null;
    }
}