<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasDuplicate
{
    use WithModel;

    /**
     * Duplicates the record with the given ID and returns the duplicated record.
     *
     * @param int $id The ID of the record to be duplicated.
     * @return \Illuminate\Http\JsonResponse The response containing the duplicated record.
     */
    public function duplicate(int $id)
    {
        $item = $this->getItem($id);

        $item = $item->replicate()->fill($this->duplicateChangedValues($item));

        $item->save();

        return ResponderFacade::setData($item)->setMessage(trans('responder::messages.success_duplicate.status'))->respond();
    }

    /**
     * This method should return an array of field-value pairs representing the changes to be made to the duplicated record.
     *
     * @param Model $item The original record to be duplicated.
     * @return array An array of field-value pairs representing the changes to be made to the duplicated record.
     */
    protected function duplicateChangedValues(Model $item): array
    {
        return array();
    }
}
