<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\HasModel;
use Miladshm\ControllerHelpers\Traits\HasRelations;

trait HasShow
{
    use HasModel, HasRelations;


    public function show($id)
    {
        $item = $this->getModel($id);

        return ResponderFacade::setData($item->toArray())->respond();
    }

    private function getModel($id)
    {
        return $this->model()->query()
            ->when(count($this->relations()), function ($q) {
                $q->with($this->relations());
            })->findOrFail($id);
    }
}
