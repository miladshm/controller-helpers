<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\HasFilters;
use Miladshm\ControllerHelpers\Traits\HasModel;

trait HasExists
{
    use HasModel, HasFilters;

    public function exists(string|int|float $value, ?string $column = 'id')
    {
        $exists = $this->model()->query()
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            })
            ->where($column, $value)
            ->exists();

        return ResponderFacade::setData(compact('exists'))->respond();
    }

}