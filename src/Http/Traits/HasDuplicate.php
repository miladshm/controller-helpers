<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasDuplicate
{
    use WithModel;


    public function duplicate(int $id)
    {
        $item = $this->model()->query()->findOrFail($id);

        $item = $item->replicate()->fill($this->duplicateChangedValues($item));

        $item->save();

        return ResponderFacade::setData($item)->setMessage(trans('responder::messages.success_duplicate.status'))->respond();
    }


    /**
     * It must be like ["field" => "value"]
     *
     * @param Model $item
     * @return array
     */
    protected function duplicateChangedValues(Model $item): array
    {
        return array();
    }
}