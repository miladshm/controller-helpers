<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\HasFilters;
use Miladshm\ControllerHelpers\Traits\HasModel;

trait HasExists
{
    use HasModel, HasFilters;

    public function exists(Request $request)
    {
        $exists = $this->model()->query()
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            })
            ->where($request->string('column')->value(), $request->input('value', 'id'))
            ->exists();

        return ResponderFacade::setData(compact('exists'))->respond();
    }

}