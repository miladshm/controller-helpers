<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithFilters;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithValidation;

trait HasExists
{
    use WithModel, WithFilters, WithValidation;

    public function exists(Request $request)
    {
        $this->getValidationData($request);
        $exists = $this->model()->query()
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            })
            ->where($request->string('column')->value(), $request->input('value', 'id'))
            ->exists();

        return ResponderFacade::setData(compact('exists'))->respond();
    }

    protected function rules(): array
    {
        return [
            'value' => ['required'],
            'column' => ['nullable', 'string']
        ];
    }


}