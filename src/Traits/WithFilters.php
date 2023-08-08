<?php

namespace Miladshm\ControllerHelpers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait WithFilters
{
    protected function filters(Builder|QueryBuilder $builder): null|Builder|QueryBuilder
    {
        return null;
    }
}